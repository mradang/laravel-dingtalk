<?php

namespace mradang\LumenDingtalk\DingTalk;

class Client extends DingTalk {

    public static function config(string $url, array $jsApiList = []) {
        $nonceStr = uniqid();
        $timestamp = time();
        $config = [
            'agentId' => parent::$config['agentid'],
            'corpId' => parent::$config['corpid'],
            'timeStamp' => $timestamp,
            'nonceStr' => $nonceStr,
        ];
        $config['signature'] = self::sign($nonceStr, $timestamp, $url);
        $config['jsApiList'] = $jsApiList;
        return json_encode($config);
    }

    private static function sign($noncestr, $timestamp, $url) {
        $signArr = [
            'jsapi_ticket' => Token::jsapi_ticket(),
            'noncestr' => $noncestr,
            'timestamp' => $timestamp,
            'url' => $url,
        ];
        ksort($signArr);
        $signStr = urldecode(http_build_query($signArr));
        return sha1($signStr);
    }

}
