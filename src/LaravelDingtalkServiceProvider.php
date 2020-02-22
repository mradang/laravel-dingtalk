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

        // 初始化钉钉 SDK
        \mradang\LaravelDingtalk\DingTalk\DingTalk::init([
            'corpid' => config('dingtalk.corpid'),
            'agentid' => config('dingtalk.agentid'),
            'appkey' => config('dingtalk.appkey'),
            'appsecret' => config('dingtalk.appsecret'),
        ]);
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/dingtalk.php' => config_path('dingtalk.php'),
            ]);
        }

        $this->app->router->group(['prefix' => 'api/dingtalk'], function ($router) {
            $router->post('config', 'DingTalkController@config');
        });
    }
}
