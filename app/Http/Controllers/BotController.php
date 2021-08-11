<?php

namespace App\Http\Controllers;

use App\Models\JiraIssue;
use App\Notifications\MyTelegramNotification;
use App\Notifications\TelegramNotification;
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

    public function jira(Request $req)
    {
        file_put_contents('4.txt', var_export($req->getContent(), true));
        $rawData = file_get_contents("php://input");
        //$rawData = '{"timestamp":1628677146887,"webhookEvent":"comment_created","comment":{"self":"https://klienti.atlassian.net/rest/api/2/issue/10509/comment/10274","id":"10274","author":{"self":"https://klienti.atlassian.net/rest/api/2/user?accountId=557058%3Abf39af2f-2a15-473b-ba9b-fecfcab48e41","accountId":"557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41","avatarUrls":{"48x48":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/48","24x24":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/24","16x16":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/16","32x32":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/32"},"displayName":"Veniamin Smorodinsky","active":true,"timeZone":"Europe/Moscow","accountType":"atlassian"},"body":"ttt","updateAuthor":{"self":"https://klienti.atlassian.net/rest/api/2/user?accountId=557058%3Abf39af2f-2a15-473b-ba9b-fecfcab48e41","accountId":"557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41","avatarUrls":{"48x48":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/48","24x24":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/24","16x16":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/16","32x32":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/32"},"displayName":"Veniamin Smorodinsky","active":true,"timeZone":"Europe/Moscow","accountType":"atlassian"},"created":"2021-08-11T13:19:06.887+0300","updated":"2021-08-11T13:19:06.887+0300","jsdPublic":true},"issue":{"id":"10509","self":"https://klienti.atlassian.net/rest/api/2/10509","key":"TALK-132","fields":{"summary":"Разлогин на странице чатов","issuetype":{"self":"https://klienti.atlassian.net/rest/api/2/issuetype/10004","id":"10004","description":"Проблема или ошибка.","iconUrl":"https://klienti.atlassian.net/secure/viewavatar?size=medium&avatarId=10303&avatarType=issuetype","name":"Баг","subtask":false,"avatarId":10303,"hierarchyLevel":0},"project":{"self":"https://klienti.atlassian.net/rest/api/2/project/10031","id":"10031","key":"TALK","name":"MyTalking","projectTypeKey":"software","simplified":false,"avatarUrls":{"48x48":"https://klienti.atlassian.net/secure/projectavatar?pid=10031&avatarId=10421","24x24":"https://klienti.atlassian.net/secure/projectavatar?size=small&s=small&pid=10031&avatarId=10421","16x16":"https://klienti.atlassian.net/secure/projectavatar?size=xsmall&s=xsmall&pid=10031&avatarId=10421","32x32":"https://klienti.atlassian.net/secure/projectavatar?size=medium&s=medium&pid=10031&avatarId=10421"}},"assignee":{"self":"https://klienti.atlassian.net/rest/api/2/user?accountId=557058%3Abf39af2f-2a15-473b-ba9b-fecfcab48e41","accountId":"557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41","avatarUrls":{"48x48":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/48","24x24":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/24","16x16":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/16","32x32":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/32"},"displayName":"Veniamin Smorodinsky","active":true,"timeZone":"Europe/Moscow","accountType":"atlassian"},"priority":{"self":"https://klienti.atlassian.net/rest/api/2/priority/3","iconUrl":"https://klienti.atlassian.net/images/icons/priorities/medium.svg","name":"Обычный","id":"3"},"status":{"self":"https://klienti.atlassian.net/rest/api/2/status/10003","description":"Выполненные задачи","iconUrl":"https://klienti.atlassian.net/","name":"Готово","id":"10003","statusCategory":{"self":"https://klienti.atlassian.net/rest/api/2/statuscategory/3","id":3,"key":"done","colorName":"green","name":"Готово"}}}}}';
        //$rawData='{"timestamp":1628701839609,"webhookEvent":"worklog_created","worklog":{"self":"https://klienti.atlassian.net/rest/api/2/issue/10531/worklog/10146","author":{"self":"https://klienti.atlassian.net/rest/api/2/user?accountId=557058%3Abf39af2f-2a15-473b-ba9b-fecfcab48e41","accountId":"557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41","avatarUrls":{"48x48":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/48","24x24":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/24","16x16":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/16","32x32":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/32"},"displayName":"Veniamin Smorodinsky","active":true,"timeZone":"Europe/Moscow","accountType":"atlassian"},"updateAuthor":{"self":"https://klienti.atlassian.net/rest/api/2/user?accountId=557058%3Abf39af2f-2a15-473b-ba9b-fecfcab48e41","accountId":"557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41","avatarUrls":{"48x48":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/48","24x24":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/24","16x16":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/16","32x32":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/32"},"displayName":"Veniamin Smorodinsky","active":true,"timeZone":"Europe/Moscow","accountType":"atlassian"},"created":"2021-08-11T20:10:39.609+0300","updated":"2021-08-11T20:10:39.609+0300","started":"2021-08-11T20:09:35.894+0300","timeSpent":"1m","timeSpentSeconds":60,"id":"10146","issueId":"10531"}}';

        $f = var_export($_REQUEST, true);
        file_put_contents('1.txt', $rawData);
        file_put_contents('2.txt', $f);
        $jsonData = json_decode($rawData, true);
        $json = json_decode($rawData);
        $f2 = var_export($jsonData, true);
        file_put_contents('3.txt', $f2);
//----------
        //dd($json);
        if ($json->webhookEvent=='worklog_created')
        {
            $issue_id=$json->worklog->issueId;
        }
        else {
            $issue_id=$json->issue->id;
        }

        $issue=JiraIssue::query()->where('issue_id','=',$issue_id)->first();

        if (!$issue) {
            if ($json->webhookEvent=='worklog_created') {continue;}

            $issue = JiraIssue::query()->create(
                [
                    'key' => $json->issue->key,
                    'issue_id' => $issue_id,
                    'event_created' => Carbon::createFromTimestamp($json->timestamp)->toDateTimeString(),
                    //'updateAuthor' => $json->updateAuthor,
                    'webhookEvent' => $json->webhookEvent,
                    'issue_url' => env('JIRA_URL') . 'browse/' . $json->issue->key,
                    'summary' => $json->issue->fields->summary,
                    'src' => $rawData,
                ]);
        }
        else {
            if ($json->webhookEvent=='worklog_created')
            {
                $issue->query()->where('issue_id', '=', $issue_id)
                    ->update([
                        'key' => $issue->key,
                        'issue_id' => $issue_id,
                        'event_created' => Carbon::createFromTimestamp($json->timestamp)->toDateTimeString(),
                        //'updateAuthor' => $json->updateAuthor,
                        'webhookEvent' => $json->webhookEvent,
                        'src' => $rawData,
                    ]);
            }
            else {
                $issue->query()->where('issue_id', '=', $issue_id)
                    ->update([
                        'key' => $json->issue->key,
                        'issue_id' => $issue_id,
                        'event_created' => Carbon::createFromTimestamp($json->timestamp)->toDateTimeString(),
                        //'updateAuthor' => $json->updateAuthor,
                        'webhookEvent' => $json->webhookEvent,
                        'issue_url' => env('JIRA_URL') . 'browse/' . $json->issue->key,
                        'summary' => ($json->webhookEvent == 'worklog_created') ? $issue->summary : $json->issue->fields->summary,
                        'src' => $rawData,
                    ]);
            }
        }

        $issue=JiraIssue::query()->where('issue_id','=',$issue_id)->first();

        Notification::send($issue, new MyTelegramNotification($issue));

    }
}
