<?php

namespace App\Console\Commands;

use App\Imports\Srd\SrdImport as ImportHelper;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
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

        Schema::disableForeignKeyConstraints();

        // Drop the SRD Notes
        $this->output->comment('Dropping SRD notes and route links');
        DB::table('srd_note_srd_route')->truncate();
        $this->output->comment('Dropping SRD notes');
        DB::table('srd_notes')->truncate();
        $this->output->comment('Dropping SRD routes');
        DB::table('srd_routes')->truncate();

        Schema::enableForeignKeyConstraints();

        // Import the data
        (new ImportHelper())->withOutput($this->output)->import($this->argument('file_name'), 'imports');
        $this->output->success('SRD import complete');
    }
}
