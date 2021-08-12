<?php

namespace App\Handlers\Command;

use App\Models\Position;
use App\Models\Subscriber;
use Illuminate\Support\Facades\DB;
use WeStacks\TeleBot\Handlers\CommandHandler;

class SetPositionCommand extends BaseCommand
{
    protected static $aliases = ['/set_position'];
    protected static $description = 'Задать мою должность';

    public function answer($text)
   {
       Subscriber::query()
           ->where('chat_id', '=', $this->update->message->chat->id)
           ->update(['id_position'=>Position::find($text)->id]);

       $this->sendMessage([
            'text' => Position::find($text)->name,
        ]);
    }

//    public function waited($text)
//    {
//        $this->sendMessage([
//            'text' => 'WAITED ' . $text
//        ]);
//
//        $sub = Subscriber::query()
//            ->where('chat_id', '=', $this->update->message->chat->id)
//            ->first();
//
//        $sub->waited_command=null;
//        $sub->save();
//
//        return true;
//    }

    public function handle()
    {
        parent::handle();

        $this->sub->waited_command='SetPositionCommand';
        $this->sub->save();

        $keyboard = [
            [ "Кнопка 1" ],
            [ "Кнопка 2" ],
            [ "Кнопка 3" ]
        ];
        $reply_markup = json_encode([
            "keyboard"=>$keyboard,
            "resize_keyboard"=>true
        ]);

        $list1='';
        $positions=Position::query()->get();
        foreach($positions as $position)
        {
            $list1.="/set_position_".$position->id.' '.$position->name."\r\n";
        }

        $this->sendMessage([
            'text' => "Задайте свою должность \r\n".$list1,
            'chat_id'=>$this->update->message->chat->id,
        ]);
        return true;
    }
}
