<?php

namespace App\Helper;

class Common
{
    /**
     * @param $value
     * @return string
     */
    public static function encrypt($value): string
    {

        $encryptKey = base64_decode('bRuD5WYw5wd0rdHR9yLlM6wt2vteuiniQBqE70nAuhU=');
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $encryped = openssl_encrypt($value, 'aes-256-cbc', $encryptKey, 0, $iv);

        return base64_encode($encryped . '::' . $iv);
    }

    /**
     * @param $value
     * @return false|string
     */
    public static function decrypt($value): bool|string
    {

        $encryptKey = base64_decode('bRuD5WYw5wd0rdHR9yLlM6wt2vteuiniQBqE70nAuhU=');
        list($encryptedData, $iv) = explode('::', base64_decode($value), 2);

        return openssl_decrypt($encryptedData, 'aes-256-cbc', $encryptKey, 0, $iv);
    }

    /**
     * @param $length
     * @return string
     */
    public static function random($length): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ~!@#$%^&*()-_+=';
        $charactersLength = strlen($characters);
        $randomString = '';

        for($i=0;$i<$length;$i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    /**
     * @param $value
     * @return array|mixed|string|string[]
     */
    public static function setEmoji($value): mixed
    {
        $emoji = array(';-)'=>'&#128521;',';)'=>'&#128521;',':-)'=>'&#128578;',':)'=>'&#128578;');

        foreach($emoji as $key => $val) {
            if(str_contains($value, $key)) {
                $value = str_replace($key, $val, $value);
            }
        }

        return $value;
    }

    /**
     * @param $birth
     * @return string
     */
    public static function getAge($birth): string
    {
        $yearInSec = 31536000;
        $age = time() - $birth;

        return floor($age / $yearInSec);
    }

    /**
     * @param string $number
     * @param string $lang
     * @return string
     */
    public static function numberFormat(string $number, string $lang): string
    {
        if ($lang === 'ned') {
            return number_format($number, 2, ',', '.');
        } else {
            return number_format($number, 2, '.', ',');
        }
    }
}