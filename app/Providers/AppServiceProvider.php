<?php

namespace App\Providers;

use RestCord\DiscordClient;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(DiscordClient::class, function ($app) {
            return new DiscordClient([
                'token' => config('services.discord.token'),
                'logger' => new \Psr\Log\NullLogger
            ]);
        });
    }
}
