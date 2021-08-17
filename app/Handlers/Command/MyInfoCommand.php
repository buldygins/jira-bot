<?php

namespace App\Handlers\Command;

use App\Models\Subscriber;
use WeStacks\TeleBot\Handlers\CommandHandler;

class MyInfoCommand extends BaseCommand
{
    protected static $aliases = [ '/myInfo'];
    protected static $description = 'Инфо обо мне';

    public function handle()
    {
        parent::handle();

        $this->sendMessage([
            'text' => $this->sub->fio
        ]);
        return true;
    }
}
