<?php

namespace App\Http\Controllers;

use App\BaseApiTestCase;
use App\Models\Version\PluginReleaseChannel;
use App\Models\Version\Version;
use App\Services\VersionService;
use PHPUnit\Framework\Attributes\DataProvider;
use TestingUtils\Traits\WithSeedUsers;

class VersionControllerTest extends BaseApiTestCase
{
    const CREATION_DATE_FIRST = '2017-12-02T00:00:00.000000Z';
    const DELETED_DATE_FIRST = '2017-12-04T00:00:00.000000Z';
    const CREATION_DATE_SECOND = '2017-12-03T00:00:00.000000Z';
    const CREATION_DATE_THIRD = '2017-12-04T00:00:00.000000Z';

    use WithSeedUsers;

    public function testItConstructs()
    {
        $this->assertInstanceOf(VersionController::class, $this->app->make(VersionController::class));
    }

    public function testItFindsAVersion()
    {
        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'version/2.0.1')
            ->assertJson(
                $this->app->make(VersionService::class)->getFullVersionDetails(Version::find(3))
            )->assertStatus(200);
    }

    public function testItFindsLatestStableVersion()
    {
        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'version/latest')
            ->assertJson(
                $this->app->make(VersionService::class)->getFullVersionDetails(Version::find(3))
            )->assertStatus(200);
    }

    public function testItFindsLatestVersionOnReleaseChannel()
    {
        $version = Version::create(
            [
                'version' => '5.0.0',
                'plugin_release_channel_id' => PluginReleaseChannel::where('name', 'beta')->first()->id
            ]
        );

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'version/latest?channel=beta')
            ->assertJson(
                $this->app->make(VersionService::class)->getFullVersionDetails($version)
            )->assertStatus(200);
    }

    public function testItReturnsNotFoundIfReleaseChannelInvalidForLatestVersion()
    {
        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'version/latest?channel=zeta')->assertStatus(404);
    }

    public function testItReturnsNotFoundIfVersionNotFound()
    {
        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'version/9.0.0')->assertStatus(404);
    }

    public function testItHandlesIncorrectEvent()
    {
        $this->makeAuthenticatedApiGithubRequest('version', ['action' => 'foo'])
            ->assertOk();
    }

    public static function badPublishDataProvider(): array
    {
        return [
            'No release' => [
                [
                    'action' => 'published',
                ]
            ],
            'Release not an array' => [
                [
                    'action' => 'published',
                    'release' => '',
                ]
            ],
            'No tag name' => [
                [
                    'action' => 'published',
                    'release' => [],
                ]
            ],
            'Tag name not a string' => [
                [
                    'action' => 'published',
                    'release' => [
                        'tag_name' => 123,
                    ],
                ]
            ],
        ];
    }

    #[DataProvider('badPublishDataProvider')]
    public function testItHandlesBadPublishData(array $data)
    {
        $this->makeAuthenticatedApiGithubRequest('version', $data)
            ->assertStatus(400);
    }

    public function testItHandlesVersionAlreadyExists()
    {
        $data = [
            'action' => 'published',
            'release' => [
                'tag_name' => '2.0.0',
            ],
        ];
        $this->makeAuthenticatedApiGithubRequest('version', $data)
            ->assertOk();
    }

    public function testItHandlesBadReleaseChannel()
    {
        $data = [
            'action' => 'published',
            'release' => [
                'tag_name' => '2.0.0-abcd.1',
            ],
        ];
        $this->makeAuthenticatedApiGithubRequest('version', $data)
            ->assertOk();
    }

    public function testItCreatesANewVersion()
    {
        $data = [
            'action' => 'published',
            'release' => [
                'tag_name' => '3.0.0',
            ],
        ];

        $this->makeAuthenticatedApiGithubRequest('version', $data)
            ->assertCreated();
        $this->assertDatabaseHas(
            'version',
            [
                'version' => '3.0.0',
            ]
        );
    }
}
