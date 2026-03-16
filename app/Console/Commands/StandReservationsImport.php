<?php

namespace App\Console\Commands;

use App\Imports\Stand\StandReservationsImport as Importer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Services\Stand\StandReservationPayloadRowsBuilder;
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
    public function handle(Importer $importer, StandReservationPayloadRowsBuilder $payloadRowsBuilder)
    {
        $fileName = $this->argument('file_name');

        if (!Storage::disk('imports')->exists($fileName)) {
            $this->error(sprintf('Import file not found: %s', $this->argument('file_name')));
            return 1;
        }

        $this->output->title('Starting stand reservations import');

        if ($this->fileIsJson($fileName)) {
            // JSON imports reuse the same payload format accepted by stand reservation plans.
            $payload = json_decode(Storage::disk('imports')->get($fileName), true);

            if (!is_array($payload)) {
                $this->error('Import file is not valid JSON');
                return 1;
            }

            $rows = array_is_list($payload)
                ? collect($payload)
                : $payloadRowsBuilder->fromPayload($payload);

            $importer->withOutput($this->output)->collection(
                // Convert schema payload data into the row format expected by the importer.
                $rows
            );
        } else {
            // CSV support remains for backwards compatibility with existing import workflows.
            $importer->withOutput($this->output)->import($fileName, 'imports', Excel::CSV);
        }

        $this->output->success('Stand reservations import complete');

        return 0;
    }

    // File extension check keeps import routing simple and backwards compatible.
    private function fileIsJson(string $fileName): bool
    {
        return str_ends_with(strtolower($fileName), '.json');
    }
}
