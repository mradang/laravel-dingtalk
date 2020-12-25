<?php

namespace mradang\LaravelDingtalk\Events;

class UserModifyEvent extends Event
{
    public $userid;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(string $userid)
    {
        $this->userid = $userid;
    }
}
