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

        $videoLinksText = implode(' &nbsp;&middot;&nbsp; ', $videoLinks);
        $userToMention = config('services.discord.webmaster');

        $discord->send(config('services.discord.channel_id'), [
            'embed' => null,
            'content' => "
<@{$userToMention}> Post recruitment on https://www.reddit.com/r/FindAUnit\n
```
[A3][Recruiting][EU/US] ARCOMM - Casual Community, Serious Arma

We are a welcoming community of like-minded gamers, established in 2015. We utilize conventional tactics, but we do so without the added fluff - we don’t do ranks or saluting - however, we do expect our members to be level-headed when it comes to our in-game attitude.

##### Community
* We play numerous other games such as Siege, DCS, Squad etc
* Community runs on Discord, TeamSpeak, and ARCHUB
* Apex DLC required
* Must be at least 18
* Welcoming of newcomers to Arma

##### Gameplay
* No respawns
* No magnified infantry optics
* First person perspective
* Base mods include ACE, ACRE, CUP, NIArms
* Average of 25 players per session

##### Schedule
* Every Saturday at 17:00 UTC (Zulu)
* Usually consists of 1 PVP and 2 CO-OPs
* Session can take up to 4 hours to complete
* Monthly group meetings

-

**Latest Videos:** {$videoLinksText}

-

**Want to find out more? Check us out at [https://arcomm.co.uk](https://arcomm.co.uk)**
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
                'channel.id' => (int) config('services.discord.media_channel'),
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
