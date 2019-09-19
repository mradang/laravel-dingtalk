<?php

namespace mradang\LumenDingtalk\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

use mradang\LumenDingtalk\DingTalk\Client as DingTalkAppClient;

class DingTalkService {

    public static function config($url, array $jsApis) {
        $allow_sites = explode('|', config('dingtalk.sites'));
        $base_url = explode('?', $url)[0];
        if (in_array($base_url, $allow_sites)) {
            return DingTalkAppClient::config($url, $jsApis);
        }
    }

    public static function setLastSyncTimestamp($timestamp) {
        $cache_name = config('dingtalk.sync_timestamp_cache_name');
        Cache::forever($cache_name, $timestamp);
    }

    public static function sync() {
        $cache_name = config('dingtalk.sync_timestamp_cache_name');
        $last_timestamp = Cache::get($cache_name, 0);

        $changes = self::getChangeRecord($last_timestamp);
        if (empty($changes)) {
            Log::info('未获取到通讯录变更数据');
            return;
        }

        foreach ($changes as $row) {
            $last_timestamp = $row['timestamp'];
            if ($row['biz_type'] === 13) { // 员工
                if ($row['biz_data']) {
                    $data = json_decode($row['biz_data'], true);
                    event(new \mradang\LumenDingtalk\Events\UserUpdateEvent($data));
                } else {
                    event(new \mradang\LumenDingtalk\Events\UserDeleteEvent($row['biz_id']));
                }
            } elseif ($row['biz_type'] === 14) { // 部门
                if ($row['biz_data']) {
                    $data = json_decode($row['biz_data'], true);
                    event(new \mradang\LumenDingtalk\Events\DepartmentUpdateEvent($data));
                } else {
                    event(new \mradang\LumenDingtalk\Events\DepartmentDeleteEvent($row['biz_id']));
                }
            }
        }

        Cache::forever($cache_name, $last_timestamp);
    }

    public static function getChangeRecord($last_timestamp) {
        if ($token = self::getToken()) {
            $url = config('dingtalk.sync_host') . '/api/changeRecord';

            $ret = self::postJson($url, [
                'access_token' => $token,
                'timestamp' => $last_timestamp,
            ]);

            if (array_get($ret, 'errcode')) {
                Log::error(sprintf(
                    '获取通讯录变更记录失败：[%s]%s',
                    array_get($ret, 'errcode'),
                    array_get($ret, 'errmsg')
                ));
            } else {
                return array_get($ret, 'result');
            }
        } else {
            return false;
        }
    }

    private static function getToken() {
        $key = __CLASS__.'sync_token';

        if ($token = Cache::get($key)) {
            return $token;
        }

        if ($token = self::getTokenFromAPI()) {
            Cache::put($key, $token, 7200/60 - 3);
            return $token;
        } else {
            return false;
        }
    }

    private static function getTokenFromAPI() {
        $url = config('dingtalk.sync_host') . '/api/gettoken';

        $ret = self::postJson($url, [
            'corp_id' => config('dingtalk.sync_id'),
            'corp_secret' => config('dingtalk.sync_secret'),
        ]);

        if (array_get($ret, 'errcode')) {
            Log::error(sprintf(
                '获取通讯录同步令牌失败：[%s]%s',
                array_get($ret, 'errcode'),
                array_get($ret, 'errmsg')
            ));
            return false;
        } else {
            return array_get($ret, 'result');
        }
    }

    private static function postJson($url, $params) {
        $client = new Client();
        $res = $client->request('POST', $url, [
            'verify' => false,
            'json' => $params,
        ]);

        $body = $res->getBody()->getContents();
        $ret = json_decode($body, true);
        return $ret;
    }

}
