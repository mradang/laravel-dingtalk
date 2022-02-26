<?php

namespace mradang\LaravelDingtalk\Services;

use mradang\LaravelDingTalk\DingTalk;

class DingTalkService
{
    public static function config(string $url, array $jsApiList = [])
    {
        $nonceStr = uniqid();
        $timestamp = time();
        $config = [
            'agentId' => config('dingtalk.agentid'),
            'corpId' => config('dingtalk.corpid'),
            'timeStamp' => $timestamp,
            'nonceStr' => $nonceStr,
        ];
        $config['signature'] = self::sign($nonceStr, $timestamp, $url);
        $config['jsApiList'] = $jsApiList;
        return json_encode($config);
    }

    private static function sign($noncestr, $timestamp, $url)
    {
        $signArr = [
            'jsapi_ticket' => DingTalk::getJsapiTicket(),
            'noncestr' => $noncestr,
            'timestamp' => $timestamp,
            'url' => $url,
        ];
        ksort($signArr);
        $signStr = urldecode(http_build_query($signArr));
        return sha1($signStr);
    }
}
