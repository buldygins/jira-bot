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

class JiraAuthCommand extends BaseCommand
{
    public static $aliases = ['/jira_auth'];
    protected static $description = 'Авторизоваться для совершения действий';
    protected static $cancelAuth = 'Закончить регистрацию.';
    protected $keyboardService;

    public function __construct(TeleBot $bot, Update $update)
    {
        parent::__construct($bot, $update);
        $this->keyboardService = new KeyboardService();
    }

    public function handle()
    {
        parent::handle();

        if ($this->sub->canSendCommands()) {
            $this->sendMessage([
                'text' => "Вы уже зарегистрированы.",
                'chat_id' => $this->update->message->chat->id,
            ]);
            return true;
        }

        $this->sub->waited_command = get_class($this) . '::answerFio';
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

        $this->sub->waited_command = get_class($this) . '::answerPosition';
        $this->sub->fio = trim($text);
        $this->sub->save();

        $positions = Position::all()->pluck('name');

        $this->sendMessage([
            'text' => "Выберите свою должность.",
            'chat_id' => $this->update->message->chat->id,
            'disable_web_page_preview' => false,
            'reply_markup' => $this->keyboardService->makeKeyboard(array_merge($positions,[self::$cancelAuth])),
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
        $this->sub->waited_command = get_class($this) . '::answerLogin';
        $this->sub->save();

        $this->sendMessage([
            'text' => "Ваша должность: {$position->name}. Отправьте логин Jira.",
            'chat_id' => $this->update->message->chat->id,
            'reply_markup' => $this->keyboardService->makeKeyboard([self::$cancelAuth]),
        ]);
        return true;
    }

    public function answerLogin($text)
    {
        parent::handle();

        if ($this->checkCancel($text)) {
            return true;
        }
        dump('aswerLogin');
        $this->sub->jira_login = trim($text);
        $this->sub->waited_command = get_class($this) . '::answerLoginAndToken';
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
        dump('aswerLoginAndToken');

        parent::handle();

        dump('aswerLoginAndToken1');

        if ($this->checkCancel($text)) {
            return true;
        }

        dump('aswerLoginAndToken2');

        $this->sub->api_token = trim($text);
        $this->sub->waited_command = null;
        $this->sub->save();

        dump('aswerLoginAndToken3');

        try {
            dump(1);
            $userService = new UserService(new ArrayConfiguration($this->getJiraArrayConfiguration()));
            dump(2);
            $myself = $userService->getMyself();
            dump(3);
            $data = [
                'key' => $myself->key ?? null,
                'name' => $myself->name ?? null,
                'accountId' => $myself->accountId ?? null,
                'active' => $myself->active ?? true,
                'timeZone' => $myself->timeZone ?? 'Europe/Moscow',
                'displayName' => $myself->displayName ?? null,
            ];
            dump(4);
            $jira_user = JiraUser::query()->firstOrCreate($data);
            dump(5);
            if (!$jira_user) {
                throw new \Exception('User was not created. Data : ' . json_encode($data));
            }
            dump(6);
            $this->sub->jira_user_id = $jira_user->id;
            $this->sub->save();
            dump(7);
        } catch (\Exception $e) {
            dump(8);
            $this->sendMessage([
                'text' => "👎Регистрация не пройдена! Проверьте провильность данных!",
                'chat_id' => $this->update->message->chat->id,
                'reply_markup' => $this->keyboardService->removeKeyboard(),
            ]);
            Log::channel('telegram_log')->alert($e->getMessage());
            return true;
        }

        $this->sendMessage([
            'text' => "👍Регистрация успешна! Теперь вы можете выполнять действия из телеграм бота.",
            'chat_id' => $this->update->message->chat->id,
            'reply_markup' => $this->keyboardService->removeKeyboard(),
        ]);
        return true;
    }


    public function checkCancel($text)
    {
        if (strpos($text, self::$cancelAuth) !== false) {
            $this->sendMessage([
                'parse_mode' => 'HTML',
                'text' => "Регистрация завершена.",
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
