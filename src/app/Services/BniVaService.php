<?php
namespace App\Services;

use App\Libraries\BniEncryption;
use App\Core\Database;

class BniVaService {

    private $hostUrl;
    private $clientId;
    private $secretKey;
    private $prefix;

    public function __construct($unit = 'ALIYAH') {
        $this->hostUrl = getenv('VA_HOST_URL') ?: 'https://api.bni-ecollection.com/';

        switch (strtoupper($unit)) {
            case 'TSANAWIYAH':
            case 'MTS':
                $this->clientId = getenv('VA_CLIENT_ID_TSANAWIYAH');
                $this->secretKey = getenv('VA_SECRET_KEY_TSANAWIYAH');
                $this->prefix = getenv('VA_PREFIX_TSANAWIYAH');
                break;
            case 'MAHAD_ALY':
            case 'PDF':
                $this->clientId = getenv('VA_CLIENT_ID_MAHAD_ALY');
                $this->secretKey = getenv('VA_SECRET_KEY_MAHAD_ALY');
                $this->prefix = getenv('VA_PREFIX_MAHAD_ALY');
                break;
            case 'ALIYAH':
            case 'MA':
            default:
                $this->clientId = getenv('VA_CLIENT_ID_ALIYAH');
                $this->secretKey = getenv('VA_SECRET_KEY_ALIYAH');
                $this->prefix = getenv('VA_PREFIX_ALIYAH');
                break;
        }

        if (empty($this->clientId) || empty($this->secretKey)) {
            throw new \Exception("BNI Configuration missing for unit: {$unit}");
        }
    }

    public function getPrefix() { return $this->prefix; }
    public function getClientId() { return $this->clientId; }
    public function getSecretKey() { return $this->secretKey; }

    public function createVaBilling(array $data) {
        $data['type'] = 'createbilling';
        return $this->sendRequest($data);
    }

    public function createPSBFeeBilling(array $data) {
        $data['type'] = 'createbilling';
        // You could add specific defaults for PSB here
        return $this->sendRequest($data);
    }

    public function inquiry(array $data) {
        $data['type'] = 'inquirybilling';
        return $this->sendRequest($data);
    }

    public function updateBilling(array $data) {
        $data['type'] = 'updatebilling';
        return $this->sendRequest($data);
    }

    public function callback(string $encryptedData) {
        try {
            $decrypted = BniEncryption::decrypt($encryptedData, $this->clientId, $this->secretKey);
            if (!$decrypted) {
                throw new \Exception("Failed to decrypt callback payload");
            }
            return $decrypted;
        } catch (\Exception $e) {
            $this->logRequest('callback', ['payload' => $encryptedData], ['error' => $e->getMessage()], 'FAILED');
            throw $e;
        }
    }

    private function sendRequest(array $data) {
        $data['client_id'] = $this->clientId;

        $encryptedData = BniEncryption::encrypt($data, $this->clientId, $this->secretKey);

        $payload = json_encode([
            'client_id' => $this->clientId,
            'prefix' => $this->prefix,
            'data' => $encryptedData
        ]);

        $ch = curl_init($this->hostUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error || $httpCode >= 400) {
            $this->logRequest($data['type'], $data, ['error' => $error, 'code' => $httpCode, 'response' => $response], 'FAILED');
            throw new \Exception("BNI API Error: " . ($error ?: "HTTP $httpCode"));
        }

        $decodedResponse = json_decode($response, true);

        // Handle invalid response format
        if (!isset($decodedResponse['status'])) {
            $this->logRequest($data['type'], $data, $response, 'FAILED');
            throw new \Exception("Invalid BNI API Response Format");
        }

        // BNI specific Status Codes
        // "000" = Success, "102" = Billing exist
        if ($decodedResponse['status'] !== '000' && $decodedResponse['status'] !== '102') {
            $this->logRequest($data['type'], $data, $decodedResponse, 'FAILED');
            throw new \Exception("BNI Error [{$decodedResponse['status']}]: " . ($decodedResponse['message'] ?? 'Unknown Error'));
        }

        $responseData = null;
        if (isset($decodedResponse['data'])) {
            $responseData = BniEncryption::decrypt($decodedResponse['data'], $this->clientId, $this->secretKey);
        }

        $this->logRequest($data['type'], $data, $responseData ?? $decodedResponse, 'SUCCESS');

        return $responseData ?? $decodedResponse;
    }

    private function logRequest(string $action, $request, $response, string $status) {
        try {
            $db = Database::getInstance();
            $db->query("INSERT INTO bni_api_logs (action, request_payload, response_payload, status) VALUES (?, ?, ?, ?)", [
                $action,
                is_string($request) ? $request : json_encode($request),
                is_string($response) ? $response : json_encode($response),
                $status
            ]);
        } catch (\Exception $e) {
            // Silently ignore log errors to prevent blocking transaction
        }
    }
}
