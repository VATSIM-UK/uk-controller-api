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

class WakeCategoriesImportTest extends BaseFunctionalTestCase
{
    private $mockImporter;

    public function setUp(): void
    {
        parent::setUp();
        $this->mockImporter = Mockery::mock(Importer::class);
        $this->app->instance(Importer::class, $this->mockImporter);
    }

    public function testItThrowsExceptionIfFileNotFound()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Import file not found: wake.csv');
        Storage::fake('imports');

        Artisan::call('wake:import wake.csv');
    }

    public function testItCallsImporter()
    {
        Storage::fake('imports');
        Storage::disk('imports')->put('wake.csv', 'testdata');

        $this->mockImporter->shouldReceive('withOutput')
            ->once()
            ->andReturnSelf();

        $this->mockImporter->shouldReceive('import')
            ->once()
            ->with('wake.csv', 'imports', Excel::CSV);

        Artisan::call('wake:import wake.csv');
    }

    public function testItTouchesWakeDependencyAfterImport()
    {
        $dependency = Dependency::create(
            [
                'key' => 'DEPENDENCY_WAKE',
                'action' => 'foo',
                'local_file' => 'wake-categories.json',
            ],
        );
        $dependency->updated_at = null;
        $dependency->save();

        Storage::fake('imports');
        Storage::disk('imports')->put('wake.csv', 'testdata');

        $this->mockImporter->shouldReceive('withOutput')
            ->once()
            ->andReturnSelf();

        $this->mockImporter->shouldReceive('import')
            ->once()
            ->with('wake.csv', 'imports', Excel::CSV);

        Artisan::call('wake:import wake.csv');
        $dependency->refresh();
        $this->assertNotNull($dependency->updated_at);
    }
}
