<?php

namespace mradang\LaravelDingtalk\DingTalk;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;

class Token extends DingTalk
{
    public static function access_token()
    {
        $key = parent::$config['corpid'] . parent::$config['appkey'] . __FUNCTION__;
        $lock_file = sys_get_temp_dir() . '/' . md5($key);

        if ($token = Cache::get($key)) {
            return $token;
        }

        $fp = fopen($lock_file, 'a');
        if (flock($fp, \LOCK_EX)) {
            $token = Cache::get($key);
            if (empty($token)) {
                $client = new Client([
                    'base_uri' => parent::$baseUrl,
                ]);
                $ret = $client->request('GET', '/gettoken', [
                    'query' => [
                        'appkey' => parent::$config['appkey'],
                        'appsecret' => parent::$config['appsecret'],
                    ],
                ]);
                $ret = json_decode($ret->getBody()->getContents(), true);
                if (Arr::get($ret, 'errcode') == 0) {
                    $token = $ret['access_token'];
                    Cache::put($key, $token, 7200 - 60);
                } else {
                    Log::error('[laravel-dingtalk] gettoken fail.');
                }
            }
            flock($fp, \LOCK_UN);
        }
        fclose($fp);
        return $token;
    }

    public static function jsapi_ticket()
    {
        $key = parent::$config['corpid'] . parent::$config['appkey'] . __FUNCTION__;
        $lock_file = sys_get_temp_dir() . '/' . md5($key);

        if ($ticket = Cache::get($key)) {
            return $ticket;
        }

        $fp = fopen($lock_file, 'a');
        if (flock($fp, \LOCK_EX)) {
            $ticket = Cache::get($key);
            if (empty($ticket)) {
                $ret = parent::get('/get_jsapi_ticket');
                if ($ret) {
                    $ticket = $ret['ticket'];
                    Cache::put($key, $ticket, 7200 - 60);
                } else {
                    Log::error('[laravel-dingtalk] ' . parent::error());
                }
            }
            flock($fp, \LOCK_UN);
        }
        fclose($fp);
        return $ticket;
    }
}
