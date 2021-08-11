<?php

namespace App\Handlers;

use App\Models\Subscriber;
use WeStacks\TeleBot\Interfaces\UpdateHandler;
use WeStacks\TeleBot\Objects\Update;
use WeStacks\TeleBot\TeleBot;

class MyUpdateHandler extends UpdateHandler
{
    public static function trigger(Update $update, TeleBot $bot): bool
    {
        return isset($update->message); // handle regular messages (example)
    }

    public function command($cmd)
    {
        if ($cmd=='/stop')
        {

            $chat_id = $this->update->message->chat->id;
            Subscriber::query()
                ->where('chat_id','=',$chat_id)
                ->update(['is_active'=>false]);

            $this->sendMessage([
                'text' => 'Вы отписаны от рассылки ' //. $chat_id,
            ]);
            return true;
        }

        if ($cmd=='/start')
        {

            $chat_id = $this->update->message->chat->id;
            Subscriber::query()
                ->where('chat_id','=',$chat_id)
                ->update(['is_active'=>true]);

            $this->sendMessage([
                'text' => 'Вы подписаны' //. $chat_id,
            ]);
            return true;
        }

        if ($cmd=='/list')
        {
            $this->sendMessage([
                'text' => 'Список команд:
                /start - Подписка на бота
                /stop - Отписка от бота
                /list - Список команд
                '
            ]);
            return true;
        }

        return false;
    }

    public function handle()
    {
        $update = $this->update;
        $bot = $this->bot;

        $chat_id = $this->update->message->chat->id;
        $command = $this->update->message->text;

        $subscriber=Subscriber::query()->where('chat_id','=',$chat_id)->first();
        if (!$subscriber) {
            Subscriber::query()->create(['chat_id'=>$chat_id]);
            $this->sendMessage([
                'text' => 'Вы успешно подписаны. ' //. $chat_id,
            ]);
        }
        elseif (!$this->command($command))
        {
            $this->sendMessage([
                'text' => 'Вы уже были подписаны ранее. ' //. $chat_id,
            ]);
        }


    }
}
