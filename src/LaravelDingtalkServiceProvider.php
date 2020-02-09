<?php

namespace mradang\LaravelDingtalk;

use Illuminate\Support\ServiceProvider;

class LaravelDingtalkServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/dingtalk.php',
            'dingtalk'
        );
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/dingtalk.php' => config_path('dingtalk.php'),
        ]);

        // 初始化钉钉SDK
        \mradang\LaravelDingtalk\DingTalk\DingTalk::init([
            'corpid' => config('dingtalk.corpid'),
            'agentid' => config('dingtalk.agentid'),
            'appkey' => config('dingtalk.appkey'),
            'appsecret' => config('dingtalk.appsecret'),
        ]);

        // 加载路由
        $this->loadRoutesFrom(__DIR__ . '/routes/routes.php');
    }
}
