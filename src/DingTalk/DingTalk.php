<?php

namespace mradang\LaravelDingtalk\DingTalk;

use GuzzleHttp\Client;
use Illuminate\Support\Arr;

class DingTalk
{
    protected static $baseUrl = 'https://oapi.dingtalk.com/';

    protected static $error = null;

    protected static $config = [
        'corpid' => '',
        'agentid' => '',
        'appkey' => '',
        'appsecret' => '',
        'aes_key' => '',
        'token' => '',
        'proxy' => '',
    ];

    private function __construct()
    {
        // 禁止实例化
    }

    final public static function init($config = [])
    {
        self::$config = array_merge(self::$config, $config);
    }

    final public static function error($msg = null)
    {
        if (!is_null($msg)) {
            self::$error = $msg;
        } else {
            return self::$error;
        }
    }

    final public static function request(string $url, string $method, array $params)
    {
        $client = new Client([
            'base_uri' => self::$baseUrl,
        ]);

        Arr::set($params, 'query.access_token', Token::access_token());

        if (self::$config['proxy']) {
            $params['proxy'] = self::$config['proxy'];
        }

        $res = $client->request($method, $url, $params);
        $result = $res->getBody()->getContents();

        if ($result) {
            $result = json_decode($result, true);
            if ($result['errcode'] == 0) {
                return $result;
            } else {
                self::error($result['errmsg']);
                return false;
            }
        } else {
            return false;
        }
    }

    final public static function get(string $api, array $params = [])
    {
        $requestParams = [
            'query' => $params,
        ];
        return self::request($api, 'GET', $requestParams);
    }

    final public static function post(string $api, array $params = [])
    {
        $requestParams = [
            'json' => $params ?: null,
        ];
        return self::request($api, 'POST', $requestParams);
    }
}
