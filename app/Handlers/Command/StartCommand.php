<?php

namespace App\Handlers\Command;

use App\Models\Subscriber;
use WeStacks\TeleBot\Handlers\CommandHandler;

class StartCommand extends CommandHandler
{
    protected static $aliases = [ '/start'];
    protected static $description = 'Подписка на бота';

    public function handle()
    {
        $chat_id = $this->update->message->chat->id;
        Subscriber::query()
            ->where('chat_id', '=', $chat_id)
            ->update(['is_active' => true]);

        $this->sendMessage([
            'text' => 'Вы подписаны' //. $chat_id,
        ]);
        return true;
    }
}
