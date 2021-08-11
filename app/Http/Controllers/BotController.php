<?php

namespace App\Http\Controllers;

use App\Notifications\TelegramNotification;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
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

    public function jira()
    {
        $rawData = file_get_contents("php://input");
        $rawData = '{"timestamp":1628677146887,"webhookEvent":"comment_created","comment":{"self":"https://klienti.atlassian.net/rest/api/2/issue/10509/comment/10274","id":"10274","author":{"self":"https://klienti.atlassian.net/rest/api/2/user?accountId=557058%3Abf39af2f-2a15-473b-ba9b-fecfcab48e41","accountId":"557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41","avatarUrls":{"48x48":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/48","24x24":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/24","16x16":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/16","32x32":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/32"},"displayName":"Veniamin Smorodinsky","active":true,"timeZone":"Europe/Moscow","accountType":"atlassian"},"body":"ttt","updateAuthor":{"self":"https://klienti.atlassian.net/rest/api/2/user?accountId=557058%3Abf39af2f-2a15-473b-ba9b-fecfcab48e41","accountId":"557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41","avatarUrls":{"48x48":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/48","24x24":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/24","16x16":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/16","32x32":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/32"},"displayName":"Veniamin Smorodinsky","active":true,"timeZone":"Europe/Moscow","accountType":"atlassian"},"created":"2021-08-11T13:19:06.887+0300","updated":"2021-08-11T13:19:06.887+0300","jsdPublic":true},"issue":{"id":"10509","self":"https://klienti.atlassian.net/rest/api/2/10509","key":"TALK-132","fields":{"summary":"Разлогин на странице чатов","issuetype":{"self":"https://klienti.atlassian.net/rest/api/2/issuetype/10004","id":"10004","description":"Проблема или ошибка.","iconUrl":"https://klienti.atlassian.net/secure/viewavatar?size=medium&avatarId=10303&avatarType=issuetype","name":"Баг","subtask":false,"avatarId":10303,"hierarchyLevel":0},"project":{"self":"https://klienti.atlassian.net/rest/api/2/project/10031","id":"10031","key":"TALK","name":"MyTalking","projectTypeKey":"software","simplified":false,"avatarUrls":{"48x48":"https://klienti.atlassian.net/secure/projectavatar?pid=10031&avatarId=10421","24x24":"https://klienti.atlassian.net/secure/projectavatar?size=small&s=small&pid=10031&avatarId=10421","16x16":"https://klienti.atlassian.net/secure/projectavatar?size=xsmall&s=xsmall&pid=10031&avatarId=10421","32x32":"https://klienti.atlassian.net/secure/projectavatar?size=medium&s=medium&pid=10031&avatarId=10421"}},"assignee":{"self":"https://klienti.atlassian.net/rest/api/2/user?accountId=557058%3Abf39af2f-2a15-473b-ba9b-fecfcab48e41","accountId":"557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41","avatarUrls":{"48x48":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/48","24x24":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/24","16x16":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/16","32x32":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/32"},"displayName":"Veniamin Smorodinsky","active":true,"timeZone":"Europe/Moscow","accountType":"atlassian"},"priority":{"self":"https://klienti.atlassian.net/rest/api/2/priority/3","iconUrl":"https://klienti.atlassian.net/images/icons/priorities/medium.svg","name":"Обычный","id":"3"},"status":{"self":"https://klienti.atlassian.net/rest/api/2/status/10003","description":"Выполненные задачи","iconUrl":"https://klienti.atlassian.net/","name":"Готово","id":"10003","statusCategory":{"self":"https://klienti.atlassian.net/rest/api/2/statuscategory/3","id":3,"key":"done","colorName":"green","name":"Готово"}}}}}';
        $f = var_export($_REQUEST, true);
        file_put_contents('1.txt', $rawData);
        file_put_contents('2.txt', $f);
        $jsonData = json_decode($rawData, true);
        $json = json_decode($rawData);
        $f2 = var_export($jsonData, true);
        file_put_contents('3.txt', $f2);
//----------
        print_r($json->issue->key);
        Notification::send('1111@mail.ru', new TelegramNotification());

    }
}
