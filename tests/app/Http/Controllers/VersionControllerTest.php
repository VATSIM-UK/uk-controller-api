<?php
namespace App\Http\Controllers;

use App\BaseApiTestCase;
use App\Models\Version\Version;
use App\Providers\AuthServiceProvider;
use App\Services\VersionService;
use TestingUtils\Traits\WithSeedUsers;

class VersionControllerTest extends BaseApiTestCase
{
    const CREATION_DATE_FIRST = '2017-12-02T00:00:00.000000Z';
    const DELETED_DATE_FIRST = '2017-12-04T00:00:00.000000Z';
    const CREATION_DATE_SECOND = '2017-12-03T00:00:00.000000Z';
    const CREATION_DATE_THIRD = '2017-12-04T00:00:00.000000Z';

    use WithSeedUsers;

    protected static $tokenScope = [
        AuthServiceProvider::SCOPE_USER,
        AuthServiceProvider::SCOPE_VERSION_ADMIN,
    ];

    public function testItConstructs()
    {
        $this->assertInstanceOf(VersionController::class, $this->app->make(VersionController::class));
    }

    public function testGetAllVersionsFailsWithoutVersionAdminScope()
    {
        $this->regenerateAccessToken([], static::$tokenUser);
        $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'version')
            ->assertStatus(403);
    }

    public function testItFindsAllVersions()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'version')
            ->assertJson(
                [
                    [
                        'id' => 1,
                        'version' => '1.0.0',
                        'created_at' => self::CREATION_DATE_FIRST,
                        'updated_at' => self::CREATION_DATE_SECOND,
                        'deleted_at' => self::DELETED_DATE_FIRST,
                    ],
                    [
                        'id' => 2,
                        'version' => '2.0.0',
                        'created_at' => self::CREATION_DATE_SECOND,
                        'updated_at' => null,
                        'deleted_at' => null,
                    ],
                    [
                        'id' => 3,
                        'version' => '2.0.1',
                        'created_at' => self::CREATION_DATE_THIRD,
                        'updated_at' => null,
                        'deleted_at' => null,
                    ],
                ]
            )->assertStatus(200);
    }

    public function testItFindsAVersion()
    {
        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'version/2.0.1')
            ->assertJson(
                $this->app->make(VersionService::class)->getFullVersionDetails(Version::find(3))
            )->assertStatus(200);
    }

    public function testItFindsLatestVersion()
    {
        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'version/latest')
            ->assertJson(
                $this->app->make(VersionService::class)->getFullVersionDetails(Version::find(3))
            )->assertStatus(200);
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

    public function badPublishDataProvider(): array
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

    /**
     * @dataProvider badPublishDataProvider
     */
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
