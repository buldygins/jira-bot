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

    public function command($cmd, Subscriber $subscriber)
    {

        $fn = str_replace('/', '', $cmd);
        if ($fn != $cmd) {
            return true;
        }

        if (!is_null($subscriber->waited_command)) {
            $this->sendMessage([
                'text' => $subscriber->waited_command . ' Команда: ' . $cmd //. $chat_id,
            ]);
        }
//        try {
//            $this->$fn();
//        } catch (\Exception $exception) {
//            $this->sendMessage([
//                'text' => 'Увы, такой команды нет: '.$cmd
//            ]);
//
//        }
        return true;
    }

    public function handle()
    {
        $update = $this->update;
        $bot = $this->bot;

        $chat_id = $this->update->message->chat->id;
        $command = $this->update->message->text;

        $subscriber = Subscriber::query()->where('chat_id', '=', $chat_id)->first();
        if (!$subscriber) {
            Subscriber::query()->create(['chat_id' => $chat_id]);
            $this->sendMessage([
                'text' => 'Вы успешно добавлены. ' //. $chat_id,
            ]);
        }

        $this->command($command, $subscriber);
    }
}
