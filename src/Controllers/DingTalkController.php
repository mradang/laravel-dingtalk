<?php

namespace mradang\LaravelDingTalk\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use mradang\LaravelDingTalk\Services\CryptoService;
use mradang\LaravelDingTalk\Services\DingTalkService;
use mradang\LaravelDingTalk\Services\EventService;

class DingTalkController extends BaseController
{
    public function config(Request $request)
    {
        $request->validate([
            'url' => 'required|string',
            'jsApiList' => 'required|string',
        ]);

        $jsApiList = explode('|', $request->jsApiList);

        $hosts = explode('|', config('dingtalk.hosts'));
        $host = parse_url($request->url, PHP_URL_HOST);

        if (in_array($host, $hosts)) {
            return DingTalkService::config($request->url, $jsApiList);
        }
    }

    public function callback(Request $request)
    {
        try {
            $text = CryptoService::decryptMsg(
                $request->signature,
                $request->timestamp,
                $request->nonce,
                $request->encrypt
            );

            $eventMsg = json_decode($text, true);
            $eventType = $eventMsg['EventType'];
            EventService::$eventType($eventMsg);

            // 为钉钉服务器返回成功状态
            return CryptoService::encryptMsg('success', $request->timestamp, $request->nonce);
        } catch (\Exception $e) {
            logger('钉钉回调消息处理失败：' . $e->getMessage());
        }
    }
}
