<?php

namespace mradang\LaravelDingtalk;

use Illuminate\Support\ServiceProvider;
use mradang\LaravelDingtalk\Controllers\DingTalkController;

class LaravelDingtalkServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/dingtalk.php',
            'dingtalk'
        );

        // 初始化钉钉 SDK
        \mradang\LaravelDingtalk\DingTalk\DingTalk::init([
            'corpid' => config('dingtalk.corpid'),
            'agentid' => config('dingtalk.agentid'),
            'appkey' => config('dingtalk.appkey'),
            'appsecret' => config('dingtalk.appsecret'),
            'aes_key' => config('dingtalk.aes_key'),
            'token' => config('dingtalk.token'),
            'proxy' => config('dingtalk.proxy'),
        ]);
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/dingtalk.php' => config_path('dingtalk.php'),
            ]);
        }

        $this->app->router->group([
            'prefix' => 'api/dingtalk',
            'namespace' => 'mradang\LaravelDingtalk\Controllers',
        ], function ($router) {
            $router->post('config', [DingTalkController::class, 'config']);
            $router->post('callback', [DingTalkController::class, 'callback']);
        });
    }
}
