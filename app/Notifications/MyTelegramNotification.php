<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use WeStacks\TeleBot\Laravel\TelegramNotification;

class MyTelegramNotification extends Notification
{
    public function via($notifiable)
    {
        return ['telegram'];
    }

    public function toTelegram($notifiable)
    {
        $notifiable->telegram_chat_id='1912458751';
        return (new TelegramNotification)->bot('bot')
            ->sendMessage([
                'chat_id' => $notifiable->telegram_chat_id,
                'text'    => "<a href='{$notifiable->issue_url}'>".$notifiable->key. '</a> '.$notifiable->summary,
            ]);
    }
}
