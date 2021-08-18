<?php

namespace App\Handlers\Command;

use App\Models\Position;
use App\Models\Subscriber;
use Illuminate\Support\Facades\DB;
use WeStacks\TeleBot\Handlers\CommandHandler;

class SetFioCommand extends BaseCommand
{
    public static $aliases = ['/set_my_name'];
    protected static $description = 'Задать ФИО';

    public function answerFio($text)
    {
        $sub = Subscriber::query()->where('chat_id', '=', $this->update->message->chat->id)->first();
        $sub->fio = trim($text);
        $sub->waited_command = null;
        $sub->save();

        $this->sendMessage([
            'text' => 'Ваше ФИО записано',
        ]);
    }

    public function handle()
    {
        parent::handle();

        if (isset($this->sub)) {
            $this->sub->waited_command = 'SetFioCommand::answerFio';
            $this->sub->save();
        }

        $this->sendMessage([
            'text' => "Задайте свои ФИО",
            'chat_id' => $this->update->message->chat->id,
        ]);
        return true;
    }
}
