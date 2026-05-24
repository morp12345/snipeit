<?php

namespace App\Services;

use Defuse\Crypto\Crypto;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\User;

class OrangeHRMProvider extends AbstractProvider
{
    protected $scopes = [];

    protected $scopeSeparator = ' ';

    private function baseUrl(): string
    {
        return rtrim(config('services.orangehrm.base_url'), '/');
    }

    protected function getAuthUrl($state): string
    {
        return $this->buildAuthUrlFromBase($this->baseUrl().'/oauth2/authorize', $state);
    }

    protected function getTokenUrl(): string
    {
        return $this->baseUrl().'/oauth2/token';
    }

    protected function getUserByToken($token): array
    {
        // OrangeHRM exposes no userinfo endpoint and its access tokens are opaque
        // (Defuse-encrypted token identifiers, not JWTs). We decrypt the token to
        // recover the token identifier, then resolve the user via the OrangeHRM DB.
        $userId = $this->userIdFromToken($token);
        if (! $userId) {
            return [];
        }

        $record = DB::connection('orangehrm')
            ->table('ohrm_user as u')
            ->join('ohrm_user_role as ur', 'u.user_role_id', '=', 'ur.id')
            ->leftJoin('hs_hr_employee as e', 'u.emp_number', '=', 'e.emp_number')
            ->where('u.id', $userId)
            ->select(
                'u.id',
                'u.user_name',
                'ur.name as role',
                'e.emp_firstname',
                'e.emp_lastname',
                'e.emp_work_email'
            )
            ->first();

        if (! $record) {
            return [];
        }

        return [
            'id'        => $record->id,
            'userName'  => $record->user_name,
            'userRole'  => ['name' => $record->role],
            'employee'  => [
                'firstName' => $record->emp_firstname,
                'lastName'  => $record->emp_lastname,
            ],
            'workEmail' => $record->emp_work_email,
        ];
    }

    private function userIdFromToken(string $token): ?int
    {
        // Decrypt the opaque bearer token to recover the OrangeHRM token identifier,
        // then map it to the OrangeHRM user id via the oauth2 access-token table.
        try {
            $tokenId = Crypto::decryptWithPassword($token, config('services.orangehrm.token_encryption_key'));
        } catch (\Throwable) {
            return null;
        }

        $userId = DB::connection('orangehrm')
            ->table('ohrm_oauth2_access_token')
            ->where('access_token', $tokenId)
            ->where('revoked', 0)
            ->value('user_id');

        return $userId !== null ? (int) $userId : null;
    }

    protected function mapUserToObject(array $user): User
    {
        $employee = $user['employee'] ?? [];

        return (new User)->setRaw($user)->map([
            'id'       => $user['id'] ?? null,
            'nickname' => $user['userName'] ?? null,
            'name'     => trim(($employee['firstName'] ?? '').' '.($employee['lastName'] ?? '')),
            'email'    => $user['workEmail'] ?? null,
            'avatar'   => null,
            'role'     => $user['userRole']['name'] ?? null,
        ]);
    }
}
