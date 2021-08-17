<?php

namespace App\Handlers\Command;

use App\Models\Position;
use App\Models\Subscriber;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use JiraRestApi\Configuration\ArrayConfiguration;
use JiraRestApi\JiraException;
use JiraRestApi\User\UserService;
use WeStacks\TeleBot\Handlers\CommandHandler;
use WeStacks\TeleBot\Objects\Keyboard;
use WeStacks\TeleBot\Objects\KeyboardButton;

class JiraAuthCommand extends BaseCommand
{
    protected static $aliases = ['/jira_auth'];
    protected static $description = 'Авторизоваться для совершения действий';
    protected static $cancelAuth = 'Закончить регистрацию.';

    public function handle()
    {
        parent::handle();

        $this->sub->waited_command = 'AuthCommand::answerFio';
        $this->sub->save();

        $this->sendMessage([
            'text' => "Задайте свои ФИО",
            'chat_id' => $this->update->message->chat->id,
        ]);
        return true;
    }

    public function answerFio($text)
    {
        parent::handle();

        $this->sub->waited_command = 'AuthCommand::answerPosition';
        $this->sub->save();

        $keyboard_buttons = [];
        $positions = Position::query()->get();
        $i = 0;
        foreach ($positions as $position) {
            if (isset($keyboard_buttons[$i]) && count($keyboard_buttons[$i]) >= 3) {
                $i++;
            }
            $keyboard_buttons[$i][] = new KeyboardButton(['text' => $position->name]);
        }
        $keyboard = Keyboard::create([
            'keyboard' => $keyboard_buttons,
            'resize_keyboard' => true,
            'one_time_keyboard' => true,
        ]);

        $this->sendMessage([
            'text' => "Выберите свою должность.",
            'chat_id' => $this->update->message->chat->id,
            'disable_web_page_preview' => false,
            'reply_markup' => $keyboard,
        ]);
        return true;
    }

    public function AnswerPosition($text)
    {
        parent::handle();

        $position = Position::where('name', trim($text))->first();
        if (!$position) {
            $this->sendMessage([
                'text' => 'Ошибка, попробуйте снова!',
            ]);
            return true;
        }

        $this->sub->id_position = $position->id;
        $this->sub->waited_command = 'AuthCommand::answerLogin';
        $this->sub->save();

        $keyboard = Keyboard::create([
            'keyboard' => [[new KeyboardButton(['text' => self::$cancelAuth])]],
            'resize_keyboard' => true,
            'one_time_keyboard' => true,
        ]);

        $this->sendMessage([
            'text' => "Ваша должность: {$position->name}. Отправьте логин Jira.",
            'chat_id' => $this->update->message->chat->id,
            'reply_markup' => $keyboard,
        ]);
        return true;
    }

    public function answerLogin($text)
    {
        $this->checkCancel($text);

        parent::handle();

        $this->sub->jira_login = trim($text);
        $this->sub->waited_command = 'AuthCommand::answerLoginAndToken';
        $this->sub->save();

        $link = env('JIRA_URL') . 'secure/ViewProfile.jspa?selectedTab=com.atlassian.pats.pats-plugin:jira-user-personal-access-tokens';

        $this->sendMessage([
            'parse_mode' => 'HTML',
            'text' => "Отправьте  токен авторизации.\r\nТокен авторизации можно получить <a href='{$link}' style='margin-right: 5px;'>тут</a>.",
            'chat_id' => $this->update->message->chat->id,
        ]);
        return true;
    }

    public function answerLoginAndToken($text)
    {
        $this->checkCancel($text);

        parent::handle();

        $this->sub->api_token = trim($text);
        $this->sub->waited_command = null;
        $this->sub->save();

        try {
            $userService = new UserService(new ArrayConfiguration([
                'jiraHost' => env('JIRA_URL'),
                'jiraUser' => $this->sub->jira_login,
                'jiraPassword' => $this->sub->api_token,
            ]));
        } catch (JiraException $e) {
            Log::channel('telegram_request')->alert("Trying to auth {$this->sub->jira_login} with token {$this->sub->api_token}" . $e->getMessage());
        }

        $this->sendMessage([
            'parse_mode' => 'HTML',
            'text' => "Регистрация успешна! Теперь вы можете выполнять действия из телеграм бота.",
            'chat_id' => $this->update->message->chat->id,
        ]);
        return true;
    }



    public function checkCancel($text){
        if (strpos($text, self::$cancelAuth) !== false) {
            $this->sendMessage([
                'parse_mode' => 'HTML',
                'text' => "Регистрация завершена.",
                'chat_id' => $this->update->message->chat->id,
            ]);
            exit();
        }
    }
}
