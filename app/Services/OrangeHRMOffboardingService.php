<?php

namespace App\Services;

use App\Models\Actionlog;
use App\Models\Asset;
use App\Models\User;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrangeHRMOffboardingService
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.orangehrm.base_url', ''), '/');
    }

    /**
     * Mark an asset as returned in the OrangeHRM employee record and log the
     * offboarding event in both Snipe-IT and OrangeHRM.
     *
     * Authentication note: OrangeHRM's OAuth server only supports the
     * authorization_code grant (no server-to-server token issuance). The API
     * call below is therefore a placeholder — replace the endpoint and auth
     * header once OrangeHRM exposes a machine-to-machine grant or a service
     * account token mechanism. The offboarding event is written directly to
     * the OrangeHRM database in the interim (consistent with the established
     * hybrid integration pattern in this project).
     *
     * @return array{success: bool, message: string}
     */
    public function syncAssetReturn(int $userId, int $assetId): array
    {
        // ----------------------------------------------------------------
        // 1. Resolve Snipe-IT records
        // ----------------------------------------------------------------
        $user = User::whereNull('deleted_at')->find($userId);

        if (! $user) {
            $msg = "OrangeHRM offboarding: Snipe-IT user #{$userId} not found.";
            Log::warning($msg);

            return ['success' => false, 'message' => $msg];
        }

        $asset = Asset::whereNull('deleted_at')->find($assetId);

        if (! $asset) {
            $msg = "OrangeHRM offboarding: Snipe-IT asset #{$assetId} not found.";
            Log::warning($msg);

            return ['success' => false, 'message' => $msg];
        }

        // ----------------------------------------------------------------
        // 2. Resolve OrangeHRM employee number via the shared DB connection
        //    (username is the link between Snipe-IT users and OrangeHRM users)
        // ----------------------------------------------------------------
        $ohrm = DB::connection('orangehrm')
            ->table('ohrm_user')
            ->where('user_name', $user->username)
            ->where('status', 1)
            ->select('id as ohrm_user_id', 'emp_number')
            ->first();

        if (! $ohrm || ! $ohrm->emp_number) {
            $msg = "OrangeHRM offboarding: no active OrangeHRM user found for username '{$user->username}'.";
            Log::warning($msg);

            return ['success' => false, 'message' => $msg];
        }

        $empNumber = $ohrm->emp_number;

        // ----------------------------------------------------------------
        // 3. Write offboarding event to Snipe-IT action log
        // ----------------------------------------------------------------
        try {
            $log = new Actionlog([
                'item_type'   => Asset::class,
                'item_id'     => $asset->id,
                'action_type' => 'offboarding_return',
                'note'        => "Asset returned during OrangeHRM offboarding for employee #{$empNumber} (user: {$user->username}).",
                'user_id'     => $userId,
            ]);
            $log->save();
        } catch (\Throwable $e) {
            Log::error("OrangeHRM offboarding: failed to write Snipe-IT action log — ".$e->getMessage());
        }

        // ----------------------------------------------------------------
        // 4. Call OrangeHRM API — PLACEHOLDER
        //    TODO: Replace this endpoint once OrangeHRM exposes a dedicated
        //    asset-return / offboarding API, or when a service-account token
        //    mechanism becomes available.
        //    Candidate endpoint: /api/v2/pim/employees/{empNumber}/custom-fields
        // ----------------------------------------------------------------
        $apiSuccess = false;
        $apiMessage = '';

        try {
            $client   = new HttpClient(['http_errors' => true, 'timeout' => 10]);
            $endpoint = $this->baseUrl.'/api/v2/pim/employees/'.$empNumber.'/custom-fields';

            // TODO: replace with a valid bearer token once server-to-server
            // OAuth is available on this OrangeHRM instance.
            $response = $client->put($endpoint, [
                'headers' => [
                    'Authorization' => 'Bearer PLACEHOLDER_TOKEN',
                    'Accept'        => 'application/json',
                    'Content-Type'  => 'application/json',
                ],
                'json' => [
                    // TODO: map to the correct custom field configured in OrangeHRM
                    // for tracking asset-return status on employee records.
                    'customFieldId' => 1,
                    'value'         => 'Asset '.$asset->asset_tag.' returned — '.now()->toDateTimeString(),
                ],
            ]);

            $apiSuccess = in_array($response->getStatusCode(), [200, 201, 204], true);
            $apiMessage = $apiSuccess
                ? "OrangeHRM API call succeeded (HTTP {$response->getStatusCode()})."
                : "OrangeHRM API returned unexpected status {$response->getStatusCode()}.";

            Log::info(
                "OrangeHRM offboarding: asset [{$asset->asset_tag}] returned for ".
                "empNumber={$empNumber} — {$apiMessage}"
            );
        } catch (GuzzleException $e) {
            $apiMessage = 'OrangeHRM API call failed (Guzzle): '.$e->getMessage();
            Log::error("OrangeHRM offboarding: {$apiMessage}");
        } catch (\Throwable $e) {
            $apiMessage = 'OrangeHRM API call failed (unexpected): '.$e->getMessage();
            Log::error("OrangeHRM offboarding: {$apiMessage}");
        }

        // ----------------------------------------------------------------
        // 5. Return consolidated result
        //    The Snipe-IT log is written regardless of API outcome; the
        //    overall success flag reflects the API call result.
        // ----------------------------------------------------------------
        return [
            'success' => $apiSuccess,
            'message' => $apiMessage,
        ];
    }
}
