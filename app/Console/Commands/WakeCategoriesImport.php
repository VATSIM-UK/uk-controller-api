<?php

namespace App\Console\Commands;

use App\Imports\Wake\Importer;
use App\Services\DependencyService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use Maatwebsite\Excel\Excel;

class WakeCategoriesImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wake:import {file_name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import wake categories as CSV from a single sheet of the CAA categorisation document';

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

        $this->output->title('Starting wake category import');
        $importer->withOutput($this->output)->import($this->argument('file_name'), 'imports', Excel::CSV);
        DependencyService::touchDependencyByKey('DEPENDENCY_WAKE');
        $this->output->success('Wake category import complete');
    }
}
