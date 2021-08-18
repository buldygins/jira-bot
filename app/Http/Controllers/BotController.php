<?php

namespace App\Http\Controllers;

use App\Models\JiraIssue;
use App\Models\JiraUser;
use App\Models\Log;
use App\Models\Position;
use App\Models\Subscriber;
use App\Notifications\MyTelegramNotification;
use App\Service\KeyboardService;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Notification;
use WeStacks\TeleBot\TeleBot;
use WeStacks\TeleBot\Objects\User;

class BotController extends BaseController
{
    public $changelog;

    public $data = [];

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function index()
    {
        $bot = new TeleBot([
            'token' => env('TELEGRAM_BOT_TOKEN'),
            'api_url' => 'https://api.telegram.org',
            'exceptions' => true,
            'async' => false,
            'handlers' => []
        ]);

        /** @var User */
        $user = $bot->getMe();
        //dd($user);


// You may change all config parameters "on the go" using get/set syntax
        $bot->async = true; // Now bot uses A+ promises

        $bot->getMe()->then(function (User $user) {
            var_dump($user);
        })->wait();
    }

    public function parse_jira_users($json)
    {
        if (isset($json->user)) {
            //dd($json->user);
            $jirauser = JiraUser::query()->firstOrCreate([
                'key' => $json->user->key ?? null,
                'name' => $json->user->name ?? null,
                'accountId' => $json->user->accountId ?? null,
                'active' => $json->user->active ?? true,
                'timeZone' => $json->user->timeZone ?? 'Europe/Moscow',
                'displayName' => $json->user->displayName ?? null,
            ]);
        }
    }

