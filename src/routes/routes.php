<?php

Route::group([
    'prefix' => 'api/dingtalk',
    'namespace' => 'mradang\LaravelDingtalk\Controllers',
], function () {
    Route::post('config', 'DingTalkController@config');
});
