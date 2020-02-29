<?php

namespace app\Http\Controllers;

use App\BaseApiTestCase;
use App\Models\SectorFile\SectorFileIssue;
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

    private function doMocks(bool $api, bool $shouldBeCalled = true)
    {
        if (!$shouldBeCalled) {
            $this->client
                ->shouldReceive('authenticate')
                ->never();
            return;
        }

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
            )
        ->andReturn(['number' => '55', 'url' => 'test']);
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
                    'number' => 22,
                    'labels' => [
                        [
                            'name' => config('github.api.label'),
                        ]
                    ]
                ]
            ]
        )->assertStatus(201);

        $this->assertDatabaseHas(
            'sector_file_issues',
            [
                'number' => 22,
                'api' => true,
                'plugin' => false,
            ]
        );
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
                    'number' => 22,
                    'labels' => [
                        [
                            'name' => config('github.plugin.label'),
                        ]
                    ]
                ]
            ]
        )->assertStatus(201);

        $this->assertDatabaseHas(
            'sector_file_issues',
            [
                'number' => 22,
                'plugin' => true,
                'api' => false,
            ]
        );
    }

    public function testItLabelsApiIssues()
    {
        $this->doMocks(true);
        $this->makeAuthenticatedApiGithubRequest(
            'github',
            [
                'action' => 'labeled',
                'issue' => [
                    'title' => 'Test Title',
                    'html_url' => 'Test Body',
                    'number' => 22,
                    'labels' => [
                        [
                            'name' => config('github.api.label'),
                        ]
                    ]
                ]
            ]
        )->assertStatus(201);

        $this->assertDatabaseHas(
            'sector_file_issues',
            [
                'number' => 22,
                'api' => true,
                'plugin' => false,
            ]
        );
    }

    public function testItLabelsPluginIssues()
    {
        $this->doMocks(false);
        $this->makeAuthenticatedApiGithubRequest(
            'github',
            [
                'action' => 'labeled',
                'issue' => [
                    'title' => 'Test Title',
                    'html_url' => 'Test Body',
                    'number' => 22,
                    'labels' => [
                        [
                            'name' => config('github.plugin.label'),
                        ]
                    ]
                ]
            ]
        )->assertStatus(201);

        $this->assertDatabaseHas(
            'sector_file_issues',
            [
                'number' => 22,
                'plugin' => true,
                'api' => false,
            ]
        );
    }

    public function testItDoesntLabelApiWhenAlreadyDone()
    {
        SectorFileIssue::create(
            [
                'number' => 22,
                'api' => true,
                'plugin' => true
            ]
        );

        $this->doMocks(true, false);
        $this->makeAuthenticatedApiGithubRequest(
            'github',
            [
                'action' => 'labeled',
                'issue' => [
                    'title' => 'Test Title',
                    'html_url' => 'Test Body',
                    'number' => 22,
                    'labels' => [
                        [
                            'name' => config('github.api.label'),
                        ]
                    ]
                ]
            ]
        )->assertStatus(200);
    }

    public function testItDoesntLabelPluginWhenAlreadyDone()
    {
        SectorFileIssue::create(
            [
                'number' => 22,
                'api' => true,
                'plugin' => true
            ]
        );

        $this->doMocks(false, false);
        $this->makeAuthenticatedApiGithubRequest(
            'github',
            [
                'action' => 'labeled',
                'issue' => [
                    'title' => 'Test Title',
                    'html_url' => 'Test Body',
                    'number' => 22,
                    'labels' => [
                        [
                            'name' => config('github.plugin.label'),
                        ]
                    ]
                ]
            ]
        )->assertStatus(200);
    }

    public function testItLabelsApiIfPluginDone()
    {
        SectorFileIssue::create(
            [
                'number' => 22,
                'api' => false,
                'plugin' => true
            ]
        );

        $this->doMocks(true);
        $this->makeAuthenticatedApiGithubRequest(
            'github',
            [
                'action' => 'labeled',
                'issue' => [
                    'title' => 'Test Title',
                    'html_url' => 'Test Body',
                    'number' => 22,
                    'labels' => [
                        [
                            'name' => config('github.api.label'),
                        ]
                    ]
                ]
            ]
        )->assertStatus(201);

        $this->assertDatabaseHas(
            'sector_file_issues',
            [
                'number' => 22,
                'plugin' => true,
                'api' => true,
            ]
        );
    }

    public function testItLabelsPluginIfApiDone()
    {
        SectorFileIssue::create(
            [
                'number' => 22,
                'api' => true,
                'plugin' => false
            ]
        );

        $this->doMocks(false);
        $this->makeAuthenticatedApiGithubRequest(
            'github',
            [
                'action' => 'labeled',
                'issue' => [
                    'title' => 'Test Title',
                    'html_url' => 'Test Body',
                    'number' => 22,
                    'labels' => [
                        [
                            'name' => config('github.plugin.label'),
                        ]
                    ]
                ]
            ]
        )->assertStatus(201);

        $this->assertDatabaseHas(
            'sector_file_issues',
            [
                'number' => 22,
                'plugin' => true,
                'api' => true,
            ]
        );
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

    public function testItHandlesNoValidLabels()
    {
        $this->makeAuthenticatedApiGithubRequest(
            'github',
            [
                'action' => 'created',
                'issue' => [
                    'title' => 'Test Title',
                    'html_url' => 'Test Body',
                    'labels' => [
                        [
                            'name' => 'nope',
                        ]
                    ]
                ]
            ]
        )->assertStatus(200);
        $this->assertDatabaseMissing(
            'sector_file_issues',
            [
                'number' => 22,
            ]
        );
    }

    public function testItHandlesNoLabels()
    {
        $this->makeAuthenticatedApiGithubRequest(
            'github',
            [
                'action' => 'created',
                'issue' => [
                    'title' => 'Test Title',
                    'html_url' => 'Test Body',
                ]
            ]
        )->assertStatus(200);
        $this->assertDatabaseMissing(
            'sector_file_issues',
            [
                'number' => 22,
            ]
        );
    }
}
