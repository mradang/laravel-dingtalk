<?php

namespace mradang\LumenDingtalk;

use Illuminate\Support\ServiceProvider;

class LumenDingtalkServiceProvider extends ServiceProvider {

    public function boot() {
        $this->configure();
        $this->registerRoutes();
        if (!class_exists('DingTalk')) {
            class_alias('mradang\LumenDingtalk\DingTalk\DingTalk', 'DingTalk');
        }
    }

    protected function configure() {
        $this->app->configure('dingtalk');

        $this->mergeConfigFrom(
            __DIR__.'/../config/dingtalk.php', 'dingtalk'
        );

        // 初始化钉钉SDK
        \mradang\LumenDingtalk\DingTalk\DingTalk::init([
            'corpid' => config('dingtalk.corpid'),
            'agentid' => config('dingtalk.agentid'),
            'appkey' => config('dingtalk.appkey'),
            'appsecret' => config('dingtalk.appsecret'),
        ]);
    }

    protected function registerRoutes() {
        \Illuminate\Support\Facades\Route::group([
            'namespace' => 'mradang\LumenDingtalk\Controllers',
        ], function () {
            $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        });
    }

}