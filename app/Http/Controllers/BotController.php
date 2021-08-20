<?php

namespace App\Http\Controllers;

use App\Models\JiraIssue;
use App\Models\JiraIssueStatus;
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

    public $issue;

    private $assignee;

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function index()
    {
        $bot = new TeleBot([
            'token' => config('telebot.bots.bot.token'),
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

        $bot->setMyCommands([
            'commands' => $bot->getLocalCommands()
        ]);
    }

    public function parse_jira_users($json)
    {
        if (isset($json->user)) {
            //dd($json->user);
            $this->assignee = JiraUser::query()->firstOrCreate([
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
        //$rawData='{"timestamp":1629270380422,"webhookEvent":"comment_created","comment":{"self":"https://klienti.atlassian.net/rest/api/2/issue/10388/comment/10328","id":"10328","author":{"self":"https://klienti.atlassian.net/rest/api/2/user?accountId=557058%3Abf39af2f-2a15-473b-ba9b-fecfcab48e41","accountId":"557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41","avatarUrls":{"48x48":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/48","24x24":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/24","16x16":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/16","32x32":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/32"},"displayName":"Veniamin Smorodinsky","active":true,"timeZone":"Europe/Moscow","accountType":"atlassian"},"body":"77","updateAuthor":{"self":"https://klienti.atlassian.net/rest/api/2/user?accountId=557058%3Abf39af2f-2a15-473b-ba9b-fecfcab48e41","accountId":"557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41","avatarUrls":{"48x48":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/48","24x24":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/24","16x16":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/16","32x32":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/32"},"displayName":"Veniamin Smorodinsky","active":true,"timeZone":"Europe/Moscow","accountType":"atlassian"},"created":"2021-08-18T10:06:20.422+0300","updated":"2021-08-18T10:06:20.422+0300","jsdPublic":true},"issue":{"id":"10388","self":"https://klienti.atlassian.net/rest/api/2/10388","key":"TALK-42","fields":{"summary":"Баги при отправке сообщения","issuetype":{"self":"https://klienti.atlassian.net/rest/api/2/issuetype/10004","id":"10004","description":"Проблема или ошибка.","iconUrl":"https://klienti.atlassian.net/secure/viewavatar?size=medium&avatarId=10303&avatarType=issuetype","name":"Баг","subtask":false,"avatarId":10303,"hierarchyLevel":0},"project":{"self":"https://klienti.atlassian.net/rest/api/2/project/10031","id":"10031","key":"TALK","name":"MyTalking","projectTypeKey":"software","simplified":false,"avatarUrls":{"48x48":"https://klienti.atlassian.net/secure/projectavatar?pid=10031&avatarId=10421","24x24":"https://klienti.atlassian.net/secure/projectavatar?size=small&s=small&pid=10031&avatarId=10421","16x16":"https://klienti.atlassian.net/secure/projectavatar?size=xsmall&s=xsmall&pid=10031&avatarId=10421","32x32":"https://klienti.atlassian.net/secure/projectavatar?size=medium&s=medium&pid=10031&avatarId=10421"}},"assignee":{"self":"https://klienti.atlassian.net/rest/api/2/user?accountId=60e55212f90dee00694cff7e","accountId":"60e55212f90dee00694cff7e","avatarUrls":{"48x48":"https://secure.gravatar.com/avatar/dab68af54cc1eb6bdf14575a5a389269?d=https%3A%2F%2Favatar-management--avatars.us-west-2.prod.public.atl-paas.net%2Fdefault-avatar-6.png","24x24":"https://secure.gravatar.com/avatar/dab68af54cc1eb6bdf14575a5a389269?d=https%3A%2F%2Favatar-management--avatars.us-west-2.prod.public.atl-paas.net%2Fdefault-avatar-6.png","16x16":"https://secure.gravatar.com/avatar/dab68af54cc1eb6bdf14575a5a389269?d=https%3A%2F%2Favatar-management--avatars.us-west-2.prod.public.atl-paas.net%2Fdefault-avatar-6.png","32x32":"https://secure.gravatar.com/avatar/dab68af54cc1eb6bdf14575a5a389269?d=https%3A%2F%2Favatar-management--avatars.us-west-2.prod.public.atl-paas.net%2Fdefault-avatar-6.png"},"displayName":"Даниил","active":true,"timeZone":"Europe/Moscow","accountType":"atlassian"},"priority":{"self":"https://klienti.atlassian.net/rest/api/2/priority/3","iconUrl":"https://klienti.atlassian.net/images/icons/priorities/medium.svg","name":"Обычный","id":"3"},"status":{"self":"https://klienti.atlassian.net/rest/api/2/status/10003","description":"Выполненные задачи","iconUrl":"https://klienti.atlassian.net/","name":"Готово","id":"10003","statusCategory":{"self":"https://klienti.atlassian.net/rest/api/2/statuscategory/3","id":3,"key":"done","colorName":"green","name":"Готово"}}}}}';

        $f = var_export($_REQUEST, true);
        file_put_contents('1.txt', $rawData);
        file_put_contents('2.txt', $f);
        $jsonData = json_decode($rawData, true);
        $json = json_decode($rawData);
        $f2 = var_export($jsonData, true);
        file_put_contents('3.txt', $f2);
//----------

        //dd($json);
        $this->parse_jira_users($json);

        $webhook_parts = explode('_', $json->webhookEvent);

        $log_message_header = '';
        $log_message_body = '';

        $changeLog = $json->changelog ?? null;
        $this->parseChangelog($changeLog->items ?? []);

        $issue_id = $json->issue->id ?? $json->worklog->issueId;

        $this->issue = JiraIssue::where('issue_id', $issue_id)->first();

        $project_key = $json->issue->fields->project->key ?? null;
        if ($webhook_parts[0] == 'worklog') {
            $log_message_header = '{action} записи о работе от ' . $json->worklog->author->displayName . ' ' . $json->worklog->timeSpent . " " .
                Carbon::createFromTimeString($json->worklog->created)->toDateString();

            if (isset($json->worklog->comment)) {
                $log_message_body .= "Комментарий к работе: {$json->worklog->comment}\n";
            }
        }

        if ($webhook_parts[0] == 'comment') {
            $log_message_header = "{action} комментария #" . $json->comment->id . ' от пользователя ' . $json->comment->updateAuthor->displayName . "\r\n\r\n" .
                "------\r\n" . $json->comment->body;
        }

        if ($webhook_parts[0] == 'jira:issue') {
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
                    $log_message_body .= "\nПоле {$field}\n\n<b>Новое значение</b>\n\n{$change['to']}\n";
                } elseif (!empty($change['from'] && empty($change['to']))) {
                    $log_message_body .= "\n❌Поле {$field} удалено\n\n<b>Было</b>\n\n\"{$change['from']}\"";
                } else {
                    $log_message_body .= "\nПоле {$field}\n\n<b>Было</b>\n\n\"{$change['from']}\"\n\n<b>Стало</b>\n\n\"{$change['to']}\"";
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

            if (isset($json->issue->fields->status->id)) {
                $jira_status_id = $json->issue->fields->status->id;
                $status = JiraIssueStatus::where('jiraId', $jira_status_id)->orderBy('order')->get()->first();
            }

            $issue = JiraIssue::query()->create(
                [
                    'key' => $issue_key,
                    'issue_id' => $issue_id,
                    'project_key' => $project_key,
                    'event_created' => Carbon::createFromTimestamp($json->timestamp)->toDateTimeString(),
                    //'updateAuthor' => $json->updateAuthor,
                    'webhookEvent' => $json->webhookEvent,
                    'issue_url' => config('app.jira_url') . 'browse/' . $issue_key,
                    'summary' => $json->issue->fields->summary ?? null,
                    'src' => $rawData,
                    'status_id' => $status->id ?? null,
                    'assignee_id' => $this->assignee->id,
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
            $issue->project_key = $project_key;
            $issue->event_created = Carbon::createFromTimestamp($json->timestamp)->toDateTimeString();
            $issue->webhookEvent = $json->webhookEvent;
            $issue->src = $rawData;

            if ($webhook_parts[0] != 'worklog') {
                $issue->key = $json->issue->key;
                if (!isset($issue->issue_url)) {
                    $issue->issue_url = config('app.jira_url') . 'browse/' . $json->issue->key;
                }
                $issue->summary = $json->issue->fields->summary;
            }

            $issue->save();
        }

        $issue = JiraIssue::query()->where('issue_id', '=', $issue_id)->first();

        $keyboardService = app(KeyboardService::class);

        $msg = $this->data['log_message_header'] ?? '' . $this->data['log_message_body'] ?? '';

        $subscribers = Subscriber::where('is_active', '=', true)->get();
        foreach ($subscribers as $subscriber) {

            if (
                in_array($issue->project_key, $subscriber->team->projectList()) && !$subscriber->wantsOnlyTagged()
                || $subscriber->isUserTagged($msg)
            ) {
                $this->data['keyboard'] = $keyboardService->buildIssueKeyboard($subscriber, $issue);
                Notification::send($subscriber, new MyTelegramNotification($issue, $this->data));
            }
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
        } elseif (isset($this->changelog['assignee']['to']) && $displayName == null) {
            $assignee .= 'не назначено -> ';
        }
        if (isset($this->changelog['assignee']['to'])) {
            $displayName = $this->changelog['assignee']['to'];
        } elseif ($displayName == null) {
            $displayName = 'не назначено';
        }
        $assignee .= "{$displayName}.\n";
        unset($this->changelog['assignee']);
        return $assignee;
    }

    public function getStatus($statusName = 'Нет статуса')
    {
        $status = "Статус: ";
        if ($this->issue) {
            if (isset($this->changelog['status']['to'])) {
                $statusName = optional($this->issue->status->getStatusFullName());
                if (empty($statusName)) {
                    $statusName = $this->changelog['status']['to'];
                }
            }
        } else {
            if (isset($this->changelog['status']['from'])) {
                $status .= $this->changelog['status']['from'] . ' -> ';
            }
            if (isset($this->changelog['status']['to'])) {
                $statusName = $this->changelog['status']['to'];
            } elseif ($statusName == null) {
                $statusName = 'ERR';
            }
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
