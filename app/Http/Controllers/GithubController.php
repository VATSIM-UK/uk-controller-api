<?php

namespace App\Http\Controllers;

use Exception;
use Github\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GithubController
{
    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function processGithubWebhook(Request $request)
    {
        if ($request->json()->get('action') !== 'created') {
            return response('', 200);
        }

        if (!$request->json()->get('issue')) {
            return response('', 422);
        }

        $issue = $request->json()->get('issue');
        $labels = $issue['labels'] ?? [];

        foreach ($labels as $label) {
            if ($label['name'] == config('github.plugin.label') || $label['name'] == config('github.api.label')) {
                // Do it
                $pushOrg = $label['name'] == config('github.plugin.label')
                    ? config('github.plugin.org')
                    : config('github.api.org');

                $pushRepo = $label['name'] == config('github.plugin.label')
                    ? config('github.plugin.repo')
                    : config('github.api.repo');


                try {
                    $this->client->authenticate(
                        config('github.access_token'),
                        null,
                        Client::AUTH_HTTP_TOKEN
                    );

                    $issueInfo = $this->client->api('issue')
                        ->create(
                            $pushOrg,
                            $pushRepo,
                            [
                                'title' => $issue['title'],
                                'body' => $issue['html_url'],
                                'labels' => [
                                    'dependency'
                                ],
                            ]
                        );
                } catch (Exception $exception) {
                    Log::error(
                        'Unable to create GitHub issue',
                        [$exception->getMessage()]
                    );
                    return response('', 502);
                }

                Log::info('Created GitHub issue', [json_decode($issueInfo, true)['url']]);
                return response('', 201);
            }
        }

        return response('', 200);
    }
}
