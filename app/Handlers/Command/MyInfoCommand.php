<?php

namespace App\Handlers\Command;

use App\Models\Subscriber;
use WeStacks\TeleBot\Handlers\CommandHandler;

class MyInfoCommand extends BaseCommand
{
    public static $aliases = [ '/myInfo'];
    protected static $description = 'Инфо обо мне';

    public function handle()
    {
        parent::handle();

        $this->sendMessage([
            'text' => 'ФИО: '.$this->sub->fio."\r\n".
                'Команда: '.$this->sub->team->name."\r\n".
                'Должность: '.$this->sub->position->name."\r\n"
        ]);
        return true;
    }
}
