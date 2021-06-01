<?php
namespace App\Services;

use App\Exceptions\Version\VersionAlreadyExistsException;
use App\Exceptions\Version\VersionNotFoundException;
use Carbon\Carbon;
use App\BaseFunctionalTestCase;
use App\Models\Version\Version;

class VersionServiceTest extends BaseFunctionalTestCase
{
    const CURRENT_VERSION = '2.0.1';
    const ALLOWED_OLD_VERSION = '2.0.0';
    const DEPRECATED_VERSION = '1.0.0';

    /**
     * @var VersionService
     */
    private $service;

    public function setUp() : void
    {
        parent::setUp();
        $this->service = $this->app->make(VersionService::class);
        Carbon::setTestNow(Carbon::now());
    }

    public function testItConstructs()
    {
        $this->assertInstanceOf(VersionService::class, $this->service);
    }

    public function testResponseUnknownVersion()
    {
        $expected = [
            'version_disabled' => true,
            'message' => 'This version of UK Controller Plugin is unknown.',
            'update_available' => true,
        ];

        $this->assertEquals($expected, $this->service->getVersionResponse('notaversion'));
    }

    public function testResponseNotAllowedVersion()
    {
        $expected = [
            'update_available' => true,
            'message' => 'This version of the UK Controller Plugin has been removed from service. ' .
                'In order to continue using the plugin, you must download the latest version from the website.',
            'version_disabled' => true,
        ];

        $this->assertEquals($expected, $this->service->getVersionResponse(self::DEPRECATED_VERSION));
    }

    public function testResponseUpdateAvailable()
    {
        $expected = [
            'update_available' => true,
            'message' => 'A new version of the UK Controller Plugin is available from the VATSIM UK Website.',
            'version_disabled' => false,
        ];

        $this->assertEquals($expected, $this->service->getVersionResponse(self::ALLOWED_OLD_VERSION));
    }

    public function testResponseUpToDate()
    {
        $expected = [
            'update_available' => false,
            'version_disabled' => false,
        ];

        $this->assertEquals($expected, $this->service->getVersionResponse(self::CURRENT_VERSION));
    }

    public function testGetVersionReturnsVersionInformation()
    {
        $expected = [
            'id' => 3,
            'version' => '2.0.1',
            'core_download_url' => 'https://github.com/VATSIM-UK/uk-controller-plugin/releases/download/2.0.1/UKControllerPluginCore.dll',
            'updater_download_url' => 'https://github.com/VATSIM-UK/uk-controller-plugin/releases/download/2.0.1/UKControllerPluginUpdater.dll',
            'loader_download_url' => 'https://github.com/VATSIM-UK/uk-controller-plugin/releases/download/2.0.1/UKControllerPlugin.dll',
        ];

        $this->assertEquals($expected, $this->service->getFullVersionDetails(Version::find(3)));
    }

    public function testGetAllVersionsReturnsAllVersions()
    {
        $versions = $this->service->getAllVersions();
        $this->assertEquals(3, $versions->count());

        foreach ($versions as $key => $version) {
            $this->assertEquals($key + 1, $version->id);
        }
    }

    public function testCreateVersionCreatesAVersion()
    {
        Carbon::setTestNow(Carbon::now());
        $this->assertTrue($this->service->createOrUpdateVersion('3.0.0', true));
        $this->assertDatabaseHas(
            'version',
            [
                'version' => '3.0.0',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null,
            ]
        );
    }

    public function testUpdateVersionUpdatesVersion()
    {
        Carbon::setTestNow(Carbon::now());
        $this->assertFalse($this->service->createOrUpdateVersion(self::ALLOWED_OLD_VERSION, false));
        $this->assertDatabaseHas(
            'version',
            [
                'version' => self::ALLOWED_OLD_VERSION,
                'created_at' => '2017-12-03 00:00:00',
                'updated_at' => Carbon::now()->toDateTimeString(),
                'deleted_at' => Carbon::now()->toDateTimeString(),
            ]
        );
    }

    public function testToggleVersionAllowedThrowsExceptionOnUnknownVersion()
    {
        $this->expectException(VersionNotFoundException::class);
        $this->service->toggleVersionAllowed('666');
    }

    public function testToggleVersionAllowedTogglesAllowed()
    {
        $this->assertFalse(Version::find(2)->trashed());
        $this->service->toggleVersionAllowed(self::ALLOWED_OLD_VERSION);
        $this->assertTrue(Version::withTrashed()->find(2)->trashed());
        $this->service->toggleVersionAllowed(self::ALLOWED_OLD_VERSION);
        $this->assertFalse(Version::find(2)->trashed());
    }

    public function testItPublishesANewGithubVersion()
    {
        $this->service->publishNewVersionFromGithub('3.0.0');
        $this->assertDatabaseHas(
            'version',
            [
                'version' => '3.0.0',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null,
            ]
        );
    }

    public function testItRetiresOldVersions()
    {
        $this->service->createOrUpdateVersion('2.0.2', true);
        $this->service->createOrUpdateVersion('2.0.3', true);
        $this->service->createOrUpdateVersion('2.0.4', true);
        $this->service->publishNewVersionFromGithub('3.0.0');

        $this->assertDatabaseHas(
            'version',
            [
                'version' => '2.0.0',
                'deleted_at' => Carbon::now(),
            ]
        );
        $this->assertDatabaseHas(
            'version',
            [
                'version' => '2.0.1',
                'deleted_at' => Carbon::now(),
            ]
        );
        $this->assertDatabaseHas(
            'version',
            [
                'version' => '2.0.2',
                'deleted_at' => Carbon::now(),
            ]
        );
        $this->assertDatabaseHas(
            'version',
            [
                'version' => '2.0.3',
                'deleted_at' => null,
            ]
        );
        $this->assertDatabaseHas(
            'version',
            [
                'version' => '2.0.4',
                'deleted_at' => null,
            ]
        );
        $this->assertDatabaseHas(
            'version',
            [
                'version' => '3.0.0',
                'deleted_at' => null,
            ]
        );
    }

    public function testItThrowsExceptionIfPublishingExistingVersion()
    {
        $this->expectException(VersionAlreadyExistsException::class);
        $this->service->publishNewVersionFromGithub('1.0.0');
    }
}
