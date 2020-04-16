<?php

namespace App\Console\Commands;

use App\Imports\SrdImport as ImportHelper;
use App\Models\Srd\SrdRoute;
use Illuminate\Console\Command;
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
        $this->output->comment('Dropping existing SRD routes');
        SrdRoute::truncate();
        $this->output->comment('Importing new SRD routes');
        (new ImportHelper())->withOutput($this->output)->import($this->argument('file_name'), 'imports');
        $this->output->success('SRD import complete');
    }
}
