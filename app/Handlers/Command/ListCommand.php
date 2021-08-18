<?php

namespace App\Handlers\Command;


use App\Models\Subscriber;
use Illuminate\Support\Facades\Log;
use WeStacks\TeleBot\Handlers\CommandHandler;
use WeStacks\TeleBot\Objects\Update;
use WeStacks\TeleBot\TeleBot;

class ListCommand extends BaseCommand
{
    protected static $commandsNotToShow = [
        SetFioCommand::class,
        SetPositionCommand::class,
    ];
    public static $aliases = ['/list'];
    protected static $description = 'Список команд';
    //protected $doNotShow = [];

    public function __construct(TeleBot $bot, Update $update)
    {
        parent::__construct($bot, $update);

        foreach (self::$commandsNotToShow as $commandClass) {
            $commandClass = new $commandClass($bot, $update);
            array_merge($this->doNotShow, $commandClass::$aliases);
        }
        $this->doNotShow = array_unique($this->doNotShow);
    }

    public function handle()
    {
        $r = $this->getLocalCommands();
        $commandList = '';
        foreach ($r as $obj) {
            $item = $obj->toArray();
            if (in_array($item['command'], $this->doNotShow)) {
                continue;
            }
            $commandList .= $item['command'] . ' ' . $item['description'] . "\r\n";
        }

        $this->sendMessage([
            'text' => $commandList
        ]);
        return true;
    }

    public static function trigger(Update $update, TeleBot $bot): bool
    {
        if (isset($update->callback_query->data) && in_array($update->callback_query->data, static::$aliases)) {
            return true;
        }
        return parent::trigger($update, $bot);
    }
}
