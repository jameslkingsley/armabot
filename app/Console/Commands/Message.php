<?php

namespace App\Console\Commands;

use RestCord\DiscordClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use NotificationChannels\Discord\Discord;

class Message extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send {message} {--channel=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends the given Discord message.';

    /**
     * Discord API client instance.
     *
     * @var \RestCord\DiscordClient
     */
    protected $client;

    /**
     * Constructor method.
     *
     * @return void
     */
    public function __construct(DiscordClient $client)
    {
        parent::__construct();

        $this->client = $client;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(Discord $discord)
    {
        $channel = $this->getChannelId(
            $this->option('channel') ?: config('services.discord.default_channel')
        );

        $discord->send($channel->id, [
            'content' => $this->argument('message'),
            'embed' => null,
        ]);
    }

    /**
     * Gets the channel ID from the name.
     *
     * @return string
     */
    public function getChannelId($name)
    {
        return Cache::remember('discord-channel-' . $name, 60 * 24 * 30, function () use ($name) {
            $channels = collect(
                $this->client->guild->getGuildChannels([
                    'guild.id' => (int)config('services.discord.server_id')
                ])
            );

            return $channels->where('name', $name)->first();
        });
    }
}
