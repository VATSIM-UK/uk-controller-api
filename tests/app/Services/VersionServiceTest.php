<?php
namespace App\Services;

use App\Exceptions\VersionNotFoundException;
use Carbon\Carbon;
use App\BaseFunctionalTestCase;
use App\Models\Version\Version;

class VersionServiceTest extends BaseFunctionalTestCase
{
    /**
     * @var VersionService
     */
    private $service;

    public function setUp() : void
    {
        parent::setUp();
        $this->service = $this->app->make(VersionService::class);
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

        $this->assertEquals($expected, $this->service->getVersionResponse('1.0.0'));
    }

    public function testResponseUpdateAvailable()
    {
        $expected = [
            'update_available' => true,
            'message' => 'A new version of the UK Controller Plugin is available from the VATSIM UK Website.',
            'version_disabled' => false,
        ];

        $this->assertEquals($expected, $this->service->getVersionResponse('2.0.0'));
    }

    public function testResponseUpToDate()
    {
        $expected = [
            'update_available' => false,
            'version_disabled' => false,
        ];

        $this->assertEquals($expected, $this->service->getVersionResponse('2.0.1'));
    }

    public function testGetVersionThrowsExceptionIfDoesNotExist()
    {
        $this->expectException(VersionNotFoundException::class);
        $this->service->getVersion('666');
    }

    public function testGetVersionReturnsAVersion()
    {
        $this->assertEquals(1, $this->service->getVersion('1.0.0')->id);
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
                'allowed' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        );
    }

    public function testUpdateVersionUpdatesVersion()
    {
        Carbon::setTestNow(Carbon::now());
        $this->assertFalse($this->service->createOrUpdateVersion('2.0.0', false));
        $this->assertDatabaseHas(
            'version',
            [
                'version' => '2.0.0',
                'allowed' => 0,
                'created_at' => '2017-12-03 00:00:00',
                'updated_at' => Carbon::now(),
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
        $this->assertTrue(Version::find(2)->allowed);
        $this->service->toggleVersionAllowed('2.0.0');
        $this->assertFalse(Version::find(2)->allowed);
        $this->service->toggleVersionAllowed('2.0.0');
        $this->assertTrue(Version::find(2)->allowed);
    }
}
