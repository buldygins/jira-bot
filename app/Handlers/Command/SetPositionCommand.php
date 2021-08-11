<?php

namespace App\Handlers\Command;

use App\Models\Subscriber;
use Illuminate\Support\Facades\DB;
use WeStacks\TeleBot\Handlers\CommandHandler;

class SetPositionCommand extends CommandHandler
{
    protected static $aliases = ['/set_position'];
    protected static $description = 'Задать мою должность';

    public function waited($text)
    {
        $this->sendMessage([
            'text' => 'WAITED ' . $text
        ]);

        $sub = Subscriber::query()
            ->where('chat_id', '=', $this->update->message->chat->id)
            ->update(['waited_command'=>null]);

        return true;
    }

    public function handle()
    {
        $sub = Subscriber::query()
            ->where('chat_id', '=', $this->update->message->chat->id)
            ->update(['waited_command'=>'SetPositionCommand']);

        $this->sendMessage([
            'text' => 'Задайте свою должность ' //. $sub->id
            //$chat_id,
        ]);
        return true;
    }
}
