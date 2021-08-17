<?php

namespace App\Notifications;

use App\Models\JiraIssue;
use App\Service\KeyboardService;
use Illuminate\Notifications\Notification;
use WeStacks\TeleBot\Laravel\TelegramNotification;

class MyTelegramNotification extends Notification
{

    /**
     * @var JiraIssue
     */
    private $issue;
    /**
     * @var string
     */
    private $message_body;
    /**
     * @var string
     */
    private $message_header;
    /**
     * @var mixed|null
     */
    private $image;

    private $keyboardService;
    private $notifiable;

    public function __construct(JiraIssue $issue, array $data = [])
    {
        $this->keyboardService = app(KeyboardService::class);

        $this->issue = $issue;
        $this->message_header = $data['log_message_header'];
        $this->message_body = $data['log_message_body'];

        $this->image = $data['image'] ?? [];
    }

    public function via($notifiable)
    {
        $this->notifiable=$notifiable;
        return ['telegram'];
    }

    private function getInlineKeyBoard($data)
    {
        $inlineKeyboard = array(
            "inline_keyboard" => $data,
        );
        return json_encode($inlineKeyboard);
    }

    private function getKeyBoard($data)
    {
        return $this->keyboardService->makeKeyboard($data);
    }

    public function toTelegram($notifiable)
    {
        if (!$notifiable->chat_id) {
            $notifiable->chat_id = env('TELEGRAM_DEFAULT_CHAT_ID');
        }
        // $this->issue->update(['event_processed'=>$this->issue->event_created]);
        JiraIssue::query()->where('id', '=', $this->issue->id)->update([
            'event_processed' => $this->issue->event_created
        ]);

        $notification = (new TelegramNotification)->bot('bot')
            ->sendMessage([
                'parse_mode' => 'HTML',
                'disable_web_page_preview' => true,
                'chat_id' => $notifiable->chat_id,
                'reply_markup' => $this->keyboardService->makeInlineKeyBoard([['text' => 'list','callback_data' => '/list',]]),
                'text' => view('telegram.notification', [
                    'issue' => $this->issue,
                    'message_header' => $this->message_header,
                    'message_body' => $this->message_body
                ])->render()]);
//        if (!empty($this->image)) {
//            foreach ($this->image as $img) {
//                $notification->sendPhoto(
//                    [
//                        'chat_id' => $notifiable->chat_id,
//                        'photo' => fopen($img, 'r'),
//                    ]
//                );
//            }
//        }

        return $notification;
    }
}
