<?php

namespace mradang\LumenDingtalk\Events;

class UserUpdateEvent extends Event
{
    public $user;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(array $user)
    {
        $this->user = $user;
    }
}
