<?php
namespace App\Models;

class WhatsappService {

    private static function serviceUrl(): string {
        return rtrim(getenv('WA_SERVICE_URL') ?: 'http://wa_service:3000', '/');
    }

    private static function apiKey(): string {
        return getenv('WA_SERVICE_KEY') ?: 'siakad-wa-secret';
    }

    private static function request(string $method, string $path, array $body = []): array {
        $curl = curl_init(self::serviceUrl() . $path);
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json', 'x-api-key: ' . self::apiKey()],
        ]);
        if ($method === 'POST') {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($body));
        }
        $res  = curl_exec($curl);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $data = json_decode($res ?: '{}', true) ?? [];
        $data['_http_code'] = $code;
        return $data;
    }

    /** Ambil status koneksi & QR code (base64 data URL) */
    public static function getStatus(): array {
        return self::request('GET', '/status');
    }

    /** Kirim pesan ke satu nomor. Nomor boleh format 08xx atau 628xx */
    public static function send(string $number, string $message): array {
        $number = preg_replace('/\D/', '', $number);
        if (str_starts_with($number, '0')) $number = '62' . substr($number, 1);
        return self::request('POST', '/send', ['number' => $number, 'message' => $message]);
    }

    /** Kirim ke banyak nomor sekaligus. Kembalikan ringkasan sukses/gagal */
    public static function blast(array $numbers, string $message): array {
        $success = 0; $failed = 0;
        foreach ($numbers as $num) {
            $res = self::send($num, $message);
            isset($res['success']) && $res['success'] ? $success++ : $failed++;
        }
        return ['success' => $success, 'failed' => $failed];
    }

    /** Logout / disconnect WhatsApp */
    public static function logout(): array {
        return self::request('POST', '/logout');
    }
}
