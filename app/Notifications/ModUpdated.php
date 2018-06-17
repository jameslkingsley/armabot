<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Discord\DiscordChannel;
use NotificationChannels\Discord\DiscordMessage;

class ModUpdated extends Notification
{
    use Queueable;

    /**
     * Attributes for the mod.
     *
     * @var object
     */
    protected $mod;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(array $mod)
    {
        $this->mod = (object) $mod;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [DiscordChannel::class];
    }

    /**
     * Get the Discord message for the notification.
     *
     * @param  mixed  $notifiable
     * @return \NotificationChannels\Discord\DiscordMessage
     */
    public function toDiscord($notifiable)
    {
        $version = $this->mod->version ? " ({$this->mod->version})" : '';

        return DiscordMessage::create("**{$this->mod->name}** has released a new version{$version}\n{$this->mod->url}");
    }
}
