<?php

namespace mradang\LumenDingtalk\Controllers;

use Illuminate\Http\Request;
use mradang\LumenDingtalk\DingTalk\Client as DingTalkClient;

class DingTalkController extends Controller {

    public function config(Request $request) {
        $validatedData = $this->validate($request, [
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
