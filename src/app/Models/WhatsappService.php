<?php
namespace App\Models;
use App\Models\AppConfig;

class WhatsappService {
    
    public static function send($target, $message) {
        $token = AppConfig::get('wa_api_token');
        $url = AppConfig::get('wa_api_url');

        if (empty($token) || empty($url)) {
            return false; // Belum dikonfigurasi
        }

        // Contoh Implementasi Generik (Fonnte/Wablas/WooWA biasanya mirip)
        // Sesuaikan payload ini dengan vendor yang Anda pakai nanti
        $data = [
            'target' => $target,
            'message' => $message,
            'token' => $token
        ];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTPHEADER, ["Authorization: $token"]);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        
        $result = curl_exec($curl);
        curl_close($curl);

        return $result;
    }
}
