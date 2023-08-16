<?php

namespace mradang\LaravelDingTalk\Services;

use mradang\LaravelDingTalk\Events\BpmsInstanceChangedEvent;
use mradang\LaravelDingTalk\Events\DepartmentCreateEvent;
use mradang\LaravelDingTalk\Events\DepartmentModifyEvent;
use mradang\LaravelDingTalk\Events\DepartmentRemoveEvent;
use mradang\LaravelDingTalk\Events\UserAddEvent;
use mradang\LaravelDingTalk\Events\UserLeaveEvent;
use mradang\LaravelDingTalk\Events\UserModifyEvent;

class EventService
{
    public static function __callStatic($method, $args)
    {
        logger($method . ':' . var_export($args, true));
    }

    // 检查 URL
    public static function check_url($event)
    {
    }

    // 通讯录用户增加
    public static function user_add_org($event)
    {
        $ids = $event['UserId'];
        if (is_array($ids)) {
            foreach ($ids as $id) {
                event(new UserAddEvent($id));
            }
        }
    }

    // 通讯录用户更改
    public static function user_modify_org($event)
    {
        $ids = $event['UserId'];
        if (is_array($ids)) {
            foreach ($ids as $id) {
                event(new UserModifyEvent($id));
            }
        }
    }

    // 通讯录用户离职
    public static function user_leave_org($event)
    {
        $ids = $event['UserId'];
        if (is_array($ids)) {
            foreach ($ids as $id) {
                event(new UserLeaveEvent($id));
            }
        }
    }

    // 通讯录企业部门创建
    public static function org_dept_create($event)
    {
        $ids = $event['DeptId'];
        if (is_array($ids)) {
            foreach ($ids as $id) {
                event(new DepartmentCreateEvent($id));
            }
        }
    }

    // 通讯录企业部门修改
    public static function org_dept_modify($event)
    {
        $ids = $event['DeptId'];
        if (is_array($ids)) {
            foreach ($ids as $id) {
                event(new DepartmentModifyEvent($id));
            }
        }
    }

    // 通讯录企业部门删除
    public static function org_dept_remove($event)
    {
        $ids = $event['DeptId'];
        if (is_array($ids)) {
            foreach ($ids as $id) {
                event(new DepartmentRemoveEvent($id));
            }
        }
    }

    // 审批实例开始、结束
    public static function bpms_instance_change(array $eventMsg)
    {
        event(new BpmsInstanceChangedEvent($eventMsg));
    }
}
