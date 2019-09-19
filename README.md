# Laravel Dingtalk

封装钉钉接口，整合自建钉钉通讯录同步接口，触发员工和部门数据变更事件

## 依赖
- guzzlehttp/guzzle

## 安装
```
composer require mradang/laravel-dingtalk
```

## 配置
1. 添加 .env 环境变量，使用默认值时可省略
```
DINGTALK_CORPID=dingxxxxxxx
DINGTALK_AGENTID=xxxxxxxx
DINGTALK_APPKEY=xxxxxxxx
DINGTALK_APPSECRET=xxxxxxxx
DINGTALK_ALLOW_SITE=http://xx.xx.com/|http://localhost:8080/

# 通讯录同步接口
SYNC_CORP_ID=xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
SYNC_CORP_SECRET=xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
SYNC_HOST=http://xxx.xxx.xxx
```

2. 手动添加钉钉通讯录同步任务

修改 laravel 工程 app\Console\Kernel.php 文件，在 schedule 函数中增加
```php
// 每分钟同步钉钉通讯录数据
try {
    $schedule
    ->call(function () {
        \mradang\LaravelDingtalk\Services\DingTalkService::sync();
    })
    ->everyMinute()
    ->name('DingTalkService::sync')
    ->withoutOverlapping(10);
} catch (\Exception $e) {
    L(sprintf('Kernel.schedule 同步钉钉数据失败：%s', $e->getMessage()), 'sys');
}
```

## 添加的路由
- post /api/dingtalk/config

## 添加的事件
- mradang\LaravelDingtalk\Events\UserUpdateEvent
> array $user
- mradang\LaravelDingtalk\Events\UserDeleteEvent
> string $userid
- mradang\LaravelDingtalk\Events\DepartmentUpdateEvent
> array $dept
- mradang\LaravelDingtalk\Events\DepartmentDeleteEvent
> string $deptid

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
