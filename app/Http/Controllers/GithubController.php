<?php

namespace App\Http\Controllers;

use App\Models\SectorFile\SectorFileIssue;
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
        if (!$request->json()->get('issue')) {
            return response('', 422);
        }

        if (!in_array($request->json()->get('action'), ['created', 'labeled'])) {
            return response('', 200);
        }

        return $this->handleEvent($request->json()->get('issue'));
    }

    private function handleEvent(array $issue)
    {
        $databaseIssue = $this->getDatabaseIssue($issue);
        return $this->processLabels($databaseIssue, $issue);
    }
    /**
     * Create a blank database issue if we dont have one, or get the current
     *
     * @param array $issue
     * @return SectorFileIssue
     */
    private function getDatabaseIssue(array $issue) : SectorFileIssue
    {
        return SectorFileIssue::firstOrNew(
            ['number' => $issue['number']],
            [
                'api' => false,
                'plugin' => false
            ]
        );
    }

    /**
     * Process the labels, returns a negative number if at least one creation fails. Returns 0 if
     * no issues created. Returns number of creations if all succeed.
     *
     * @param SectorFileIssue $databaseIssue
     * @param array $issue
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    private function processLabels(SectorFileIssue $databaseIssue, array $issue)
    {
        $labels = $issue['labels'] ?? [];
        $numCreated = 0;
        dump('env', env('UKSF_LABEL_NAME_API'));
        dump(config());
        foreach ($labels as $label) {
            if ($label['name'] == config('github.plugin.label') && !$databaseIssue->plugin) {
                $createdPlugin = $this->createGithubIssue($label['name'], $issue['title'], $issue['html_url']);
                dump('plugin_create', $createdPlugin);
                $databaseIssue->plugin = $createdPlugin;
                $numCreated = $numCreated + ($createdPlugin ? 1 : -10);
            }

            if ($label['name'] == config('github.api.label') && !$databaseIssue->api) {
                $createdApi = $this->createGithubIssue($label['name'], $issue['title'], $issue['html_url']);
                dump('api_create', $createdApi);
                $databaseIssue->api = $createdApi;
                $numCreated = $numCreated + ($createdApi ? 1 : -10);
            }
        }

        // Update the database with what succeeded
        dump('all', SectorFileIssue::all()->toArray());
        dump($databaseIssue->toArray());
        $databaseIssue->save();

        if ($numCreated < 0) {
            Log::error('Error creating github issue(s)');
            return response('', 502);
        }

        if ($numCreated == 0) {
            return response('', 200);
        }

        Log::info('Created GitHub issues');
        return response('', 201);
    }

    private function createGithubIssue(string $sourceLabel, string $title, string $url): bool
    {
        // Do it
        $pushOrg = $sourceLabel == config('github.plugin.label')
            ? config('github.plugin.org')
            : config('github.api.org');

        $pushRepo = $sourceLabel == config('github.plugin.label')
            ? config('github.plugin.repo')
            : config('github.api.repo');


        try {
            $this->client->authenticate(
                config('github.access_token'),
                null,
                Client::AUTH_HTTP_TOKEN
            );

            $this->client->api('issue')
                ->create(
                    $pushOrg,
                    $pushRepo,
                    [
                        'title' => $title,
                        'body' => $url,
                        'labels' => [
                            'dependency'
                        ],
                    ]
                );
            Log::info('Created GitHub issue');
            return true;
        } catch (Exception $exception) {
            dd($exception->getMessage());
            Log::error(
                'Unable to create GitHub issue',
                [$exception->getMessage()]
            );
            return false;
        }
    }
}
