<?php

namespace App\Handlers\Command;

use App\Models\Subscriber;
use WeStacks\TeleBot\Handlers\CommandHandler;

class StopCommand extends CommandHandler
{
    protected static $aliases = [ '/stop'];
    protected static $description = 'Отписка от бота';

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
