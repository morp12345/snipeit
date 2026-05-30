<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;

class OrangeHRMAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('orangehrm')->redirect();
    }

    public function callback(): RedirectResponse
    {
        try {
            /** @var \Laravel\Socialite\Two\User $ohrm */
            $ohrm = Socialite::driver('orangehrm')->user();
        } catch (InvalidStateException $e) {
            Log::warning('OrangeHRM OAuth state mismatch: '.$e->getMessage());

            return $this->loginFailed();
        } catch (\Throwable $e) {
            Log::error('OrangeHRM OAuth error: '.$e->getMessage());

            return $this->loginFailed();
        }

        $email    = $ohrm->getEmail();
        $username = $ohrm->getNickname(); // OrangeHRM username

        // OrangeHRM "Admin" role maps to Snipe-IT superuser; everyone else is a normal user
        $isAdmin     = strtolower($ohrm->getRaw()['userRole']['name'] ?? '') === 'admin';
        $permissions = json_encode(['superuser' => $isAdmin ? '1' : '0']);

        // Try to find an existing Snipe-IT user by email or username
        $user = User::query()
            ->whereNull('deleted_at')
            ->where('activated', 1)
            ->where(function ($q) use ($email, $username) {
                if ($email) {
                    $q->where('email', $email);
                }
                $q->orWhere('username', $username);
            })
            ->first();

        // Auto-create the user from OrangeHRM data if they don't exist yet
        if (! $user) {
            Log::info("OrangeHRM: creating new Snipe-IT user for '{$username}' (admin: ".($isAdmin ? 'yes' : 'no').')');

            [$firstName, $lastName] = $this->splitName($ohrm->getName());

            $user = new User([
                'first_name'  => $firstName,
                'last_name'   => $lastName,
                'username'    => $username,
                'email'       => $email,
                'activated'   => 1,
                'password'    => bcrypt(Str::random(40)),
                'permissions' => $permissions,
            ]);

            if (! $user->save()) {
                Log::error("OrangeHRM: failed to create user for '{$username}'", $user->getErrors()->toArray());

                return $this->loginFailed('Could not create your account. Please contact an administrator.');
            }
        } else {
            // Sync admin role on every login so OrangeHRM remains the source of truth
            $user->permissions = $permissions;
            $user->save();
        }

        Log::info("OrangeHRM: logging in user '{$user->username}' (admin: ".($isAdmin ? 'yes' : 'no').')');
        Auth::login($user, true);

        return redirect()->route('home');
    }

    public function logout(Request $request): \Illuminate\Contracts\View\View
    {
        // Clear Snipe-IT session first, then return a view that uses the
        // browser (not a server-side HTTP call) to hit OrangeHRM's logout URL.
        // An <img> request carries the user's OrangeHRM session cookie and
        // triggers session invalidation on the OrangeHRM side, after which
        // JS redirects the user back to the Snipe-IT login page.
        $request->session()->regenerate(true);
        $request->session()->forget('2fa_authed');
        Auth::logout();

        return view('auth.orangehrm-logout', [
            'orangehrmLogoutUrl' => rtrim(config('services.orangehrm.base_url'), '/').'/auth/logout',
            'snipeitLoginUrl'    => route('login'),
        ]);
    }

    private function loginFailed(?string $message = null): RedirectResponse
    {
        return redirect()->route('login')->withErrors([
            'username' => [$message ?? trans('auth/general.orangehrm_login_failed')],
        ]);
    }

    private function splitName(?string $name): array
    {
        if (! $name) {
            return ['', ''];
        }
        $parts = explode(' ', $name, 2);

        return [$parts[0], $parts[1] ?? ''];
    }
}
