<?php

namespace App\Handlers\Command;

use App\Models\Position;
use App\Models\Subscriber;
use Illuminate\Support\Facades\DB;
use WeStacks\TeleBot\Handlers\CommandHandler;

class SetFioCommand extends CommandHandler
{
    protected static $aliases = ['/set_my_name'];
    protected static $description = 'Задать ФМО';

    public function answer($text)
    {
        $sub = Subscriber::query()->where('chat_id', '=', $this->update->message->chat->id)->first();
        $sub->fio=$text;
        $sub->waited_command=null;
        $sub->save();

        $this->sendMessage([
            'text' => 'Ваше ФИО записано',
        ]);
    }

    public function handle()
    {
        Subscriber::query()
            ->where('chat_id', '=', $this->update->message->chat->id)
            ->update(['waited_command'=>'SetFioCommand']);

        $this->sendMessage([
            'text' => "Задайте свои ФИО",
            'chat_id'=>$this->update->message->chat->id,
            //'reply_markup'=>$reply_markup
            //$chat_id,
        ]);
        return true;
    }
}
