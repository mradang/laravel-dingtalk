<?php

namespace mradang\LaravelDingTalk;

class DingTalk extends \Illuminate\Support\Facades\Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-dingtalk';
    }
}
