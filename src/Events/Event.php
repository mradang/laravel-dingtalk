<?php

namespace mradang\LaravelDingTalk\Events;

use Illuminate\Queue\SerializesModels;

abstract class Event
{
    use SerializesModels;
}
