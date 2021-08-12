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
            ->first();

        $sub->waited_command=null;
        $sub->save();

        return true;
    }

    public function handle()
    {
        $sub = Subscriber::query()
            ->where('chat_id', '=', $this->update->message->chat->id)
            ->update(['waited_command'=>'SetPositionCommand']);

        $keyboard = [
            [ "Кнопка 1" ],
            [ "Кнопка 2" ],
            [ "Кнопка 3" ]
        ];
        $reply_markup = json_encode([
            "keyboard"=>$keyboard,
            "resize_keyboard"=>true
        ]);

        $this->sendMessage([
            'text' => 'Задайте свою должность ',
            'chat_id'=>$this->update->message->chat->id,
            //'reply_markup'=>$reply_markup
            //$chat_id,
        ]);
        return true;
    }
}
