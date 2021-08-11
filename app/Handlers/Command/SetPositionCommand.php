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

        $sub=Subscriber::query()
            ->where('chat_id', '=', $chat_id)
            ->first();


        $sub->waited_command='SetPositionCommand';
            $sub->save();


        $this->sendMessage([
            'text' => 'Задайте свою должность '. $sub->id
            //$chat_id,
        ]);
        return true;
    }
}
