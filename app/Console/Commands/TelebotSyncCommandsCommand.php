<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use WeStacks\TeleBot\Objects\User;
use WeStacks\TeleBot\TeleBot;

class TelebotSyncCommandsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telebot:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $bot = new TeleBot([
            'token' => config('telebot.bots.bot.token'),
            'api_url' => 'https://api.telegram.org',
            'exceptions' => true,
            'async' => false,
            'handlers' => []
        ]);

        dd($bot->getLocalCommands());

        $bot->setMyCommands([
            'commands' => $bot->getLocalCommands()
        ]);

        $this->info('successfull');
    }
}
