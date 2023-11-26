# Laravel Dingtalk

封装钉钉接口，处理钉钉事件订阅，触发事件

## 安装

```
composer require mradang/laravel-dingtalk
```

## 配置

1. 添加 .env 环境变量

```
DINGTALK_CORPID=dingxxxxxxx
DINGTALK_AGENTID=xxxxxxxx
DINGTALK_APPKEY=xxxxxxxx
DINGTALK_APPSECRET=xxxxxxxx
DINGTALK_ALLOW_HOSTS=xx.xx.com|localhost
DINGTALK_CALLBACK_AES_KEY=xxxxxxxx
DINGTALK_CALLBACK_TOKEN=xxxxxxxx
DINGTALK_PROXY=http://addr:port
```

## 添加的路由

- post /api/dingtalk/config
- post /api/dingtalk/callback

## 添加的事件

- mradang\LaravelDingTalk\Events\DepartmentCreateEvent
  > string $deptid
- mradang\LaravelDingTalk\Events\DepartmentModifyEvent
  > string $deptid
- mradang\LaravelDingTalk\Events\DepartmentRemoveEvent
  > string $deptid
- mradang\LaravelDingTalk\Events\UserAddEvent
  > string $userid
- mradang\LaravelDingTalk\Events\UserLeaveEvent
  > string $userid
- mradang\LaravelDingTalk\Events\UserModifyEvent
  > string $userid

## 添加的命令

1. 刷新部门和用户（触发变更事件）

```bash
php artisan dingtalk:RefreshDepartmentsAndUsers
```

## 钉钉接口调用示例

### 发送工作通知消息

```
请求方式：POST（HTTPS）
请求地址：https://oapi.dingtalk.com/topapi/message/corpconversation/asyncsend_v2?access_token=ACCESS_TOKEN
```

```php
$params = [
    'agent_id' => env('DINGTALK_AGENTID'),
    'userid_list' => '0841582759859766',
    'msg' => [
        'msgtype' => 'text',
        'text' => [
            'content' => '当前时间：'.date('Y-m-d H:i:s'),
        ],
    ],
];

$ret = DingTalk::post('/topapi/message/corpconversation/asyncsend_v2', $params);
```
