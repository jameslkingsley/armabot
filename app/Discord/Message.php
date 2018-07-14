<?php

namespace App\Discord;

class Message
{
    /**
     * The message object.
     *
     * @var object
     */
    protected $message;

    /**
     * Constructor method.
     *
     * @return void
     */
    public function __construct(array $message)
    {
        $this->message = json_decode(json_encode($message));
    }

    /**
     * Determines if the message has the minimum number of ARC Gold reactions.
     *
     * @return boolean
     */
    public function meetsReactionThreshold()
    {
        if (!isset($this->message->reactions)) {
            return false;
        }

        foreach ($this->reactions as $reaction) {
            if (strtolower($reaction->emoji->name) === 'arcgold') {
                return $reaction->count >= 3;
            }
        }

        return false;
    }

    /**
     * Gets all embedded videos.
     *
     * @return \Illuminate\Support\Collection
     */
    public function videos()
    {
        return collect($this->embeds)->where('type', 'video');
    }

    /**
     * Gets all YouTube embeds.
     *
     * @return \Illuminate\Support\Collection
     */
    public function youtube()
    {
        return $this->videos()->where('provider.name', 'YouTube');
    }

    /**
     * Gets all Twitch embeds.
     *
     * @return \Illuminate\Support\Collection
     */
    public function twitch()
    {
        return $this->videos()->where('provider.name', 'Twitch');
    }

    /**
     * Dynamically gets the given key from the message.
     *
     * @return mixed
     */
    public function __get(string $name)
    {
        $key = snake_case($name);

        return $this->message->{$key};
    }
}
