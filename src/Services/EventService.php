<?php

namespace mradang\LaravelDingtalk\Services;

use mradang\LaravelDingtalk\Events\DepartmentCreateEvent;
use mradang\LaravelDingtalk\Events\DepartmentModifyEvent;
use mradang\LaravelDingtalk\Events\DepartmentRemoveEvent;
use mradang\LaravelDingtalk\Events\UserAddEvent;
use mradang\LaravelDingtalk\Events\UserLeaveEvent;
use mradang\LaravelDingtalk\Events\UserModifyEvent;

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
}
