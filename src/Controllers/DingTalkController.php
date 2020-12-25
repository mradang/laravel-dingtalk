<?php

namespace mradang\LaravelDingtalk\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use mradang\LaravelDingtalk\DingTalk\Client as DingTalkClient;
use mradang\LaravelDingtalk\DingTalk\Crypto;
use mradang\LaravelDingtalk\Services\EventService;

class DingTalkController extends BaseController
{
    public function config(Request $request)
    {
        $validatedData = $request->validate([
            'url' => 'required|string',
            'jsApiList' => 'required|string',
        ]);

        $jsApiList = explode('|', $request->jsApiList);

        $allow_sites = explode('|', config('dingtalk.sites'));
        $base_url = explode('?', $request->url)[0];
        if (in_array($base_url, $allow_sites)) {
            return DingTalkClient::config($request->url, $jsApiList);
        }
    }

    public function callback(Request $request)
    {
        try {
            $text = Crypto::decryptMsg(
                $request->signature,
                $request->timestamp,
                $request->nonce,
                $request->encrypt
            );

            $eventMsg = json_decode($text, true);
            $eventType = $eventMsg['EventType'];
            EventService::$eventType($eventMsg);

            // 为钉钉服务器返回成功状态
            return Crypto::encryptMsg('success', $request->timestamp, $request->nonce);
        } catch (\Exception $e) {
            logger('钉钉回调消息处理失败：' . $e->getMessage());
        }
    }
}
