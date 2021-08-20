<?php

namespace App\Handlers\Command;

use App\Models\Subscriber;
use Illuminate\Support\Facades\Log;
use WeStacks\TeleBot\Handlers\CommandHandler;
use WeStacks\TeleBot\Objects\Update;
use WeStacks\TeleBot\TeleBot;

class BaseCommand extends CommandHandler
{
    public $sub;

    public function __construct(TeleBot $bot, Update $update)
    {
        parent::__construct($bot, $update);
        Log::channel('telegram_log')->info(json_encode($this->update));
        if (isset($this->update->message)){
            $chat_id = $this->update->message->chat->id;
            $this->sub = Subscriber::query()->where('chat_id', '=', $chat_id)->first();
        }
    }

    public function handle()
    {
    }

    public function getJiraArrayConfiguration()
    {
        return [
            'jiraHost' => env('JIRA_URL'),
            'jiraUser' => $this->sub->jira_login,
            'jiraPassword' => $this->sub->api_token,
        ];
    }


    public function checkCancel($text)
    {
        if (strpos($text, static::$cancelAuth) !== false) {
            $this->sendMessage([
                'parse_mode' => 'HTML',
                'text' => "Действие отменено.",
                'chat_id' => $this->update->message->chat->id,
                'reply_markup' => $this->keyboardService->removeKeyboard(),
            ]);

            $this->sub->waited_command = null;
            $this->sub->save();

            return true;
        }
        return false;
    }
}
