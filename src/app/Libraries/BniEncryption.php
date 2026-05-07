<?php
namespace App\Libraries;

class BniEncryption {

    const TIME_DIFF_LIMIT = 480;

    public static function encrypt(array $json_data, string $cid, string $secret): string {
        return self::doubleEncrypt(strrev(time()) . '.' . json_encode($json_data), $cid, $secret);
    }

    public static function decrypt(string $hashing, string $cid, string $secret) {
        $parsed_string = self::doubleDecrypt($hashing, $cid, $secret);
        list($timestamp, $data) = array_pad(explode('.', $parsed_string, 2), 2, null);

        if (self::tsDiff(strrev($timestamp)) === true) {
            return json_decode($data, true);
        }

        return null;
    }

    private static function doubleEncrypt(string $string, string $cid, string $secret): string {
        $result = '';
        $result = self::opensslEncrypt($string, $cid);
        $result = self::opensslEncrypt($result, $secret);
        return rtrim(strtr(base64_encode($result), '+/', '-_'), '=');
    }

    private static function doubleDecrypt(string $string, string $cid, string $secret): string {
        $result = base64_decode(str_pad(strtr($string, '-_', '+/'), strlen($string) % 4, '=', STR_PAD_RIGHT));
        $result = self::opensslDecrypt($result, $secret);
        $result = self::opensslDecrypt($result, $cid);
        return $result;
    }

    private static function opensslEncrypt(string $string, string $key): string {
        $iv = substr(hash('sha256', $key), 0, 16);
        $key = substr(hash('sha256', $key), 0, 32);
        return openssl_encrypt($string, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
    }

    private static function opensslDecrypt(string $string, string $key): string {
        $iv = substr(hash('sha256', $key), 0, 16);
        $key = substr(hash('sha256', $key), 0, 32);
        return openssl_decrypt($string, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
    }

    private static function tsDiff($ts): bool {
        return (abs($ts - time()) <= self::TIME_DIFF_LIMIT);
    }
}
