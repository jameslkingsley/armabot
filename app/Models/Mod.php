<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Mod extends Model
{
    use Notifiable;

    /**
     * Guarded attributes.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Gets the Discord channel ID.
     *
     * @return string
     */
    public function routeNotificationForDiscord()
    {
        return config('services.discord.channel_id');
    }
}
