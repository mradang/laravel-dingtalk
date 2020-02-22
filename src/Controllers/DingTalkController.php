<?php

namespace mradang\LaravelDingtalk\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use mradang\LaravelDingtalk\DingTalk\Client as DingTalkClient;

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
}