    public function jira(Request $req)
    {
        file_put_contents('4.txt', var_export($req->getContent(), true));
        $rawData = file_get_contents("php://input");

        $f = var_export($_REQUEST, true);
        file_put_contents('1.txt', $rawData);
        file_put_contents('2.txt', $f);
        $jsonData = json_decode($rawData, true);
        $json = json_decode($rawData);
        $f2 = var_export($jsonData, true);
        file_put_contents('3.txt', $f2);
//----------

        $this->parse_jira_users($json);

        $webhook_parts = explode('_', $json->webhookEvent);

        $log_message_header = '';
        $log_message_body = '';

        $changeLog = $json->changelog ?? null;
        $this->parseChangelog($changeLog->items ?? []);


        $project_key = $json->issue->fields->project->key ?? null;
        if ($webhook_parts[0] == 'worklog') {
            $issue_id = $json->worklog->issueId;

            $log_message_header = '{action} записи о работе от ' . $json->worklog->author->displayName . ' ' . $json->worklog->timeSpent . " " .
                Carbon::createFromTimeString($json->worklog->created)->toDateString();

            if (isset($json->worklog->comment)){
                $log_message_body .= "Комментарий к работе: {$json->worklog->comment}\n";
            }
        }

        if ($webhook_parts[0] == 'comment') {
            $issue_id = $json->issue->id;
            $log_message_header = "{action} комментария #" . $json->comment->id . ' от пользователя ' . $json->comment->updateAuthor->displayName . "\r\n\r\n" .
                "------\r\n" . $json->comment->body;

        }

        if ($webhook_parts[0] == 'jira:issue') {
            $issue_id = $json->issue->id;
            $assignee = $this->getAssignee($json->issue->fields->assignee->displayName ?? null);
            $status = $this->getStatus($json->issue->fields->status->name ?? null);
            $log_message_header = "{$assignee}{$status}{action} задачи пользователем {$json->user->displayName}.\n";
        }

        if ($webhook_parts[1] == 'created') {
            $log_message_header = str_replace('{action}', '📌Создание', $log_message_header);
        } elseif ($webhook_parts[1] == 'updated') {
            $log_message_header = str_replace('{action}', 'Изменение', $log_message_header);
        } elseif ($webhook_parts[1] == 'deleted') {
            $log_message_header = str_replace('{action}', '❌Удаление', $log_message_header);
        } else {
            $log_message_header = str_replace('{action}', $json->webhookEvent, $log_message_header);
        }

        if (!empty($this->changelog)) {
            $log_message_body .= "Изменения: ";
            foreach ($this->changelog as $field => $change) {
                if (empty($change['from']) && !empty($change['to'])) {
                    $log_message_body .= "\nПоле {$field}\nНовое значение\n{$change['to']}\n";
                } elseif (!empty($change['from'] && empty($change['to']))) {
                    $log_message_body .= "\n❌Поле {$field} удалено\nБыло\n\"{$change['from']}\"";
                } else {
                    $log_message_body .= "\nПоле {$field}\nБыло\n\"{$change['from']}\"\nСтало\n\"{$change['to']}\"";
                }
            }
        }

        if (isset($json->issue_event_type_name)) {
            $eventTypeName = $this->getIssueEventTypeName($json->issue_event_type_name);
            if (!empty($this->changelog) || strpos($eventTypeName, 'deleted') !== false) {
                $log_message_body = $eventTypeName . $log_message_body;
            }
        }

        $this->data['log_message_body'] = $log_message_body;
        $this->data['log_message_header'] = $log_message_header;

        $issue = JiraIssue::query()->where('issue_id', '=', $issue_id)->first();

        if (!$issue) {

            if (isset($json->issue->key)) {
                $issue_key = $json->issue->key;
            } else {
                $issue_key = "NOT-01";
            }

            $issue = JiraIssue::query()->create(
                [
                    'key' => $issue_key,
                    'issue_id' => $issue_id,
                    'project_key' => $project_key,
                    'event_created' => Carbon::createFromTimestamp($json->timestamp)->toDateTimeString(),
                    //'updateAuthor' => $json->updateAuthor,
                    'webhookEvent' => $json->webhookEvent,
                    'issue_url' => env('JIRA_URL') . 'browse/' . $issue_key,
                    'summary' => $json->issue->fields->summary ?? null,
                    'src' => $rawData,
                ]);

            Log::create([
                'issue_id' => $issue_id,
                'issue_key' => $issue_key,
                'project_key' => $project_key,
                'webhook_event' => $json->webhookEvent,
                'name' => $log_message_header,
                'body' => $log_message_body,
                'src' => $rawData,
            ]);
        } else {

            Log::create([
                'issue_id' => $issue_id,
                'issue_key' => $issue->key,
                'project_key' => $project_key,
                'webhook_event' => $json->webhookEvent,
                'name' => $log_message_header,
                'body' => $log_message_body,
                'src' => $rawData,
            ]);

            $issue->issue_id = $issue_id;
            $issue->event_created = Carbon::createFromTimestamp($json->timestamp)->toDateTimeString();
            $issue->webhookEvent = $json->webhookEvent;
            $issue->src = $rawData;

            if ($webhook_parts[0] != 'worklog') {
                $issue->key = $json->issue->key;
                $issue->issue_url = env('JIRA_URL') . 'browse/' . $json->issue->key;
                $issue->summary = $json->issue->fields->summary;
            }

            $issue->save();
        }

        $issue = JiraIssue::query()->where('issue_id', '=', $issue_id)->first();

        $keyboardService = app(KeyboardService::class);

        $subscribers = Subscriber::where('is_active', '=', true)->get();
        foreach ($subscribers as $subscriber) {
            if (!in_array($issue->projeck_key,$subscriber->team->projectList()))
            {
                return false;
            }

            $this->data['keyboard'] = $keyboardService->buildIssueKeyboard($subscriber, $issue);
            Notification::send($subscriber, new MyTelegramNotification($issue, $this->data));
        }
    }

