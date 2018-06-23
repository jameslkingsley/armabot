<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use NotificationChannels\Discord\Discord;

class AttendanceReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatches a reminder every hour during operation to collect attendance.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(Discord $discord)
    {
        if (now()->dayOfWeek === Carbon::SATURDAY && now()->hour >= 17 && now()->hour <= 22) {
            $discord->send(config('services.discord.channel_id'), [
                'content' => '<@' . config('services.discord.webmaster') . '> Collect attendance!',
                'embed' => null,
            ]);
        }
    }
}
