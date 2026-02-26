<?php

namespace App\Console\Commands;

use App\Imports\Stand\StandReservationsImport as Importer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Support\StandReservationPayloadRows;
use Maatwebsite\Excel\Excel;

class StandReservationsImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stand-reservations:import {file_name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import stands reservations';

    /**
     * Execute the console command.
     *
     * @param Importer $importer
     * @return mixed
     */
    public function handle(Importer $importer)
    {
        $fileName = $this->argument('file_name');

        if (!Storage::disk('imports')->exists($fileName)) {
            $this->error(sprintf('Import file not found: %s', $this->argument('file_name')));
            return 1;
        }

        $this->output->title('Starting stand reservations import');

        if ($this->fileIsJson($fileName)) {
            $payload = json_decode(Storage::disk('imports')->get($fileName), true);

            if (!is_array($payload)) {
                $this->error('Import file is not valid JSON');
                return 1;
            }

            $importer->withOutput($this->output)->collection(
                StandReservationPayloadRows::fromPayload($payload)
            );
        } else {
            $importer->withOutput($this->output)->import($fileName, 'imports', Excel::CSV);
        }

        $this->output->success('Stand reservations import complete');

        return 0;
    }

    private function fileIsJson(string $fileName): bool
    {
        return str_ends_with(strtolower($fileName), '.json');
    }
}
