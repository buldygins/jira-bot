<?php

namespace App\Handlers\Command;

use App\Models\Subscriber;
use WeStacks\TeleBot\Handlers\CommandHandler;

class ListCommand extends CommandHandler
{
    protected static $aliases = [ '/list'];
    protected static $description = 'Отправьте "/list" чтобы посмотреть список команд бота';

    public function handle()
    {
        $commands = [
            'start' => 'Подписка на бота',
            'stop' => 'Отписка от бота',
            'list' => 'Список команд',
        ];

        $commandList = '';
        foreach ($commands as $command => $descr) {
            $commandList .= '/' . $command . ' ' . $descr . "\r\n";
        }

        $this->sendMessage([
            'text' => $commandList
        ]);
        return true;
    }
}
