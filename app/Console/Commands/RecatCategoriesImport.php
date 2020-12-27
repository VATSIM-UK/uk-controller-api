<?php

namespace App\Console\Commands;

use App\Imports\Wake\RecatImporter;
use App\Services\DependencyService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use Maatwebsite\Excel\Excel;

class RecatCategoriesImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wake:import-recat {file_name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import RECAT-EU categories as CSV';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(RecatImporter $importer)
    {
        if (!Storage::disk('imports')->exists($this->argument('file_name'))) {
            throw new InvalidArgumentException(sprintf('Import file not found: %s', $this->argument('file_name')));
        }

        $this->output->title('Starting RECAT-EU category import');
        $importer->withOutput($this->output)->import($this->argument('file_name'), 'imports', Excel::CSV);
        DependencyService::touchDependencyByKey('DEPENDENCY_RECAT');
        $this->output->success('RECAT-EU category import complete');
    }
}
