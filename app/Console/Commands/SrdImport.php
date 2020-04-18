<?php

namespace App\Console\Commands;

use App\Imports\SrdImport as ImportHelper;
use App\Models\Srd\SrdNote;
use App\Models\Srd\SrdRoute;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
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
        $this->output->section('Dropping existing SRD data');

        // Drop the SRD Notes
        $this->output->comment('Dropping SRD notes');
        $notes = SrdNote::all();
        $this->output->progressStart($notes->count());
        $notes->each(function (SrdNote $note) {
            $note->delete();
            $this->output->progressAdvance();
        });
        $this->output->progressFinish();

        // Drop the SRD routes
        $this->output->comment('Dropping SRD routes');
        $routes = SrdRoute::all();
        $this->output->progressStart($routes->count());
        $routes->each(function (SrdRoute $route) {
            $route->delete();
            $this->output->progressAdvance();
        });
        $this->output->progressFinish();

        // Import the data
        (new ImportHelper())->withOutput($this->output)->import($this->argument('file_name'), 'imports');
        $this->output->success('SRD import complete');
    }
}
