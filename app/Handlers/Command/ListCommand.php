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
        $chat_id = $this->update->message->chat->id;
        Subscriber::query()
            ->where('chat_id', '=', $chat_id)
            ->update(['is_active' => false]);

        $this->sendMessage([
            'text' => 'Вы отписаны от рассылки ' //. $chat_id,
        ]);
        return true;
    }
}
