<?php

namespace App\Handlers\Command;

use App\Models\Subscriber;
use WeStacks\TeleBot\Handlers\CommandHandler;

class StartCommand extends BaseCommand
{
    protected static $aliases = [ '/start'];
    protected static $description = 'Подписка на бота';

    public function handle()
    {
        parent::handle();

        if ($this->sub) {
            $this->sub->is_active = true;
            $this->sub->save();
        }

        $this->sendMessage([
            'text' => 'Вы подписаны' //. $chat_id,
        ]);
        return true;
    }
}
