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

    public function start()
    {
        $chat_id = $this->update->message->chat->id;
        Subscriber::query()
            ->where('chat_id', '=', $chat_id)
            ->update(['is_active' => true]);

        $this->sendMessage([
            'text' => 'Вы подписаны' //. $chat_id,
        ]);
        return true;
    }

    public function stop()
    {
        $chat_id = $this->update->message->chat->id;
        Subscriber::query()
            ->where('chat_id', '=', $chat_id)
            ->update(['is_active' => false]);

        $this->sendMessage([
            'text' => 'Вы отписаны от рассылки ' //. $chat_id,
        ]);
        return true;
    }

    public function list()
    {
        $commands = [
            'start' => 'Подписка на бота',
            'stop' => 'Отписка от бота',
            'list' => 'Список команд',
        ];

        $commandList = '';
        foreach ($commands as $command => $descr) {
            $commandList .= '/' . $command . ' ' . $descr . "\r\n";
        }

        $this->sendMessage([
            'text' => $commandList
        ]);
        return true;
    }

    public function command($cmd)
    {
//        $this->sendMessage([
//            'text' => 'Команда: '.$cmd //. $chat_id,
//        ]);
        $fn = str_replace('/', '', $cmd);
        try {
            $this->$fn();
        } catch (\Exception $exception) {
            $this->sendMessage([
                'text' => 'Увы, такой команды нет: '.$cmd
            ]);

        }
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
        } else {
            $this->command($command);
        }
    }
}
