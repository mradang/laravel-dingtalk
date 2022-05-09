<?php

namespace mradang\LaravelDingTalk\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use mradang\LaravelDingTalk\DingTalk;
use mradang\LaravelDingTalk\Jobs\SendDingtalkMessageToUsers;

class DingTalkService
{
    public static function config(string $url, array $jsApiList = [])
    {
        $nonceStr = uniqid();
        $timestamp = time();
        $config = [
            'agentId' => config('dingtalk.agentid'),
            'corpId' => config('dingtalk.corpid'),
            'timeStamp' => $timestamp,
            'nonceStr' => $nonceStr,
        ];
        $config['signature'] = self::sign($nonceStr, $timestamp, $url);
        $config['jsApiList'] = $jsApiList;
        return json_encode($config);
    }

    private static function sign($noncestr, $timestamp, $url)
    {
        $signArr = [
            'jsapi_ticket' => DingTalk::getJsapiTicket(),
            'noncestr' => $noncestr,
            'timestamp' => $timestamp,
            'url' => $url,
        ];
        ksort($signArr);
        $signStr = urldecode(http_build_query($signArr));
        return sha1($signStr);
    }

    public static function departmentIds(): array
    {
        return array_merge(self::getDepartmentAllSubIds(1), [1]);
    }

    public static function getDepartmentAllSubIds($dept_id): array
    {
        $sub_ids = Arr::get(
            DingTalk::post('/topapi/v2/department/listsubid', ['dept_id' => $dept_id]),
            'result.dept_id_list',
            []
        );
        foreach ($sub_ids as $id) {
            $sub_ids = array_merge($sub_ids, self::getDepartmentAllSubIds($id));
        }
        return $sub_ids;
    }

    public static function getDeptUserIds($dept_ids): array
    {
        $dept_ids = Arr::wrap($dept_ids);
        $user_ids = [];

        foreach ($dept_ids as $dept_id) {
            $dept_user_ids = Arr::get(
                DingTalk::post('/topapi/user/listid', ['dept_id' => $dept_id]),
                'result.userid_list',
                []
            );
            $user_ids = array_merge($user_ids, $dept_user_ids);
        }

        return collect($user_ids)->unique()->toArray();
    }

    /**
     * 发送消息给钉钉用户
     *
     * @param string|array|Collection $userid
     * @param array $msg
     * @return void
     */
    public static function messageToUser($userid, array $msg)
    {
        if (is_string($userid)) {
            $userid = explode(',', $userid);
        }
        if (is_array($userid)) {
            $userid = collect($userid);
        }
        if ($userid instanceof Collection) {
            $userid = $userid
                ->filter(function ($item) {
                    return !empty($item);
                })
                ->unique();
        }

        if ($userid->count() > 0) {
            $url = '/topapi/message/corpconversation/asyncsend_v2';
            $params = [
                'agent_id' => env('DINGTALK_AGENTID'),
                'userid_list' => $userid->join(','),
                'msg' => $msg,
            ];
            return Arr::get(
                DingTalk::post($url, $params),
                'task_id',
            );
        }
    }
}
