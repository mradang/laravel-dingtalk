<?php

namespace mradang\LumenDingtalk\Events;

class DepartmentUpdateEvent extends Event
{
    public $dept;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(array $dept)
    {
        $this->dept = $dept;
    }
}
