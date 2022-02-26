<?php

namespace mradang\LaravelDingTalk;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DingTalkManager
{
    protected $app;

    protected $baseUrl = 'https://api.dingtalk.com/';

    protected $config = [];

    public function __construct(Application $app)
    {
        $this->app = $app;

        // 初始化配置
        $this->config = [
            'corpid' => config('dingtalk.corpid'),
            'agentid' => config('dingtalk.agentid'),
            'appkey' => config('dingtalk.appkey'),
            'appsecret' => config('dingtalk.appsecret'),
            'aes_key' => config('dingtalk.aes_key'),
            'token' => config('dingtalk.token'),
            'proxy' => config('dingtalk.proxy'),
        ];
    }

    public function getAccessToken()
    {
        $key = $this->config['corpid'] . __FUNCTION__;
        $token = Cache::get($key, '');

        if ($token) {
            return $token;
        }

        Cache::lock($key, 10)->block(5, function () use ($key, &$token) {
            $token = Cache::get($key);
            if (empty($token)) {
                $res = $this->request('/gettoken', 'get', [
                    'appkey' => $this->config['appkey'],
                    'appsecret' => $this->config['appsecret'],
                ], false);

                $token = $res ? $res['access_token'] : '';
                if ($token) {
                    Cache::put($key, $token, 7200 - 60);
                }
            }
        });

        return $token;
    }

    public function getJsapiTicket()
    {
        $key = $this->config['corpid'] . __FUNCTION__;
        $token = Cache::get($key, '');

        if ($token) {
            return $token;
        }

        Cache::lock($key, 10)->block(5, function () use ($key, &$token) {
            $token = Cache::get($key);
            if (empty($token)) {
                $res = $this->request('/get_jsapi_ticket', 'get');
                $token = $res ? $res['ticket'] : '';
                if ($token) {
                    Cache::put($key, $token, 7200 - 60);
                }
            }
        });

        return $token;
    }

    private function request(string $url, string $method, array $params = [], bool $withToken = true)
    {
        $headers = [];
        if ($withToken) {
            $url .= Str::contains($url, '?') ? '&' : '?';
            $url .= 'access_token=' . $this->getAccessToken();
        }

        $options = [
            'proxy' => $this->config['proxy'],
            'base_uri' => $this->baseUrl,
        ];

        $response = Http::withHeaders($headers)->withOptions($options)->$method($url, $params);
        if ($response->successful() && $response['errcode'] === 0) {
            return $response;
        } else {
            Log::error('[laravel-dingtalk]' . (string)$response);
        }
    }

    public function get(string $api, array $params = [])
    {
        return $this->request($api, 'get', $params);
    }

    public function post(string $api, array $params = [])
    {
        return $this->request($api, 'post', $params);
    }
}