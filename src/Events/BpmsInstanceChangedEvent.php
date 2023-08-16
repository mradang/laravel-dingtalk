<?php

namespace mradang\LaravelDingTalk\Events;

class BpmsInstanceChangedEvent extends Event
{
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(public array $eventMsg)
    {
    }
}
