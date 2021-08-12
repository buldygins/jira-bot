<?php

namespace App\Handlers\Command;

use App\Models\Subscriber;
use WeStacks\TeleBot\Handlers\CommandHandler;

class StopCommand extends BaseCommand
{
    protected static $aliases = [ '/stop'];
    protected static $description = 'Отписка от бота';

    public function handle()
    {
        parent::handle();

        $this->sub->is_active=false;
        $this->sub->save();

        $this->sendMessage([
            'text' => 'Вы отписаны от рассылки '
        ]);
        return true;
    }
}
