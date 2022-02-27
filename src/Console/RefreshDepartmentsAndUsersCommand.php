<?php

namespace mradang\LaravelDingTalk\Console;

use Illuminate\Console\Command;
use mradang\LaravelDingTalk\Events\DepartmentModifyEvent;
use mradang\LaravelDingTalk\Events\UserModifyEvent;
use mradang\LaravelDingTalk\Services\DingTalkService;

class RefreshDepartmentsAndUsersCommand extends Command
{
    protected $signature = 'dingtalk:RefreshDepartmentsAndUsers';

    protected $description = 'Refresh departments and users';

    public function handle()
    {
        $dept_ids = DingTalkService::departmentIds();
        $count = count($dept_ids);

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        foreach ($dept_ids as $dept_id) {
            event(new DepartmentModifyEvent("{$dept_id}"));

            $user_ids = DingTalkService::getDeptUserIds($dept_id);
            foreach ($user_ids as $user_id) {
                event(new UserModifyEvent($user_id));
            }

            $bar->advance();
        }

        $bar->finish();
    }
}
