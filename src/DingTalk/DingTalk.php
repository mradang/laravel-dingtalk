<?php

namespace mradang\LumenDingtalk\DingTalk;

use GuzzleHttp\Client;

class DingTalk {

    protected static $baseUrl = 'https://oapi.dingtalk.com/';

    protected static $error = null;

    protected static $config = [
        'corpid' => '',
        'agentid' => '',
        'appkey' => '',
        'appsecret' => '',
    ];

    private function __construct() {
        // 禁止实例化
    }

    final public static function init($config = []) {
        self::$config = array_merge(self::$config, $config);
    }

    final public static function error($msg = null) {
        if (!is_null($msg)) {
            self::$error = $msg;
        } else {
            return self::$error;
        }
    }

    final public static function request(string $url, string $method, array $params) {
        $client = new Client([
            'base_uri' => self::$baseUrl,
        ]);

        $res = $client->request($method, $url, $params);
        $result = $res->getBody();

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

    final public static function get(string $api, array $params = []) {
        $params['access_token'] = Token::access_token();

        $requestParams = [
            'verify' => false,
            'query' => $params,
        ];

        return self::request($api, 'GET', $requestParams);
    }

    final public static function post(string $api, array $params = []) {
        $requestParams = [
            'verify' => false,
            'query' => [
                'access_token' => Token::access_token()
            ],
            'json' => $params ?: null,
        ];

        return self::request($api, 'POST', $requestParams);
    }

}
