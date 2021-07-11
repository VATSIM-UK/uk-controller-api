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
        $newVersion = Version::create(
            [
                'version' => $tag
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
