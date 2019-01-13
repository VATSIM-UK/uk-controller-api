<?php
namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;

/**
 * A service for creating a file manifest for a given folder.
 *
 * Class ManifestService
 *
 * @package App\Providers
 */
class ManifestService extends ServiceProvider
{
    /**
     * Returns the manifest file, from cache if possible and enabled, or generates
     * a new one if not.
     *
     * @param  string $driver    The storage driver to use.
     * @param  string $directory The directory to use.
     * @param  bool   $useCache  Whether or not to try looking for cached values.
     * @return array Array of data.
     */
    public function getManifest(string $driver, string $directory, bool $useCache = false)
    {
        // Get the cached value if caching is on and we have the cached version
        if ($useCache && Cache::has($directory)) {
            return Cache::get($directory);
        }

        // Generate from disk and save to cache if required.
        $manifest = $this->generateManifestFromDisk($driver, $directory);
        if ($useCache) {
            Cache::forever($directory, $manifest);
        }

        return $manifest;
    }

    /**
     * Generates the manifest file from the files on disk.
     *
     * @param  string $driver    The storage driver to use.
     * @param  string $directory The directory to use.
     * @return array Array of data.
     */
    private function generateManifestFromDisk(string $driver, string $directory)
    {
        // Get a list of all the files
        $files = Storage::disk($driver)->files($directory);
        $manifest = [];
        
        // Create the JSON array
        foreach ($files as $file) {
            $manifest[$this->getArrayIndex($file)] = $this->getArrayEntryForFile($driver, $file);
        }

        return $manifest;
    }

    /**
     * Given a particular file, create the data
     * array for the manifest.
     *
     * @param  string $file The file to check.
     * @return array File data.
     */
    private function getArrayEntryForFile(string $driver, string $file)
    {
        return [
            'uri' => Storage::disk($driver)->url($file),
            'md5' => md5(Storage::disk($driver)->get($file)),
        ];
    }

    /**
     * Returns the array index for the given file - the filename without folders.
     *
     * @param  string $file The file
     * @return bool|string
     */
    private function getArrayIndex(string $file)
    {
        return substr($file, strrpos($file, DIRECTORY_SEPARATOR) + 1);
    }
}
