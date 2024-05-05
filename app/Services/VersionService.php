<?php

namespace App\Services;

use App\Exceptions\Version\ReleaseChannelNotFoundException;
use App\Exceptions\Version\VersionAlreadyExistsException;
use App\Exceptions\Version\VersionNotFoundException;
use App\Models\Version\PluginReleaseChannel;
use App\Models\Version\Version;
use Composer\Semver\Comparator;
use Composer\Semver\VersionParser;
use Http\Client\Common\Plugin;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use UnexpectedValueException;

class VersionService
{
    private VersionParser $versionParser;

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
     * Toggles whether or not a version is allowed to be used in production.
     *
     * @throws VersionNotFoundException
     */
    public function toggleVersionAllowed(string $versionString): void
    {
        $version = Version::withTrashed()->where('version', '=', $versionString)->first();

        if (!$version) {
            throw new VersionNotFoundException('Version ' . $versionString . ' not found');
        }

        $version->toggleAllowed();
    }

    /**
     * @throws VersionAlreadyExistsException
     * @throws ReleaseChannelNotFoundException
     */
    public function publishNewVersionFromGithub(string $tag): void
    {
        if (Version::withTrashed()->where('version', $tag)->exists()) {
            throw new VersionAlreadyExistsException();
        }

        try {
            $normalisedVersion = $this->versionParser->normalize($tag);
        } catch (UnexpectedValueException $exception) {
            Log::error(sprintf('Invalid release channel %s', $tag));
            throw new ReleaseChannelNotFoundException();
        }

        $releaseChannel = PluginReleaseChannel::where('name', VersionParser::parseStability($normalisedVersion))
            ->first();

        if (!$releaseChannel) {
            Log::error(sprintf('Invalid release channel %s', $tag));
            throw new ReleaseChannelNotFoundException();
        }

        // Create the version
        Version::create(
            [
                'version' => $tag,
                'plugin_release_channel_id' => $releaseChannel->id,
            ]
        );
    }

    /**
     * The "latest version" on any release channel is the most recent version (in semver terms)
     * on any channel that is, or more stable than, the requested channel.
     *
     * @param string $channel
     * @return Version
     * @throws VersionNotFoundException
     */
    public function getLatestVersionForReleaseChannel(string $channel): Version
    {
        $relevantVersions = $this->getRelevantVersions(PluginReleaseChannel::where('name', $channel)->firstOrFail());
        if ($relevantVersions->isEmpty()) {
            throw new VersionNotFoundException();
        }

        return $relevantVersions->reduce(function (?Version $selectedVersion, Version $version) {
            return is_null($selectedVersion) || Comparator::greaterThan(
                $version->version,
                $selectedVersion->version
            ) ? $version : $selectedVersion;
        });
    }

    private function getRelevantVersions(PluginReleaseChannel $requestedChannel): Collection
    {
        return PluginReleaseChannel::where('relative_stability', '<=', $requestedChannel->relative_stability)
            ->get()
            ->map(fn (PluginReleaseChannel $channel) => Version::orderByDesc('id')->releaseChannel($channel)->first())
            ->filter();
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
