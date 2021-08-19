<?php

namespace App\Handlers\Command;

use App\Models\Position;
use App\Models\Subscriber;
use App\Service\KeyboardService;
use Illuminate\Support\Facades\DB;
use WeStacks\TeleBot\Handlers\CommandHandler;
use WeStacks\TeleBot\Objects\Keyboard;
use WeStacks\TeleBot\Objects\KeyboardButton;
use WeStacks\TeleBot\Objects\Update;
use WeStacks\TeleBot\TeleBot;

class SetPositionCommand extends BaseCommand
{
    public static $aliases = ['/set_position'];
    protected static $description = 'Выбрать должность';
    protected $keyboardService;

    public function __construct(TeleBot $bot, Update $update)
    {
        parent::__construct($bot, $update);
        $this->keyboardService = app(KeyboardService::class);
    }

    public function answerPosition($text)
    {
        parent::handle();

        $position = Position::where('name', trim($text))->first();
        if (!$position) {
            $this->sendMessage([
                'text' => 'Ошибка, попробуйте снова!',
            ]);
        } elseif($this->sub) {
            $this->sub->waited_command = null;
            $this->sub->id_position = $position->id;
            $this->sub->save();

            $this->sendMessage([
                'text' => "Ваша должность: {$position->name} сохранена.",
                'reply_markup' => $this->keyboardService->removeKeyboard(),
            ]);
        }
    }

    public function handle()
    {
        parent::handle();

        $this->sub->waited_command = get_class($this) . '::answerPosition';
        $this->sub->save();

        $positions = Position::all()->pluck('name')->toArray();

        $this->sendMessage([
            'text' => "Выберите свою должность ( из представленных вариантов ).",
            'chat_id' => $this->update->message->chat->id,
            'disable_web_page_preview' => false,
            'reply_markup' => $this->keyboardService->makeKeyboard($positions),
        ]);
        return true;
    }
}
