<?php

namespace App\Console\Commands;

use App\Imports\Srd\SrdImport as ImportHelper;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;

class SrdImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'srd:import {file_name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports routes from an SRD XLS file';

    /**
     * @var ImportHelper
     */
    private ImportHelper $importer;

    public function __construct(ImportHelper $importer)
    {
        parent::__construct();
        $this->importer = $importer;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (!Storage::disk('imports')->exists($this->argument('file_name'))) {
            throw new InvalidArgumentException(sprintf('Import file not found: %s', $this->argument('file_name')));
        }

        $this->output->title('Starting SRD import');
        $this->output->section('Dropping existing SRD data');

        DB::transaction(function () {
            // Clear the existing data
            $this->output->comment('Dropping SRD notes');
            DB::table('srd_notes')->delete();
            $this->output->comment('Dropping SRD routes');
            DB::table('srd_routes')->delete();

            // Import the data
            $this->output->comment('About to start SRD import');
            $this->importer->withOutput($this->output)->import($this->argument('file_name'), 'imports');
        });
        $this->output->success('SRD import complete');
    }
}
