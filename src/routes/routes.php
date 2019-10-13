<?php

Route::group([
    'prefix' => config('dingtalk.uri'),
    'namespace' => 'mradang\LaravelDingtalk\Controllers',
], function () {
    Route::post('config', 'DingTalkController@config');
});
