<?php

namespace App\Handlers;

use App\Handlers\Command\SetPositionCommand;
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

        if (strpos($cmd,'/') === 0) {
            return true;
        }

        //=== далее = ответы на команды

        if (!is_null($subscriber->waited_command)) {

            $waited_command = explode('::', $subscriber->waited_command);
            if (isset($waited_command[0]) && isset($waited_command[1])) {
                $commandClass = $waited_command[0];
                $methodName = $waited_command[1];
                if (class_exists($commandClass) && method_exists($commandClass, $methodName)) {
                    $commandHandler = new $commandClass($this->bot, $this->update);
                    return $commandHandler->$methodName($cmd);
                }
            }
        }
        
        return true;
    }

    public function handle()
    {
        $update = $this->update;
        $bot = $this->bot;

        $chat_id = $this->update->message->chat->id;
        $command = $this->update->message->text ?? null;

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
