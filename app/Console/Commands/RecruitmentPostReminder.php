<?php

namespace App\Console\Commands;

use Carbon\Carbon;
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
    protected $description = 'Dispatches a reminder to post the recruitment Reddit post.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(Discord $discord)
    {
        $days = [
            Carbon::MONDAY,
            Carbon::WEDNESDAY,
            Carbon::FRIDAY,
        ];

        if (in_array(now()->dayOfWeek, $days) && now()->hour === 18) {
            $discord->send(config('services.discord.channel_id'), [
                'content' => '<@' . config('services.discord.webmaster') . '> Post recruitment on /r/FindAUnit!',
                'embed' => null,
            ]);
        }
    }
}
