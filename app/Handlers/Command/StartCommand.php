<?php

namespace App\Handlers\Command;

use App\Models\Subscriber;
use WeStacks\TeleBot\Handlers\CommandHandler;

class StartCommand extends BaseCommand
{
    public static $aliases = [ '/start'];
    protected static $description = 'Подписка на бота';

    public function handle()
    {
        parent::handle();


        if ($this->sub) {
            if ($this->sub->is_active){
                $this->sendMessage([
                    'text' => 'Вы уже подписаны' //. $chat_id,
                ]);
                return true;
            }
            $this->sub->is_active = true;
            $this->sub->save();
        }

        $this->sendMessage([
            'text' => 'Вы подписаны' //. $chat_id,
        ]);
        return true;
    }
}
