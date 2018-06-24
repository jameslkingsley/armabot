<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use NotificationChannels\Discord\Discord;

class Message extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send {message}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends the given Discord message.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(Discord $discord)
    {
        $discord->send(config('services.discord.channel_id'), [
            'content' => $this->argument('message'),
            'embed' => null,
        ]);
    }
}
