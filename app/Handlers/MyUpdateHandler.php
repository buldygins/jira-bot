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

    public function handle()
    {
        $update = $this->update;
        $bot = $this->bot;

        $chat_id = $this->update->message->chat->id;

        $subscriber=Subscriber::query()->where('chat_id','=',$chat_id)->first();
        if (!$subscriber) {
            Subscriber::query()->create(['chat_id'=>$chat_id]);
            $this->sendMessage([
                'text' => 'Вы успешно подписаны. '. $chat_id,
            ]);
        }
        else{
            $this->sendMessage([
                'text' => 'Вы уже были подписаны ранее. '. $chat_id,
            ]);
        }


    }
}
