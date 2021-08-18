<?php

namespace App\Handlers\Command;

use App\Models\Position;
use App\Models\Subscriber;
use Illuminate\Support\Facades\DB;
use WeStacks\TeleBot\Handlers\CommandHandler;
use WeStacks\TeleBot\Objects\Keyboard;
use WeStacks\TeleBot\Objects\KeyboardButton;

class SetPositionCommand extends BaseCommand
{
    public static $aliases = ['/set_position'];
    protected static $description = 'Выберать должность';

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
                'text' => "Ваша должность: {$position->name}",
            ]);
        }
    }

    public function handle()
    {
        parent::handle();

        $this->sub->waited_command = get_class($this).'::answerPosition';
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
}
