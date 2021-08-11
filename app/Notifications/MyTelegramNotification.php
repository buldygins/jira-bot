<?php

namespace App\Notifications;

use App\Models\JiraIssue;
use Illuminate\Notifications\Notification;
use WeStacks\TeleBot\Laravel\TelegramNotification;

class MyTelegramNotification extends Notification
{

    /**
     * @var JiraIssue
     */
    private $issue;

    public function __construct(JiraIssue $issue)
    {
        $this->issue = $issue;
    }

    public function via($notifiable)
    {
        return ['telegram'];
    }

    public function toTelegram($notifiable)
    {
        if (!$notifiable->chat_id) {
            $notifiable->chat_id = env('TELEGRAM_DEFAULT_CHAT_ID');
        }
        // $this->issue->update(['event_processed'=>$this->issue->event_created]);
        JiraIssue::query()->where('id','=',$this->issue->id)->update([
            'event_processed'=>$this->issue->event_created
        ]);
        return (new TelegramNotification)->bot('bot')
            ->sendMessage([
                'parse_mode' => 'HTML',
                'disable_web_page_preview' => true,
//                'reply_markup' => [
//                    'keyboard'=>['one']
//                ],
                'chat_id' => $notifiable->chat_id,
                'text' =>
                    "<a href='{$this->issue->issue_url}'>{$this->issue->key}</a>" . "\r\n" .
                    $this->issue->webhookEvent . "\r\n" .
                    $this->issue->summary,
            ]);
    }
}