    public function parseChangelog($changelog)
    {
        foreach ($changelog as $key => $item) {
            switch ($item->field) {
                case 'Attachment':
                    $link = env('JIRA_URL') . "/secure/attachment/{$item->to}/{$item->toString}";
//                    preg_match('~^https?://\S+(?:jpg|jpeg|png)$~', $link, $match);
//                    if ($match) {
//                        try {
//                            $image = fopen($link, 'r');
//                        } catch (\Exception $exception) {
//                            $image = $link;
//                        }
//                    }
//                    if (!empty($match)) {
//                        $this->data['image'][] = $link;
//                        break;
//                    }
                    if (!empty($item->to)) {
                        $this->changelog[$item->field]['to'] = "<a href='{$link}' style='margin-right: 5px;'>{$item->toString}</a>";
                    }
                    if (!empty($item->fromString)) {
                        $link_from = env('JIRA_URL') . "/secure/attachment/{$item->from}/{$item->fromString}";
                        $this->changelog[$item->field]['from'] = "<a href='{$link_from}' style='margin-right: 5px;'>{$item->fromString}</a>";
                    }
                    break;
                default:
                    $this->changelog[$item->field]['to'] = $item->toString ?? 'не назначено';
                    if (!empty($item->fromString)) {
                        $this->changelog[$item->field]['from'] = $item->fromString;
                    }
                    break;
            }
        }
    }

    public function getAssignee($displayName)
    {
        $assignee = "Исполнитель: ";
        if (isset($this->changelog['assignee']['from'])) {
            $assignee .= $this->changelog['assignee']['from'] . ' -> ';
        } elseif ($displayName == null) {
            $assignee .= 'не назначено -> ';
        }
        if (isset($this->changelog['assignee']['to'])) {
            $displayName = $this->changelog['assignee']['to'];
        } elseif ($displayName == null) {
            $displayName = 'ERR';
        }
        $assignee .= "{$displayName}.\n";
        unset($this->changelog['assignee']);
        return $assignee;
    }

    public function getStatus($statusName = 'Нет статуса')
    {
        $status = "Статус: ";
        if (isset($this->changelog['status']['from'])) {
            $status .= $this->changelog['status']['from'] . ' -> ';
        }
        if (isset($this->changelog['status']['to'])) {
            $statusName = $this->changelog['status']['to'];
        } elseif ($statusName == null) {
            $statusName = 'ERR';
        }
        $status .= "{$statusName}.\n";
        unset($this->changelog['status']);
        return $status;
    }

    public function getIssueEventTypeName($eventTypeName)
    {
        $eventTypeNamesList = [
            "issue_created" => "Была создана задача.\n",
//            "issue_assigned" => "Задача была назначена новому пользователю.\n",
//            "issue_resolved" => "Задача была решена.\n",
//            "issue_closed" => "Задача была закрыта.\n",
            "issue_commented" => "В задаче добавлен комментарий.\n",
            "issue_comment_edited" => "Комментарий был изменён.\n",
//            "issue_reopened" => "Задача была вновь открыта\n",
            "issue_deleted" => "Задача была удалена.\n",
            "issue_moved" => "Задачу была перемещена в другой проект.\n",
            "issue_worklogged" => "Добавлена запись о работе.\n",
            "work_logged_on_issue" => "Добавлена запись о работе.\n",
//            "issue_workstarted" => "Исполнитель начал работу над задачей.\n",
//            "work_started_on_issue" => "Исполнитель начал работу над задачей.\n",
//            "issue_workstopped" => "Исполнитель закончил работу над задачей\n",
            "issue_worklog_updated" => "Изменена запись о работе в задаче.\n",
            "issue_worklog_deleted" => "Запись о работе была удалена.\n",
            "issue_updated" => "Обновление полей.\n",
            "issue_generic" => "Общее событие.\n",
            "issue_comment_deleted" => "Был удалён комментарий.\n",
        ];

        return $eventTypeNamesList[$eventTypeName] ?? '';
    }
}
