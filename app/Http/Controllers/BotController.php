<?php

namespace App\Http\Controllers;

use App\Models\JiraIssue;
use App\Models\Log;
use App\Models\Subscriber;
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
        //$rawData = '{"issue": {"id": "10531", "key": "TALK-141", "self": "https://klienti.atlassian.net/rest/api/2/10531", "fields": {"status": {"id": "4", "name": "ПЕРЕОТКРЫТА", "self": "https://klienti.atlassian.net/rest/api/2/status/4", "iconUrl": "https://klienti.atlassian.net/images/icons/statuses/reopened.png", "description": "This issue was once resolved, but the resolution was deemed incorrect. From here issues are either marked assigned or resolved.", "statusCategory": {"id": 2, "key": "new", "name": "К выполнению", "self": "https://klienti.atlassian.net/rest/api/2/statuscategory/2", "colorName": "blue-gray"}}, "project": {"id": "10031", "key": "TALK", "name": "MyTalking", "self": "https://klienti.atlassian.net/rest/api/2/project/10031", "avatarUrls": {"16x16": "https://klienti.atlassian.net/secure/projectavatar?size=xsmall&s=xsmall&pid=10031&avatarId=10421", "24x24": "https://klienti.atlassian.net/secure/projectavatar?size=small&s=small&pid=10031&avatarId=10421", "32x32": "https://klienti.atlassian.net/secure/projectavatar?size=medium&s=medium&pid=10031&avatarId=10421", "48x48": "https://klienti.atlassian.net/secure/projectavatar?pid=10031&avatarId=10421"}, "simplified": false, "projectTypeKey": "software"}, "summary": "test", "assignee": {"self": "https://klienti.atlassian.net/rest/api/2/user?accountId=557058%3Abf39af2f-2a15-473b-ba9b-fecfcab48e41", "active": true, "timeZone": "Europe/Moscow", "accountId": "557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41", "avatarUrls": {"16x16": "https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/16", "24x24": "https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/24", "32x32": "https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/32", "48x48": "https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/48"}, "accountType": "atlassian", "displayName": "Veniamin Smorodinsky"}, "priority": {"id": "3", "name": "Обычный", "self": "https://klienti.atlassian.net/rest/api/2/priority/3", "iconUrl": "https://klienti.atlassian.net/images/icons/priorities/medium.svg"}, "issuetype": {"id": "10002", "name": "Задача", "self": "https://klienti.atlassian.net/rest/api/2/issuetype/10002", "iconUrl": "https://klienti.atlassian.net/secure/viewavatar?size=medium&avatarId=10318&avatarType=issuetype", "subtask": false, "avatarId": 10318, "description": "Небольшая порция работы.", "hierarchyLevel": 0}}}, "comment": {"id": "10293", "body": "3", "self": "https://klienti.atlassian.net/rest/api/2/issue/10531/comment/10293", "author": {"self": "https://klienti.atlassian.net/rest/api/2/user?accountId=557058%3Abf39af2f-2a15-473b-ba9b-fecfcab48e41", "active": true, "timeZone": "Europe/Moscow", "accountId": "557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41", "avatarUrls": {"16x16": "https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/16", "24x24": "https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/24", "32x32": "https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/32", "48x48": "https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/48"}, "accountType": "atlassian", "displayName": "Veniamin Smorodinsky"}, "created": "2021-08-11T21:00:22.287+0300", "updated": "2021-08-11T21:00:22.287+0300", "jsdPublic": true, "updateAuthor": {"self": "https://klienti.atlassian.net/rest/api/2/user?accountId=557058%3Abf39af2f-2a15-473b-ba9b-fecfcab48e41", "active": true, "timeZone": "Europe/Moscow", "accountId": "557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41", "avatarUrls": {"16x16": "https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/16", "24x24": "https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/24", "32x32": "https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/32", "48x48": "https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/48"}, "accountType": "atlassian", "displayName": "Veniamin Smorodinsky"}}, "timestamp": 1628708131809, "webhookEvent": "comment_deleted"}';
        //$rawData='{"timestamp":1628764096450,"webhookEvent":"jira:issue_updated","issue_event_type_name":"issue_updated","user":{"self":"https://klienti.atlassian.net/rest/api/2/user?accountId=557058%3Abf39af2f-2a15-473b-ba9b-fecfcab48e41","accountId":"557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41","avatarUrls":{"48x48":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/48","24x24":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/24","16x16":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/16","32x32":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/32"},"displayName":"Veniamin Smorodinsky","active":true,"timeZone":"Europe/Moscow","accountType":"atlassian"},"issue":{"id":"10509","self":"https://klienti.atlassian.net/rest/api/2/10509","key":"TALK-132","fields":{"statuscategorychangedate":"2021-08-04T17:46:29.208+0300","issuetype":{"self":"https://klienti.atlassian.net/rest/api/2/issuetype/10004","id":"10004","description":"A problem which impairs or prevents the functions of the product.","iconUrl":"https://klienti.atlassian.net/secure/viewavatar?size=medium&avatarId=10303&avatarType=issuetype","name":"Bug","subtask":false,"avatarId":10303,"hierarchyLevel":0},"timespent":180,"customfield_10030":null,"project":{"self":"https://klienti.atlassian.net/rest/api/2/project/10031","id":"10031","key":"TALK","name":"MyTalking","projectTypeKey":"software","simplified":false,"avatarUrls":{"48x48":"https://klienti.atlassian.net/secure/projectavatar?pid=10031&avatarId=10421","24x24":"https://klienti.atlassian.net/secure/projectavatar?size=small&s=small&pid=10031&avatarId=10421","16x16":"https://klienti.atlassian.net/secure/projectavatar?size=xsmall&s=xsmall&pid=10031&avatarId=10421","32x32":"https://klienti.atlassian.net/secure/projectavatar?size=medium&s=medium&pid=10031&avatarId=10421"}},"customfield_10031":null,"customfield_10032":null,"fixVersions":[],"customfield_10033":null,"aggregatetimespent":180,"customfield_10034":null,"resolution":null,"customfield_10035":null,"customfield_10036":null,"customfield_10037":null,"customfield_10027":null,"customfield_10028":{"self":"https://klienti.atlassian.net/rest/api/2/user?accountId=557058%3Abf39af2f-2a15-473b-ba9b-fecfcab48e41","accountId":"557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41","avatarUrls":{"48x48":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/48","24x24":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/24","16x16":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/16","32x32":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/32"},"displayName":"Veniamin Smorodinsky","active":true,"timeZone":"Europe/Moscow","accountType":"atlassian"},"customfield_10029":null,"resolutiondate":null,"workratio":-1,"issuerestriction":{"issuerestrictions":{},"shouldDisplay":false},"lastViewed":"2021-08-12T13:26:22.113+0300","watches":{"self":"https://klienti.atlassian.net/rest/api/2/issue/TALK-132/watchers","watchCount":1,"isWatching":true},"created":"2021-08-03T20:47:49.173+0300","customfield_10020":null,"customfield_10021":null,"customfield_10022":"0|i0022z:","priority":{"self":"https://klienti.atlassian.net/rest/api/2/priority/3","iconUrl":"https://klienti.atlassian.net/images/icons/priorities/medium.svg","name":"Обычный","id":"3"},"customfield_10025":null,"labels":[],"customfield_10026":null,"customfield_10016":null,"customfield_10017":null,"customfield_10018":{"hasEpicLinkFieldDependency":false,"showField":false,"nonEditableReason":{"reason":"PLUGIN_LICENSE_ERROR","message":"Ссылка на родителя доступна только пользователям Jira Premium."}},"customfield_10019":null,"aggregatetimeoriginalestimate":null,"timeestimate":0,"versions":[],"issuelinks":[],"assignee":{"self":"https://klienti.atlassian.net/rest/api/2/user?accountId=557058%3Abf39af2f-2a15-473b-ba9b-fecfcab48e41","accountId":"557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41","avatarUrls":{"48x48":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/48","24x24":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/24","16x16":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/16","32x32":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/32"},"displayName":"Veniamin Smorodinsky","active":true,"timeZone":"Europe/Moscow","accountType":"atlassian"},"updated":"2021-08-12T13:28:16.386+0300","status":{"self":"https://klienti.atlassian.net/rest/api/2/status/10003","description":"","iconUrl":"https://klienti.atlassian.net/","name":"Done","id":"10003","statusCategory":{"self":"https://klienti.atlassian.net/rest/api/2/statuscategory/3","id":3,"key":"done","colorName":"green","name":"Complete"}},"components":[],"timeoriginalestimate":null,"description":"Вылетает JSON. Нужно - редирект на главную. \\n\\ntest","customfield_10010":null,"customfield_10014":null,"timetracking":{"remainingEstimate":"0m","timeSpent":"3m","remainingEstimateSeconds":0,"timeSpentSeconds":180},"customfield_10015":null,"customfield_10005":null,"customfield_10006":null,"customfield_10007":null,"security":null,"customfield_10008":null,"customfield_10009":null,"aggregatetimeestimate":0,"attachment":[],"summary":"Разлогин на странице чатов","creator":{"self":"https://klienti.atlassian.net/rest/api/2/user?accountId=557058%3Abf39af2f-2a15-473b-ba9b-fecfcab48e41","accountId":"557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41","avatarUrls":{"48x48":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/48","24x24":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/24","16x16":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/16","32x32":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/32"},"displayName":"Veniamin Smorodinsky","active":true,"timeZone":"Europe/Moscow","accountType":"atlassian"},"subtasks":[],"reporter":{"self":"https://klienti.atlassian.net/rest/api/2/user?accountId=557058%3Abf39af2f-2a15-473b-ba9b-fecfcab48e41","accountId":"557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41","avatarUrls":{"48x48":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/48","24x24":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/24","16x16":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/16","32x32":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/32"},"displayName":"Veniamin Smorodinsky","active":true,"timeZone":"Europe/Moscow","accountType":"atlassian"},"customfield_10000":"{}","aggregateprogress":{"progress":180,"total":180,"percent":100},"customfield_10001":null,"customfield_10002":null,"customfield_10003":null,"customfield_10004":null,"environment":null,"duedate":null,"progress":{"progress":180,"total":180,"percent":100},"votes":{"self":"https://klienti.atlassian.net/rest/api/2/issue/TALK-132/votes","votes":0,"hasVoted":false}}},"changelog":{"id":"15427","items":[{"field":"description","fieldtype":"jira","fieldId":"description","from":"{\\"id\\":\\"ari:cloud:jira:dadb0c0a-d30a-43e1-bd21-7fa1026b53f8:issuefieldvalue/10509/description\\",\\"version\\":\\"1\\"}","fromString":"Вылетает JSON. Нужно - редирект на главную. ","to":"{\\"id\\":\\"ari:cloud:jira:dadb0c0a-d30a-43e1-bd21-7fa1026b53f8:issuefieldvalue/10509/description\\",\\"version\\":\\"2\\"}","toString":"Вылетает JSON. Нужно - редирект на главную. \\n\\ntest"}]}}';
        //$rawData='{"timestamp":1628765428302,"webhookEvent":"worklog_created","worklog":{"self":"https://klienti.atlassian.net/rest/api/2/issue/10388/worklog/10152","author":{"self":"https://klienti.atlassian.net/rest/api/2/user?accountId=557058%3Abf39af2f-2a15-473b-ba9b-fecfcab48e41","accountId":"557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41","avatarUrls":{"48x48":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/48","24x24":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/24","16x16":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/16","32x32":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/32"},"displayName":"Veniamin Smorodinsky","active":true,"timeZone":"Europe/Moscow","accountType":"atlassian"},"updateAuthor":{"self":"https://klienti.atlassian.net/rest/api/2/user?accountId=557058%3Abf39af2f-2a15-473b-ba9b-fecfcab48e41","accountId":"557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41","avatarUrls":{"48x48":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/48","24x24":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/24","16x16":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/16","32x32":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/32"},"displayName":"Veniamin Smorodinsky","active":true,"timeZone":"Europe/Moscow","accountType":"atlassian"},"created":"2021-08-12T13:50:28.302+0300","updated":"2021-08-12T13:50:28.302+0300","started":"2021-08-12T13:49:13.949+0300","timeSpent":"1m","timeSpentSeconds":60,"id":"10152","issueId":"10388"}}';
        //$rawData='{"timestamp":1628767758245,"webhookEvent":"comment_created","comment":{"self":"https://klienti.atlassian.net/rest/api/2/issue/10484/comment/10314","id":"10314","author":{"self":"https://klienti.atlassian.net/rest/api/2/user?accountId=557058%3Abf39af2f-2a15-473b-ba9b-fecfcab48e41","accountId":"557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41","avatarUrls":{"48x48":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/48","24x24":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/24","16x16":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/16","32x32":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/32"},"displayName":"Veniamin Smorodinsky","active":true,"timeZone":"Europe/Moscow","accountType":"atlassian"},"body":"3","updateAuthor":{"self":"https://klienti.atlassian.net/rest/api/2/user?accountId=557058%3Abf39af2f-2a15-473b-ba9b-fecfcab48e41","accountId":"557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41","avatarUrls":{"48x48":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/48","24x24":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/24","16x16":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/16","32x32":"https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/557058:bf39af2f-2a15-473b-ba9b-fecfcab48e41/381ac727-55c2-48db-ac74-24f7c412762e/32"},"displayName":"Veniamin Smorodinsky","active":true,"timeZone":"Europe/Moscow","accountType":"atlassian"},"created":"2021-08-12T14:29:18.245+0300","updated":"2021-08-12T14:29:18.245+0300","jsdPublic":true},"issue":{"id":"10484","self":"https://klienti.atlassian.net/rest/api/2/10484","key":"TALK-111","fields":{"summary":"Количество непрочитанных в аккаунте","issuetype":{"self":"https://klienti.atlassian.net/rest/api/2/issuetype/10002","id":"10002","description":"Небольшая порция работы.","iconUrl":"https://klienti.atlassian.net/secure/viewavatar?size=medium&avatarId=10318&avatarType=issuetype","name":"Задача","subtask":false,"avatarId":10318,"hierarchyLevel":0},"project":{"self":"https://klienti.atlassian.net/rest/api/2/project/10031","id":"10031","key":"TALK","name":"MyTalking","projectTypeKey":"software","simplified":false,"avatarUrls":{"48x48":"https://klienti.atlassian.net/secure/projectavatar?pid=10031&avatarId=10421","24x24":"https://klienti.atlassian.net/secure/projectavatar?size=small&s=small&pid=10031&avatarId=10421","16x16":"https://klienti.atlassian.net/secure/projectavatar?size=xsmall&s=xsmall&pid=10031&avatarId=10421","32x32":"https://klienti.atlassian.net/secure/projectavatar?size=medium&s=medium&pid=10031&avatarId=10421"}},"assignee":{"self":"https://klienti.atlassian.net/rest/api/2/user?accountId=60e55212f90dee00694cff7e","accountId":"60e55212f90dee00694cff7e","avatarUrls":{"48x48":"https://secure.gravatar.com/avatar/dab68af54cc1eb6bdf14575a5a389269?d=https%3A%2F%2Favatar-management--avatars.us-west-2.prod.public.atl-paas.net%2Fdefault-avatar-6.png","24x24":"https://secure.gravatar.com/avatar/dab68af54cc1eb6bdf14575a5a389269?d=https%3A%2F%2Favatar-management--avatars.us-west-2.prod.public.atl-paas.net%2Fdefault-avatar-6.png","16x16":"https://secure.gravatar.com/avatar/dab68af54cc1eb6bdf14575a5a389269?d=https%3A%2F%2Favatar-management--avatars.us-west-2.prod.public.atl-paas.net%2Fdefault-avatar-6.png","32x32":"https://secure.gravatar.com/avatar/dab68af54cc1eb6bdf14575a5a389269?d=https%3A%2F%2Favatar-management--avatars.us-west-2.prod.public.atl-paas.net%2Fdefault-avatar-6.png"},"displayName":"Даниил","active":true,"timeZone":"Europe/Moscow","accountType":"atlassian"},"priority":{"self":"https://klienti.atlassian.net/rest/api/2/priority/3","iconUrl":"https://klienti.atlassian.net/images/icons/priorities/medium.svg","name":"Обычный","id":"3"},"status":{"self":"https://klienti.atlassian.net/rest/api/2/status/10003","description":"Выполненные задачи","iconUrl":"https://klienti.atlassian.net/","name":"Готово","id":"10003","statusCategory":{"self":"https://klienti.atlassian.net/rest/api/2/statuscategory/3","id":3,"key":"done","colorName":"green","name":"Готово"}}}}}';

        $f = var_export($_REQUEST, true);
        file_put_contents('1.txt', $rawData);
        file_put_contents('2.txt', $f);
        $jsonData = json_decode($rawData, true);
        $json = json_decode($rawData);
        $f2 = var_export($jsonData, true);
        file_put_contents('3.txt', $f2);
