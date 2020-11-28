<?php

namespace App\Console\Commands;

use App\Imports\Stand\StandReservationsImport as Importer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
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
        if (!Storage::disk('imports')->exists($this->argument('file_name'))) {
            throw new InvalidArgumentException(sprintf('Import file not found: %s', $this->argument('file_name')));
        }

        $this->output->title('Starting stand reservations import');
        $importer->withOutput($this->output)->import($this->argument('file_name'), 'imports', Excel::CSV);
        $this->output->success('Stand reservations import complete');
    }
}
