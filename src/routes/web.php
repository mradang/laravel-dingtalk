<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'dingtalk',
], function () {
    Route::post('config', 'DingTalkController@config');
});
