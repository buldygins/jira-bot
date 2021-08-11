<?php

namespace App\Handlers\Command;

use App\Models\Subscriber;
use WeStacks\TeleBot\Handlers\CommandHandler;

class SetPositionCommand extends CommandHandler
{
    protected static $aliases = [ '/set_position'];
    protected static $description = 'Задать мою должность';

    public function handle()
    {
        $chat_id = $this->update->message->chat->id;
        Subscriber::query()
            ->where('chat_id', '=', $chat_id)
            ->update(['waited_command' => self::class]);

        $this->sendMessage([
            'text' => 'Задайте свою должность ' //. $chat_id,
        ]);
        return true;
    }
}
