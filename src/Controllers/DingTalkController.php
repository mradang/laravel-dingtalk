<?php

namespace mradang\LaravelDingTalk\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
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

        $allow_sites = explode('|', config('dingtalk.sites'));
        $base_url = explode('?', $request->url)[0];

        if (in_array($base_url, $allow_sites)) {
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
