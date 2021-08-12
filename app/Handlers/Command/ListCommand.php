<?php

namespace App\Handlers\Command;

use App\Models\Subscriber;
use WeStacks\TeleBot\Handlers\CommandHandler;

class ListCommand extends BaseCommand
{
    protected static $aliases = [ '/list'];
    protected static $description = 'Список команд';

    public function handle()
    {
        $r=$this->getLocalCommands();
        $commandList = '';
        foreach($r as $obj) {
            $item=$obj->toArray();
            $commandList .= $item['command'] . ' ' . $item['description'] . "\r\n";
        }

        $this->sendMessage([
            'text' => $commandList
        ]);
        return true;
    }
}
