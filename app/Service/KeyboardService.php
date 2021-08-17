<?php

namespace App\Service;

use WeStacks\TeleBot\Objects\Keyboard;
use WeStacks\TeleBot\Objects\KeyboardButton;

class KeyboardService
{
    public function removeKeyboard(){
        return Keyboard::create([
            'remove_keyboard' =>  true,
        ]);
    }

    public function makeKeyboard($buttons, $buttons_per_row = 3){
        $i = 0;
        $keyboard_buttons = [];
        foreach ($buttons as $button) {
            if (isset($keyboard_buttons[$i]) && count($keyboard_buttons[$i]) >= $buttons_per_row) {
                $i++;
            }
            $keyboard_buttons[$i][] = new KeyboardButton(['text' => $button]);
        }
        return Keyboard::create([
            'keyboard' => $keyboard_buttons,
            'resize_keyboard' => true,
            'one_time_keyboard' => true,
        ]);
    }
}
