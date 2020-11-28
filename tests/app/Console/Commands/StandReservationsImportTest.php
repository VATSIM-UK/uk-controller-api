<?php

namespace App\Console\Commands;

use App\BaseFunctionalTestCase;
use App\Imports\Wake\Importer;
use App\Models\Dependency\Dependency;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use Maatwebsite\Excel\Excel;
use Mockery;

class StandReservationsImportTest extends BaseFunctionalTestCase
{
    private $mockImporter;

    public function setUp(): void
    {
        parent::setUp();
        $this->mockImporter = Mockery::mock(Importer::class);
    }

    public function testItThrowsExceptionIfFileNotFound()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Import file not found: stands.csv');
        Storage::fake('imports');

        Artisan::call('stand-reservations:import stands.csv');
    }

    public function testItCallsImporter()
    {
        Storage::fake('imports');
        Storage::disk('imports')->put('stands.csv', 'testdata');

        $this->mockImporter->shouldReceive('withOutput')
            ->andReturnSelf();

        $this->mockImporter->shouldReceive('import')
            ->with('stands.csv', 'imports', Excel::CSV);

        Artisan::call('stand-reservations:import stands.csv');
    }
}
