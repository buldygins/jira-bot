<?php

namespace App\Handlers\Command;

use App\Models\Position;
use App\Models\Subscriber;
use Illuminate\Support\Facades\DB;
use WeStacks\TeleBot\Handlers\CommandHandler;

class SetFioCommand extends BaseCommand
{
    protected static $aliases = ['/set_my_name'];
    protected static $description = 'Задать ФИО';

    public function answer($text)
    {
        $this->sub->fio=$text;
        $this->sub->waited_command=null;
        $this->sub->save();

        $this->sendMessage([
            'text' => 'Ваше ФИО записано',
        ]);
    }

    public function handle()
    {
        parent::handle();

        $this->sub->waited_command='SetFioCommand';
        $this->sub->save();

        $this->sendMessage([
            'text' => "Задайте свои ФИО",
            'chat_id'=>$this->update->message->chat->id,
        ]);
        return true;
    }
}
