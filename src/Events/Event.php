<?php

namespace mradang\LumenDingtalk\Events;

use Illuminate\Queue\SerializesModels;

abstract class Event
{
    use SerializesModels;
}
