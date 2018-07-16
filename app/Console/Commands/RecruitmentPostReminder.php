<?php

namespace App\Console\Commands;

use App\Discord\Message;
use RestCord\DiscordClient;
use Illuminate\Console\Command;
use NotificationChannels\Discord\Discord;

class RecruitmentPostReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recruitment-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates the recruitment post with the latest footage.';

    /**
     * Discord client instance.
     *
     * @var \RestCord\DiscordClient
     */
    protected $discord;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(DiscordClient $discord)
    {
        parent::__construct();

        $this->discord = $discord;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(Discord $discord)
    {
        $messages = $this->getMessagesWithMedia();
        $videoLinks = [];

        foreach ($messages as $message) {
            foreach ($message->videos() as $video) {
                $videoLinks[] = "[{$video->title}]({$video->url})";
            }
        }

        $videoLinksText = implode(' | ', $videoLinks);
        $userToMention = config('services.discord.webmaster');

        $discord->send(config('services.discord.channel_id'), [
            'embed' => null,
            'content' => "
<@{$userToMention}> Post recruitment on https://www.reddit.com/r/FindAUnit\n
```
[A3][Recruiting][EU/US] ARCOMM - FPP, No Scopes, COOPs & PvPs

# About [ARCOMM](https://arcomm.co.uk/)
The goal of the our community is to engage players in an experience that only Arma can provide. We utilize conventional tactics as many other communities do, but we do so without the added fluff. We don’t do internet obstacle courses. There are **no ranks or saluting**; however, we do expect our members to be level-headed when it comes to our in-game attitude. Our community is international with suitable times for the American and EU time zones every **Saturday at 17:00 UTC (Zulu)** consisting of **1 PVP and 2 CO-OPs** which can take up to 5 hours to complete.

## Quick Rundown
- No respawns
- No magnified infantry optics
- Must be at least 18 years old
- Forced first person only
- Average of 25 players per week and growing
- Community runs on Discord, TeamSpeak and ARCHUB
- Base mods include ACE, ACRE, CUP, NIArms and various in-house mods
- Apex DLC is required
- No mandatory training after orientation
- Numerous other games played such as Rainbow Six Siege, PUBG, Squad and more!

## Latest Videos: {$videoLinksText}

# Want to find out more? Check us out at https://arcomm.co.uk
```
            "
        ]);
    }

    /**
     * Gets the latest (up to 100) messages that contain embedded media.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getMessagesWithMedia()
    {
        $messages = collect(
            $this->discord->channel->getChannelMessages([
                'limit' => 100,
                'channel.id' => config('services.discord.media_channel'),
            ])
        )->map(function ($message) {
            return new Message($message);
        });

        return $messages->filter(function ($message) {
            return $message->videos()->isNotEmpty()
                && $message->meetsReactionThreshold();
        })->values();
    }
}
