<?php

namespace mradang\LumenDingtalk\DingTalk;

use Illuminate\Support\Facades\Cache;

class Crypto extends DingTalk {

    private static function getSignature($timestamp, $nonce, $encrypt_msg) {
        $array = array($encrypt_msg, self::token(), $timestamp, $nonce);
        sort($array, SORT_STRING);
        $str = implode($array);
        return sha1($str);
    }

    public static function getNonceStr($length = 32) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $str = '';
        for ( $i = 0; $i < $length; $i++ ) {
            $str .= substr($chars, mt_rand(0, strlen($chars)-1), 1);
        }
        return $str;
    }

    private static function suiteKey() {
        // return 'suite4xxxxxxxxxxxxxxx';
        return parent::$config['corpid'];
    }

    public static function token () {
        return '123456';
    }

    private static function key() {
        return base64_decode(self::aes_key() . '=');
    }

    public static function aes_key () {
        return substr(md5(gethostname()).md5(__FILE__.__FUNCTION__), 0, 43);
    }

    // 加密
    public static function encryptMsg($plain, $timeStamp, $nonce) {
        $encrypt = self::encrypt($plain, self::suiteKey());
        if ($timeStamp == null) {
            $timeStamp = time();
        }
        $signature = self::getSignature($timeStamp, $nonce, $encrypt);
        return json_encode([
            'msg_signature' => $signature,
            'encrypt' => $encrypt,
            'timeStamp' => $timeStamp,
            'nonce' => $nonce
        ]);
    }

    private static function encrypt($text, $corpid) {
        // 获得16位随机字符串，填充到明文之前
        $random = self::getNonceStr(16);
        $text = $random . pack("N", strlen($text)) . $text . $corpid;
        // 网络字节序
        $iv = substr(self::key(), 0, 16);
        // 使用自定义的填充方式对明文进行补位填充
        $text = self::encode($text);
        return openssl_encrypt($text, 'AES-256-CBC', substr(self::key(), 0, 32), OPENSSL_ZERO_PADDING, $iv);
    }

    private static function encode($text) {
        $text_length = strlen($text);
        $amount_to_pad = 32 - ($text_length % 32);
        if ($amount_to_pad == 0) {
            $amount_to_pad = 32;
        }
        $pad_chr = chr($amount_to_pad);
        $tmp = "";
        for ($index = 0; $index < $amount_to_pad; $index++) {
            $tmp .= $pad_chr;
        }
        return $text . $tmp;
    }

    // 解密
    public static function decryptMsg($signature, $timeStamp, $nonce, $encrypt) {
        if ($timeStamp == null) {
            $timeStamp = time();
        }
        $verifySignature = self::getSignature($timeStamp, $nonce, $encrypt);
        if ($verifySignature != $signature) {
            throw new \Exception('Validate signature');
        }
        return self::decrypt($encrypt, self::suiteKey());
    }

    private static function decrypt($encrypted, $corpid) {
        $iv = substr(self::key(), 0, 16);
        $decrypted = openssl_decrypt($encrypted, 'AES-256-CBC', substr(self::key(), 0, 32), OPENSSL_ZERO_PADDING, $iv);
        try {
            // 去除补位字符
            $result = self::decode($decrypted);
            // 去除16位随机字符串,网络字节序和AppId
            if (strlen($result) < 16)
                return "";
            $content = substr($result, 16, strlen($result));
            $len_list = unpack("N", substr($content, 0, 4));
            $xml_len = $len_list[1];
            $xml_content = substr($content, 4, $xml_len);
            $from_corpid = substr($content, $xml_len + 4);
        } catch (\Exception $e) {
            throw new \Exception('decrypt AES error');
        }
        if ($from_corpid != $corpid) {
            throw new \Exception('Validate SuiteKey error');
        }
        return $xml_content;
    }

    private static function decode($text) {
        $pad = ord(substr($text, -1));
        if ($pad < 1 || $pad > 32) {
            $pad = 0;
        }
        return substr($text, 0, (strlen($text) - $pad));
    }

}
