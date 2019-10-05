<?php

namespace App\Console\Commands;

use App\Models\Sid;
use Illuminate\Console\Command;
use InvalidArgumentException;

class GetDeletedSidsFromSectorFile extends Command
{
    protected $signature = 'sids:check-sectorfile {sectorfile_path}';

    protected $description = 'Parse the UK Sector File and notify any SIDs that need to be deleted';

    public function handle()
    {
        if (!$this->argument('sectorfile_path')) {
            throw new InvalidArgumentException('No sectorfile path provided');
        }

        $file = fopen($this->argument('sectorfile_path'), 'r');
        if (!$file) {
            throw new InvalidArgumentException('Unable to open sectorfile for parsing');
        }

        $this->comparePluginToSectorFile(
            $this->processSectorFile($file),
            $this->processPlugin()
        );

        fclose($file);
    }

    /**
     * Compare the sector file to the plugin and announce which SIDs need to go
     *
     * @param array $sectorFile
     * @param array $plugin
     */
    private function comparePluginToSectorFile(array $sectorFile, array $plugin) : void
    {
        $removedSids = array_diff($plugin, $sectorFile);

        if (count($removedSids) === 0) {
            $this->info('No SIDs are present in the plugin that are not in the sector file.');
            return;
        }
        $this->info('SIDs present in plugin but not the sector file:');
        foreach ($removedSids as $sid) {
            $this->info($sid);
        }
    }

    private function processPlugin() : array
    {
        $sidArray = [];
        $sids = Sid::all();
        $sids->each(function (Sid $sid) use (&$sidArray) {
            $sidArray[] = $this->generateSidKey($sid->airfield->code, $sid->identifier);
        });
        return $sidArray;
    }

    /**
     * Get all of the SIDs out of the sector file.
     * @param $file
     * @return array
     */
    private function processSectorFile($file) : array
    {
        $sids = [];
        $inSidSection = false;
        while (($line = fgets($file)) !== false) {
            // Dont do anything until we find the SID section
            if ($this->enteringSidSection($line)) {
                $inSidSection = true;
                continue;
            }

            if (!$inSidSection) {
                continue;
            }

            // End of SIDs, stop
            if ($this->exitingSidSection($line)) {
                break;
            }

            $sanitisedLine = $this->truncateLineAtComment($line);
            if ($this->validSidLine($sanitisedLine)) {
                $sids[] = $this->parseSidData($sanitisedLine);
            }
        }

        return $sids;
    }

    private function truncateLineAtComment(string $line) : string
    {
        $commentPos = strpos($line, ';');
        return $commentPos === false ? $line : substr($line, 0, $commentPos);
    }

    private function validSidLine(string $line) : bool
    {
        return trim($line) !== '';
    }

    /**
     * Detect when entering the SID section of the sectorfile
     *
     * @param string $line
     * @return bool
     */
    private function enteringSidSection(string $line) : bool
    {
        return trim($line) === '[SIDSSTARS]';
    }

    /**
     * Detect when exiting the SID section of the sectorfile
     *
     * @param string $line
     * @return bool
     */
    private function exitingSidSection(string $line) : bool
    {
        return substr($line, 0, 4) === 'STAR';
    }

    private function parseSidData(string $line) : string
    {
        $sidDetails = explode(':', $line);
        return $sidDetails[1] . '.' . $this->stripDeprecatedMarkers($sidDetails[3]);
    }

    /**
     * Generate a key for each SID to do the comparision
     *
     * @param string $airfield
     * @param string $identifier
     * @return string
     */
    private function generateSidKey(string $airfield, string $identifier) : string
    {
        return $airfield . '.' . $identifier;
    }

    /**
     * Remove the deprecated markers from the SID
     *
     * @param string $identifier
     * @return string
     */
    private function stripDeprecatedMarkers(string $identifier) : string
    {
        return substr($identifier, 0, 1) === '#'
            ? substr($identifier, 1)
            : $identifier;
    }
}
