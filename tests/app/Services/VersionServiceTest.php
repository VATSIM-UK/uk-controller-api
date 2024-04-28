<?php

namespace App\Services;

use App\Exceptions\Version\ReleaseChannelNotFoundException;
use App\Exceptions\Version\VersionAlreadyExistsException;
use App\Exceptions\Version\VersionNotFoundException;
use App\Models\Version\PluginReleaseChannel;
use Carbon\Carbon;
use App\BaseFunctionalTestCase;
use App\Models\Version\Version;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class VersionServiceTest extends BaseFunctionalTestCase
{
    const CURRENT_VERSION = '2.0.1';
    const ALLOWED_OLD_VERSION = '2.0.0';
    const DEPRECATED_VERSION = '1.0.0';

    /**
     * @var VersionService
     */
    private $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(VersionService::class);
        Carbon::setTestNow(Carbon::now());
    }

    public function testItConstructs()
    {
        $this->assertInstanceOf(VersionService::class, $this->service);
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

    public function testItFindsTheMostRecentVersionOnTheStableChannel()
    {
        $this->assertEquals(Version::find(3), $this->service->getLatestVersionForReleaseChannel('stable'));
    }

    public function testItFindsTheMostRecentMostStableVersion()
    {
        Version::find(2)->update(
            ['plugin_release_channel_id' => PluginReleaseChannel::where('name', 'beta')->first()->id]
        );
        $this->assertEquals(Version::find(3), $this->service->getLatestVersionForReleaseChannel('stable'));
    }

    public function testItFindsTheMostRecentMostRecentVersionOnChannelIfMoreRecentThanStable()
    {
        $expected = Version::create(
            [
                'version' => '3.2.0-beta.1',
                'plugin_release_channel_id' => PluginReleaseChannel::where('name', 'beta')->first()->id
            ]
        );
        $this->assertEquals($expected->id, $this->service->getLatestVersionForReleaseChannel('beta')->id);
    }

    public function testItFindsTheMostRecentMostStableVersionIfMoreRecentThanRequestedChannel()
    {
        Version::find(2)->update(
            ['plugin_release_channel_id' => PluginReleaseChannel::where('name', 'beta')->first()->id]
        );
        $this->assertEquals(Version::find(3), $this->service->getLatestVersionForReleaseChannel('beta'));
    }


    public function testItThrowsExceptionIfNoVersionsAvailableOnChannel()
    {
        Version::all()->each(function (Version $version) {
            $version->delete();
        });

        $this->expectException(VersionNotFoundException::class);
        $this->service->getLatestVersionForReleaseChannel('stable');
    }

    public function testItThrowsExceptionIfChannelNotFound()
    {
        $this->expectException(ModelNotFoundException::class);
        $this->service->getLatestVersionForReleaseChannel('bla');
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
                'plugin_release_channel_id' => PluginReleaseChannel::where('name', 'stable')->first()->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null,
            ]
        );
    }

    public function testItPublishesANewGithubBetaVersion()
    {
        $this->service->publishNewVersionFromGithub('3.0.0-beta.1');
        $this->assertDatabaseHas(
            'version',
            [
                'version' => '3.0.0-beta.1',
                'plugin_release_channel_id' => PluginReleaseChannel::where('name', 'beta')->first()->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null,
            ]
        );
    }

    public function testItThrowsAnExceptionOnBadReleaseChannel()
    {
        $this->expectException(ReleaseChannelNotFoundException::class);
        $this->service->publishNewVersionFromGithub('3.0.0-abcd.1');
    }

    public function testItThrowsAnExceptionOnInvalidReleaseChannel()
    {
        $this->expectException(ReleaseChannelNotFoundException::class);
        $this->service->publishNewVersionFromGithub('3.0.0-zeta.1');
    }

    public function testItThrowsExceptionIfPublishingExistingVersion()
    {
        $this->expectException(VersionAlreadyExistsException::class);
        $this->service->publishNewVersionFromGithub('1.0.0');
    }
}
