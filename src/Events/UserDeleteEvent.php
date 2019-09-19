<?php

namespace mradang\LumenDingtalk\Events;

class UserDeleteEvent extends Event
{
    public $userid;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($userid)
    {
        $this->userid = $userid;
    }
}
