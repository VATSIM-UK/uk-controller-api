<?php
namespace App\Services;

use App\Exceptions\Version\VersionAlreadyExistsException;
use App\Exceptions\Version\VersionNotFoundException;
use App\Models\Version\Version;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

/**
 * Service for converting versions into data arrays that can be returned as
 * a response.
 *
 * Class VersionServiceProvider
 *
 * @package App\Providers
 */
class VersionService extends ServiceProvider
{
    private const VERSIONS_TO_KEEP = 3;

    /**
     * Determines an appropriate JSON response, given two versions.
     *
     * @deprecated Superseded in Version 3.0.0 of UKCP
     * @param  string $userVersion The version of the plugin that the user client reports.
     * @return array A response to be converted to JSON and returned.
     */
    public function getVersionResponse(string $userVersion)
    {
        // If the version is unknown, fail.
        $version = Version::where('version', $userVersion)->withTrashed()->first();
        if ($version === null) {
            return [
                'version_disabled' => true,
                'message' => 'This version of UK Controller Plugin is unknown.',
                'update_available' => true,
            ];
        }

        // Get the latest version and then do comparisons to make the appropriate response.
        $latestVersion = Version::orderBy('id', 'desc')
            ->first();

        return $this->getPossibleVersionResponse($version, $latestVersion);
    }

    /**
     * Generate the appropriate response array when the version
     * that we've been provided with at least exists.
     *
     * @param  Version $userVersion   The users version.
     * @param  Version $latestVersion The latest version.
     * @return array The response.
     */
    private function getPossibleVersionResponse(Version $userVersion, Version $latestVersion)
    {
        if ($userVersion->trashed()) {
            // They're using a deprecated version
            Log::info('Attempt to use deprecated version ' . $userVersion->version . ', which was rejected by the API');
            $jsonArray = [
                'update_available' => true,
                'message' => 'This version of the UK Controller Plugin has been removed from service. ' .
                    'In order to continue using the plugin, you must download the latest version from the website.',
                'version_disabled' => true,
            ];
        } elseif ($userVersion->version !== $latestVersion->version) {
            // They're using a version that is still allowed, but an update is available
            Log::info('Attempt to use old version ' . $userVersion->version . ', which was accepted by the API');
            $jsonArray = [
                'update_available' => true,
                'message' => 'A new version of the UK Controller Plugin is available from the VATSIM UK Website.',
                'version_disabled' => false,
            ];
        } else {
            // All up to date
            $jsonArray = [
                'update_available' => false,
                'version_disabled' => false,
            ];
        }

        // Return the JSON.
        return $jsonArray;
    }

    /**
     * Return a version model based on the version string
     *
     * @param string $versionString The version string
     * @throws VersionNotFoundException
     * @return Version
     */
    public function getVersion(string $versionString) : Version
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
    public function getAllVersions() : Collection
    {
        return Version::withTrashed()->get();
    }

    /**
     * Create a version based on the versions string
     *
     * @param string $versionString The version string to use
     * @return bool True if the version is new, false otherwise
     */
    public function createOrUpdateVersion(string $versionString, bool $allowed) : bool
    {
        $version = Version::updateOrCreate(
            [
                'version' => $versionString,
            ],
            [
                'deleted_at' => $allowed ? null : Carbon::now(),
            ]
        );

        return $version->wasRecentlyCreated ? true : false;
    }

    /**
     * Toggles whether or not a version is allowed to be used in production.
     *
     * @throws VersionNotFoundException
     * @return void
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

        // Create the version
        Version::create(
            [
                'version' => $tag
            ]
        );

        // Retire old versions
        $versionsToKeep = Version::orderBy('id', 'desc')->limit(self::VERSIONS_TO_KEEP)->pluck('id')->toArray();
        Version::whereNotIn('id', $versionsToKeep)->delete();
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
