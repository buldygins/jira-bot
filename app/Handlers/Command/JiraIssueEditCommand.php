<?php

namespace App\Handlers\Command;

use App\Models\JiraUser;
use App\Models\Position;
use App\Service\KeyboardService;
use Illuminate\Support\Facades\Log;
use JiraRestApi\Configuration\ArrayConfiguration;
use JiraRestApi\User\UserService;
use WeStacks\TeleBot\Objects\Update;
use WeStacks\TeleBot\TeleBot;

class JiraIssueEditCommand extends BaseCommand
{
    public static $aliases = ['/jira_issue_edit'];
    protected static $description = 'Изменение полей задачи Jira';
    protected static $cancel = 'Отменить';
    protected $keyboardService;

    public function __construct(TeleBot $bot, Update $update)
    {
        parent::__construct($bot, $update);
        $this->keyboardService = new KeyboardService();
    }

    public static function trigger(Update $update, TeleBot $bot)
    {
        if (isset($update->callback_query->data)) {
            foreach (self::$aliases as $alias) {
                if (strpos($update->callback_query->data, $alias) !== false) {
                    return true;
                }
            }
        }
        return parent::trigger($update, $bot);
    }

    public function handle()
    {
        parent::handle();

        $data = $this->update->callback_query->data;
        foreach (self::$aliases as $alias) {
            $data = str_replace($alias, '', $data);
        }
        parse_str($data,$data);
        $issueKey = $data['issue'];
        unset($data['issue']);
    }

    public function checkCancel($text)
    {
        if (strpos($text, self::$cancel) !== false) {
            $this->sendMessage([
                'parse_mode' => 'HTML',
                'text' => "Действие отменено.",
                'chat_id' => $this->update->message->chat->id,
                'reply_markup' => $this->keyboardService->removeKeyboard(),
            ]);
            exit();
        }
    }
}