//----------

        //dd($json);
        $webhook_parts = explode('_', $json->webhookEvent);

        if ($json->webhookEvent == 'worklog_created') {
            $issue_id = $json->worklog->issueId;
        } else {
            $issue_id = $json->issue->id;
        }

        $log_message = '';
        if ($webhook_parts[0] == 'worklog') {
//            $worklog_message = "Запись о работе #" . $json->worklog->id . ' {action} ' . $json->worklog->author->displayName . " " .
//                Carbon::createFromTimeString($json->worklog->created)->toDateTimeString(). ' '.$json->worklog->timeSpent;

            $worklog_message = $json->worklog->author->displayName . ' {action} запись о работе ' . $json->worklog->timeSpent . " " .
                Carbon::createFromTimeString($json->worklog->created)->toDateString();

            if ($json->webhookEvent == 'worklog_created') {
                $log_message = str_replace('{action}', 'была добавлена', $worklog_message);
            }

            if ($json->webhookEvent == 'worklog_updated') {
                $log_message = str_replace('{action}', 'была изменена', $worklog_message);
            }

            if ($json->webhookEvent == 'worklog_deleted') {
                $log_message = str_replace('{action}', 'была удалена', $worklog_message);
            }
        }

        if ($webhook_parts[0] == 'comment') {
            $comment_message = "Комментарий #" . $json->comment->id . ' {action} ' . $json->comment->updateAuthor->displayName . "\r\n\r\n" .
                "------\r\n" . $json->comment->body;

            if ($json->webhookEvent == 'comment_created') {
                $log_message = str_replace('{action}', 'был добавлен', $comment_message);
            }

            if ($json->webhookEvent == 'comment_updated') {
                $log_message = str_replace('{action}', 'был изменен', $comment_message);
            }

            if ($json->webhookEvent == 'comment_deleted') {
                $log_message = str_replace('{action}', 'был удален', $comment_message);
            }
        }

        if ($webhook_parts[0] == 'jira:issue') {
            $task_message = "Задача {action} " . $json->user->displayName; //. "\r\n\r\n"
            //"------\r\n".$json->comment->body;

            if ($json->webhookEvent == 'jira:issue_created') {
                $log_message = str_replace('{action}', 'была создана', $task_message);
            }

            if ($json->webhookEvent == 'jira:issue_updated') {
                $log_message = str_replace('{action}', 'была изменена', $task_message);
            }
            if ($json->webhookEvent == 'jira:issue_deleted') {
                $log_message = str_replace('{action}', 'была удалена', $task_message);
            }
        }

        $issue = JiraIssue::query()->where('issue_id', '=', $issue_id)->first();

        //dd($log_message);

        if (!$issue) {
//            if ($json->webhookEvent == 'worklog_created') {
//                return false;
//            }

            if (isset($json->issue->key)) {
                $issue_key = $json->issue->key;
            } else {
                $issue_key = "NOT-01";
            }

            $issue = JiraIssue::query()->create(
                [
                    'key' => $issue_key,
                    'issue_id' => $issue_id,
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
                'webhook_event' => $json->webhookEvent,
                'name' => $log_message,
                'src' => $rawData,
            ]);
        } else {

            Log::create([
                'issue_id' => $issue_id,
                'issue_key' => $issue->key,
                'webhook_event' => $json->webhookEvent,
                'name' => $log_message,
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
        //dd($issue);

        if ($issue->event_created != $issue->event_processed) {
            $subscribers = Subscriber::where('is_active', '=', true)->get();
            foreach ($subscribers as $subscriber) {
                //dd($subscriber);
                Notification::send($subscriber, new MyTelegramNotification($issue, $log_message));
            }
        }
    }
}
