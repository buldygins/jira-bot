<?php

namespace App\Handlers\Command;

use App\Models\Subscriber;
use WeStacks\TeleBot\Handlers\CommandHandler;

class BaseCommand extends CommandHandler
{
    public $sub;

    public function handle()
    {
        dd($this->update);
        $chat_id = $this->update->message->chat->id;
        $this->sub = Subscriber::query()->where('chat_id', '=', $chat_id)->first();
        return true;
    }

    public function getJiraArrayConfiguration()
    {
        return [
            'jiraHost' => env('JIRA_URL'),
            'jiraUser' => $this->sub->jira_login,
            'jiraPassword' => $this->sub->api_token,
        ];
    }
}
