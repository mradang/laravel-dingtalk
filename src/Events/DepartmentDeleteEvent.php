<?php

namespace mradang\LaravelDingtalk\Events;

class DepartmentDeleteEvent extends Event
{
    public $deptid;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($deptid)
    {
        $this->deptid = $deptid;
    }
}
