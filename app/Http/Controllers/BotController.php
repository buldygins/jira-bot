<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use WeStacks\TeleBot\TeleBot;
use WeStacks\TeleBot\Objects\User;

class BotController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function index()
    {
        $bot = new TeleBot([
            'token'      => env('TELEGRAM_BOT_TOKEN'),
            'api_url'    => 'https://api.telegram.org',
            'exceptions' => true,
            'async'      => false,
            'handlers'   => []
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
}
