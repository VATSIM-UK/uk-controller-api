<?php

namespace App\Imports\Srd;

use App\BaseUnitTestCase;
use Illuminate\Console\OutputStyle;
use Mockery;

class SrdImportTest extends BaseUnitTestCase
{
    /**
     * @var SrdImport
     */
    private $importer;

    public function setUp(): void
    {
        parent::setUp();
        $this->importer = $this->app->make(SrdImport::class)->withOutput(Mockery::mock(OutputStyle::class));
    }

    public function testItReturnsSheets()
    {
        $expected = [
            'Notes' => SrdNotesImport::class,
            'Routes' => SrdRoutesImport::class,
        ];
        $sheets = $this->importer->sheets();
        foreach ($expected as $key => $instance)
        {
            $this->assertInstanceOf($instance, $sheets[$key]);
        }
    }

    public function testItDoesNothingOnUnknownSheets()
    {
        $this->expectNotToPerformAssertions();
        $this->importer->onUnknownSheet('sheet');
    }
}
