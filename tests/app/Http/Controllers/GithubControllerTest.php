<?php

namespace app\Http\Controllers;

use App\BaseApiTestCase;
use Github\Api\ApiInterface;
use Github\Api\Issue;
use Github\Client;
use Mockery;

class GithubControllerTest extends BaseApiTestCase
{
    private $client;

    public function setUp(): void
    {
        parent::setUp();
        $this->client = Mockery::mock(Client::class);
        $this->app[Client::class] = $this->client;
    }

    private function doMocks(bool $api)
    {
        $issuesMock = Mockery::mock(Issue::class);

        $this->client
            ->shouldReceive('authenticate')
            ->withArgs([config('github.access_token'), null, Client::AUTH_HTTP_TOKEN])
            ->once();

        $this->client->shouldReceive('api')->with('issue')->andReturn($issuesMock);
        $issuesMock->shouldReceive('create')
            ->withArgs(
                [
                    config(sprintf('github.%s.org', $api ? 'api': 'plugin')),
                    config(sprintf('github.%s.repo', $api ? 'api': 'plugin')),
                    [
                        'title' => 'Test Title',
                        'body' => 'Test Body',
                        'labels' => [
                            'dependency'
                        ],
                    ]
                ]
            );
    }

    public function testItCreatesApiIssues()
    {
        $this->doMocks(true);
        $this->makeAuthenticatedApiGithubRequest(
            'github',
            [
                'action' => 'created',
                'issue' => [
                    'title' => 'Test Title',
                    'html_url' => 'Test Body',
                    'labels' => [
                        [
                            'name' => config('github.api.label'),
                        ]
                    ]
                ]
            ]
        )->assertStatus(201);
    }

    public function testItCreatesPluginIssues()
    {
        $this->doMocks(false);
        $this->makeAuthenticatedApiGithubRequest(
            'github',
            [
                'action' => 'created',
                'issue' => [
                    'title' => 'Test Title',
                    'html_url' => 'Test Body',
                    'labels' => [
                        [
                            'name' => config('github.plugin.label'),
                        ]
                    ]
                ]
            ]
        )->assertStatus(201);
    }

    public function testItHandlesNonCreatedIssues()
    {
        $this->makeAuthenticatedApiGithubRequest(
            'github',
            [
                'action' => 'updated',
                'issue' => [
                    'title' => 'Test Title',
                    'html_url' => 'Test Body',
                    'labels' => [
                        [
                            'name' => config('github.plugin.label'),
                        ]
                    ]
                ]
            ]
        )->assertStatus(200);
    }

    public function testItHandlesNotIssues()
    {
        $this->makeAuthenticatedApiGithubRequest(
            'github',
            [
                'action' => 'created',
                'notissue' => [
                    'title' => 'Test Title',
                    'html_url' => 'Test Body',
                    'labels' => [
                        [
                            'name' => config('github.plugin.label'),
                        ]
                    ]
                ]
            ]
        )->assertStatus(422);
    }
}
