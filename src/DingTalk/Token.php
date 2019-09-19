<?php

namespace mradang\LumenDingtalk\DingTalk;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

class Token extends DingTalk {

    public static function access_token() {
        $key = parent::$config['corpid'] . parent::$config['appkey'] . __FUNCTION__;
        $lock_file = sys_get_temp_dir().'/'.md5($key);

        if ($token = Cache::get($key)) {
            return $token;
        }

        $fp = fopen($lock_file, 'a');
        if (flock($fp, \LOCK_EX)) {
            $token = Cache::get($key);
            if (empty($token)) {
                $params = [
                    'appkey' => parent::$config['appkey'],
                    'appsecret' => parent::$config['appsecret'],
                ];
                $ret = parent::request('/gettoken', 'GET', [
                    'verify' => false,
                    'query' => $params,
                ]);
                if ($ret) {
                    $token = $ret['access_token'];
                    Cache::put($key, $token, 7200 / 60 - 2);
                } else {
                    Log::error('[lumen-dingtalk] '.parent::error());
                }
            }
            flock($fp, \LOCK_UN);
        }
        fclose($fp);
        return $token;
    }

    public static function jsapi_ticket() {
        $key = parent::$config['corpid'] . parent::$config['appkey'] . __FUNCTION__;
        $lock_file = sys_get_temp_dir().'/'.md5($key);

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
                    Cache::put($key, $ticket, 7200 / 60 - 2);
                } else {
                    Log::error('[lumen-dingtalk] '.parent::error());
                }
            }
            flock($fp, \LOCK_UN);
        }
        fclose($fp);
        return $ticket;
    }

}