<?php

namespace mradang\LaravelDingTalk\Events;

class DepartmentRemoveEvent extends Event
{
    public $deptid;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(string $deptid)
    {
        $this->deptid = $deptid;
    }
}
