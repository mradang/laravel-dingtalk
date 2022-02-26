<?php

namespace mradang\LaravelDingTalk;

use Illuminate\Support\ServiceProvider;
use mradang\LaravelDingTalk\Controllers\DingTalkController;

class LaravelDingTalkServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/dingtalk.php' => config_path('dingtalk.php'),
            ]);
        }

        $this->app->router->group([
            'prefix' => 'api/dingtalk',
            'namespace' => 'mradang\LaravelDingTalk\Controllers',
        ], function ($router) {
            $router->post('config', [DingTalkController::class, 'config']);
            $router->post('callback', [DingTalkController::class, 'callback']);
        });
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/dingtalk.php',
            'dingtalk'
        );

        $this->app->singleton('laravel-dingtalk', function ($app) {
            return new DingTalkManager($app);
        });
    }
}
