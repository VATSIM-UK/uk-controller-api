<?php

namespace App\Console\Commands;

use App\BaseFunctionalTestCase;
use App\Imports\Wake\RecatImporter;
use App\Models\Dependency\Dependency;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use Maatwebsite\Excel\Excel;
use Mockery;

class RecatCategoriesImportTest extends BaseFunctionalTestCase
{
    private $mockImporter;

    public function setUp(): void
    {
        parent::setUp();
        $this->mockImporter = Mockery::mock(RecatImporter::class);
    }

    public function testItThrowsExceptionIfFileNotFound()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Import file not found: recat.csv');
        Storage::fake('imports');

        Artisan::call('wake:import-recat recat.csv');
    }

    public function testItCallsImporter()
    {
        Storage::fake('imports');
        Storage::disk('imports')->put('recat.csv', 'testdata');

        $this->mockImporter->shouldReceive('withOutput')
            ->andReturnSelf();

        $this->mockImporter->shouldReceive('import')
            ->with('recat.csv', 'imports', Excel::CSV);

        Artisan::call('wake:import-recat recat.csv');
    }

    public function testItTouchesRecatDependencyAfterImport()
    {
        $dependency = Dependency::create(
            [
                'key' => 'DEPENDENCY_RECAT',
                'uri' => 'wake-category/recat/dependency',
                'local_file' => 'recat.json',
            ],
        );
        $dependency->updated_at = null;
        $dependency->save();

        Storage::fake('imports');
        Storage::disk('imports')->put('recat.csv', 'testdata');

        Artisan::call('wake:import-recat recat.csv');
        $dependency->refresh();
        $this->assertNotNull($dependency->updated_at);
    }
}
