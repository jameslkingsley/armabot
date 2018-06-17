<?php

namespace App\Queries;

use App\Models\Mod;
use GuzzleHttp\Client;
use App\Notifications\ModUpdated;

class GitHub
{
    /**
     * Full API URL.
     *
     * @var string
     */
    protected $baseUri = 'https://api.github.com/repos';

    /**
     * Kicks off the query and returns the version data.
     *
     * @return mixed
     */
    public function handle(string $name, $mod)
    {
        $client = new Client;
        $response = $client->request('GET', "{$this->baseUri}/{$mod->uri}/releases/latest");
        $release = json_decode($response->getBody());

        if (!Mod::whereName($name)->whereVersion($release->tag_name)->first()) {
            // No record for this release so it must be new
            Mod::create([
                'name' => $name,
                'version' => $release->tag_name
            ])->notify(new ModUpdated([
                'name' => $mod->name,
                'url' => $release->html_url,
                'version' => $release->tag_name,
            ]));
        }
    }
}
