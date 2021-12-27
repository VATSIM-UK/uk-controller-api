<?php

namespace App\Services;

use App\Exceptions\Version\ReleaseChannelNotFoundException;
use App\Exceptions\Version\VersionAlreadyExistsException;
use App\Exceptions\Version\VersionNotFoundException;
use App\Models\Version\PluginReleaseChannel;
use App\Models\Version\Version;
use Composer\Semver\VersionParser;
use Illuminate\Database\Eloquent\Collection;
use UnexpectedValueException;

class VersionService
{
    private readonly VersionParser $versionParser;

    public function __construct(VersionParser $versionParser)
    {
        $this->versionParser = $versionParser;
    }

    /**
     * Return a version model based on the version string
     *
     * @param string $versionString The version string
     * @return Version
     * @throws VersionNotFoundException
     */
    public function getVersion(string $versionString): Version
    {
        $version = Version::where('version', '=', $versionString)->withTrashed()->first();

        if (!$version) {
            throw new VersionNotFoundException('Version ' . $versionString . ' not found');
        }

        return $version;
    }

    /**
     * Returns a collection of all the versions
     *
     * @return Collection
     */
    public function getAllVersions(): Collection
    {
        return Version::withTrashed()->get();
    }

    /**
     * Toggles whether or not a version is allowed to be used in production.
     *
     * @return void
     * @throws VersionNotFoundException
     */
    public function toggleVersionAllowed(string $versionString)
    {
        $version = Version::withTrashed()->where('version', '=', $versionString)->first();

        if (!$version) {
            throw new VersionNotFoundException('Version ' . $versionString . ' not found');
        }

        $version->toggleAllowed();
    }

    public function publishNewVersionFromGithub(string $tag)
    {
        if (Version::withTrashed()->where('version', $tag)->exists()) {
            throw new VersionAlreadyExistsException();
        }

        try {
            $normalisedVersion = $this->versionParser->normalize($tag);
        } catch (UnexpectedValueException) {
            throw new ReleaseChannelNotFoundException('Invalid release channel');
        }

        $releaseChannel = PluginReleaseChannel::where('name', VersionParser::parseStability($normalisedVersion))
            ->first();

        if (!$releaseChannel) {
            throw new ReleaseChannelNotFoundException(sprintf('Release channel %s not found', $releaseChannel));
        }

        // Create the version
        $newVersion = Version::create(
            [
                'version' => $tag,
                'plugin_release_channel_id' => $releaseChannel->id,
            ]
        );

        // Retire old versions
        Version::where('id', '<>', $newVersion->id)->delete();
    }

    public function getFullVersionDetails(Version $version): array
    {
        $assetsUrl = sprintf('%s/%s', config('github.latest_release_assets_url'), $version->version);
        return [
            'id' => $version->id,
            'version' => $version->version,
            'updater_download_url' => sprintf('%s/UKControllerPluginUpdater.dll', $assetsUrl),
            'core_download_url' => sprintf('%s/UKControllerPluginCore.dll', $assetsUrl),
            'loader_download_url' => sprintf('%s/UKControllerPlugin.dll', $assetsUrl),
        ];
    }
}
