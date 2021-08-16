<?php

namespace App\Handlers\Command;


use App\Models\Subscriber;
use Illuminate\Support\Facades\Log;
use WeStacks\TeleBot\Handlers\CommandHandler;
use WeStacks\TeleBot\Objects\Update;
use WeStacks\TeleBot\TeleBot;

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

    public static function trigger(Update $update, TeleBot $bot): bool
    {
        if (in_array($update->callback_query->data, static::$aliases)) {
            return true;
        }
        return false;
    }
}
