<?php

namespace App\Handlers\Command;

use App\Models\JiraIssue;
use App\Models\JiraIssueStatus;
use App\Models\JiraUser;
use App\Models\Position;
use App\Models\Subscriber;
use App\Notifications\MyTelegramNotification;
use App\Service\KeyboardService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
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
    public $issue;

    public function __construct(TeleBot $bot, Update $update)
    {
        parent::__construct($bot, $update);
        $this->keyboardService = new KeyboardService();
    }

    public static function trigger(Update $update, TeleBot $bot)
    {
        if (isset($update->callback_query->data)) {
            foreach (self::$aliases as $alias) {
                Log::channel('telegram_log')->info('Trying to match ' . $update->callback_query->data . ' with ' . $alias);
                if (strpos($update->callback_query->data, $alias) !== false) {
                    return true;
                }
            }
        }
        return parent::trigger($update, $bot);
    }

    public function handle()
    {
        $data = $this->parseData();
        switch ($data['field']) {
            case 'status':
                return $this->askStatus();
            default:
                $this->sendMessage([
                    'parse_mode' => 'HTML',
                    'text' => "Произошла ошибка. Попробуйте снова.",
                    'reply_markup' => $this->keyboardService->removeKeyboard(),
                ]);
                return true;
        }
    }

    public function askStatus()
    {
        $statuses = JiraIssueStatus::getClosestStatusesName($this->issue);
        $issue_link = $this->issue->getLink();

        $this->sub->waited_command = get_class($this) . '::answerStatus';
        $this->sub->save();

        $this->sendMessage([
            'parse_mode' => 'HTML',
            'text' => "Выберите статус для задачи {$issue_link}.",
            'reply_markup' => $this->keyboardService->makeKeyboard(array_merge($statuses, [static::$cancel]), 2),
        ]);
        return true;
    }

    public function answerStatus($text)
    {
        $status = JiraIssueStatus::getStatusByFullName(trim($text));
        if (!$status) {
            $this->sendMessage([
                'parse_mode' => 'HTML',
                'text' => "Выбран неправильный статус. Попробуйте снова.",
                'reply_markup' => $this->keyboardService->removeKeyboard(),
            ]);
            return true;
        }
        $this->issue->previous_status_id = $this->issue->status_id;
        $this->issue->status_id = $status->id;
        $this->issue->save();

        $this->sendMessage([
            'parse_mode' => 'HTML',
            'text' => "Статус успешно изменён.",
            'reply_markup' => $this->keyboardService->removeKeyboard(),
        ]);

        if ($this->issue->previous_status->jiraId != $status->jiraId) {

            $log_message_header = 'Задача';
            $log_message_body = 'Изменён статус';

            \App\Models\Log::create([
                'issue_id' => $this->issue->issue_id,
                'issue_key' => $this->issue->key,
                'project_key' => null,
                'webhook_event' => 'telegram_update_issue',
                'name' => $log_message_header,
                'body' => $log_message_body,
                'src' => null,
            ]);

            $data = compact('log_message_body', 'log_message_header');

            $subscribers = Subscriber::where('is_active', '=', true)->get();
            foreach ($subscribers as $subscriber) {

                if (
                    in_array($this->issue->project_key, $subscriber->team->projectList()) && !$subscriber->wantsOnlyTagged()
                    || $this->sub->isUserTagged($subscriber)
                ) {
                    Notification::send($subscriber, new MyTelegramNotification($this->issue, $data));
                }
            }
        }

        return true;
    }

    public function parseData()
    {
        $data = $this->update->callback_query->data;
        foreach (self::$aliases as $alias) {
            $data = str_replace($alias, '', $data);
        }
        parse_str($data, $data);
        $issue_id = $data['issue_id'];
        $this->issue = JiraIssue::find($issue_id);
        if (!$this->issue) {
            return [];
        }
        return $data;
    }
}
