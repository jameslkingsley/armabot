<?php

namespace App\Queries;

use Goutte\Client;
use App\Models\Mod;
use App\Notifications\ModUpdated;

class CUP
{
    /**
     * Names of the CUP mods.
     *
     * @var array
     */
    protected $mods = [
        'Units',
        'Weapons',
        'Vehicles',
        'Terrains',
        'ACE Compatibility - Weapons',
        'ACE Compatibility - Vehicles',
        'ACE Compatibility - Terrains',
    ];

    /**
     * Kicks off the query and returns the version data.
     *
     * @return mixed
     */
    public function handle()
    {
        $mods = $this->mods;
        $client = new Client;
        $crawler = $client->request('GET', 'http://cup-arma3.org/download');
        $foundPartials = collect();

        $crawler->filter('tr')->each(function ($node) use ($mods, &$foundPartials) {
            $node->filter('td:first-of-type')->each(function ($node) use ($mods, &$foundPartials) {
                foreach ($mods as $mod) {
                    if (str_contains($node->text(), $mod)) {
                        $foundPartials->push($node->text());
                    }
                }
            });
        });

        $mod = null;
        $notifications = collect();

        foreach ($foundPartials->unique() as $name) {
            preg_match('/([0-9.]+)/', $name, $matches);
            $version = $matches[0];

            if (!Mod::whereName($name)->whereVersion($version)->first()) {
                // No record for this release so it must be new
                $mod = Mod::create([
                    'name' => $name,
                    'version' => $version
                ]);

                $notifications->push([
                    'version' => $version,
                    'url' => 'http://cup-arma3.org/download',
                    'name' => trim(preg_replace('/([0-9.]+)/', '', $name)),
                ]);
            }
        }

        if ($mod) {
            $mod->notify(new ModUpdated([
                'version' => null,
                'url' => 'http://cup-arma3.org/download',
                'name' => 'CUP ' . $notifications->implode('name', ', '),
            ]));
        }
    }
}
