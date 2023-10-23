<?php

namespace App\Http\Controllers;

use App\Models\SectorFile\SectorFileIssue;
use Exception;
use Github\Client;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GithubController
{
    const CONFIG_KEY_PLUGIN_LABEL = 'github.plugin.label';
    const CONFIG_KEY_API_LABEL = 'github.api.label';
    
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
        $allValues = $request->json()->all();
        if (!isset($allValues['issue'])) {
            return response('', 422);
        }

        // Check for the events we care about
        if (!in_array($request->json()->get('action'), ['created', 'labeled'])) {
            return response('', 200);
        }

        // Check for the presence of a valid label and only process if there's a label to work with
        $labelNames = isset($allValues['issue']['labels'])
            ? array_column($allValues['issue']['labels'], 'name')
            : [];

        $response = null;
        foreach ($labelNames as $labelName) {
            if (
                $labelName === config(self::CONFIG_KEY_PLUGIN_LABEL) ||
                $labelName === config(self::CONFIG_KEY_API_LABEL)
            ) {
                $response = $this->handleEvent($allValues['issue']);
                break;
            }
        }

        return $response ?? response('', 200);
    }

    private function handleEvent(array $issue): Response
    {
        $response = null;
        DB::transaction(function () use ($issue, &$response) {
            // Create the database issue or find it, if there's a duplicate request, handle the exception.
            try {
                $issueId = SectorFileIssue::firstOrCreate(
                    ['number' => $issue['number']],
                    [
                        'api' => false,
                        'plugin' => false,
                    ]
                )->id;
            } catch (QueryException $queryException) {
                if ($queryException->errorInfo[1] === 1062) {
                    $response = response('', 200);
                    return;
                }

                throw $queryException;
            }

            $response = $this->processLabels(SectorFileIssue::lockForUpdate()->find($issueId), $issue);
        });

        return $response;
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
        foreach ($labels as $label) {
            if ($label['name'] == config(self::CONFIG_KEY_PLUGIN_LABEL) && !$databaseIssue->plugin) {
                $createdPlugin = $this->createGithubIssue($label['name'], $issue['title'], $issue['html_url']);
                $databaseIssue->plugin = $createdPlugin;
                $numCreated = $numCreated + ($createdPlugin ? 1 : -10);
            }

            if ($label['name'] == config(self::CONFIG_KEY_API_LABEL) && !$databaseIssue->api) {
                $createdApi = $this->createGithubIssue($label['name'], $issue['title'], $issue['html_url']);
                $databaseIssue->api = $createdApi;
                $numCreated = $numCreated + ($createdApi ? 1 : -10);
            }
        }

        if ($numCreated == 0) {
            return response('', 200);
        }

        // Update the database with what succeeded
        $databaseIssue->save();

        if ($numCreated < 0) {
            Log::error('Error creating github issue(s)');
            return response('', 502);
        }

        Log::info('Created GitHub issues');
        return response('', 201);
    }

    private function createGithubIssue(string $sourceLabel, string $title, string $url): bool
    {
        // Do it
        $pushOrg = $sourceLabel == config(self::CONFIG_KEY_PLUGIN_LABEL)
            ? config('github.plugin.org')
            : config('github.api.org');

        $pushRepo = $sourceLabel == config(self::CONFIG_KEY_PLUGIN_LABEL)
            ? config('github.plugin.repo')
            : config('github.api.repo');


        try {
            $this->client->authenticate(
                config('github.access_token'),
                null,
                Client::AUTH_ACCESS_TOKEN
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
            Log::error(
                'Unable to create GitHub issue',
                [$exception->getMessage()]
            );
            return false;
        }
    }
}
