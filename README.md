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
DINGTALK_ALLOW_SITE=http://xx.xx.com/|http://localhost:8080/
DINGTALK_CALLBACK_AES_KEY=xxxxxxxx
DINGTALK_CALLBACK_TOKEN=xxxxxxxx
```

## 添加的路由
- post /api/dingtalk/config
- post /api/dingtalk/callback

## 添加的事件
- mradang\LaravelDingtalk\Events\DepartmentCreateEvent
> string $deptid
- mradang\LaravelDingtalk\Events\DepartmentModifyEvent
> string $deptid
- mradang\LaravelDingtalk\Events\DepartmentRemoveEvent
> string $deptid
- mradang\LaravelDingtalk\Events\UserAddEvent
> string $userid
- mradang\LaravelDingtalk\Events\UserLeaveEvent
> string $userid
- mradang\LaravelDingtalk\Events\UserModifyEvent
> string $userid

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

$ret = \DingTalk::post('/topapi/message/corpconversation/asyncsend_v2', $params);
```
