<?php

namespace mradang\LaravelDingtalk\Services;

use mradang\LaravelDingtalk\DingTalk\Client as DingTalkAppClient;

class DingTalkService
{
    public static function config($url, array $jsApis)
    {
        $allow_sites = explode('|', config('dingtalk.sites'));
        $base_url = explode('?', $url)[0];
        if (in_array($base_url, $allow_sites)) {
            return DingTalkAppClient::config($url, $jsApis);
        }
    }
}
