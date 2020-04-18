<?php

namespace App\Imports;

use App\BaseFunctionalTestCase;
use App\Models\Srd\SrdRoute;
use Illuminate\Console\OutputStyle;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Events\BeforeSheet;
use Mockery;

class SrdRoutesImportTest extends BaseFunctionalTestCase
{
    /**
     * @var SrdRoutesImport
     */
    private $import;

    public function setUp(): void
    {
        parent::setUp();
        // Empty the table first
        SrdRoute::all()->each(function (SrdRoute $route) {
            $route->delete();
        });
        $this->import = new SrdRoutesImport();
        $this->import->withOutput(Mockery::spy(OutputStyle::class));
    }

    public function testItProcessesModel()
    {
        $collection = new Collection();
        $collection->push(
            [
                'EGGD',
                'WOTAN',
                '140',
                '150',
                'L9 KENET',
                'OCK1A',
                'EGLL',
                'Notes: 1 - 2',
            ]
        );

        $this->import->collection($collection);
        $this->assertCount(1, SrdRoute::all());

        $model = SrdRoute::where('origin', 'EGGD')->first();
        $this->assertEquals('EGGD', $model->origin);
        $this->assertEquals('EGLL', $model->destination);
        $this->assertEquals('WOTAN', $model->sid);
        $this->assertEquals('OCK1A', $model->star);
        $this->assertSame(14000, $model->minimum_level);
        $this->assertSame(15000, $model->maximum_level);
        $this->assertEquals('L9 KENET', $model->route_segment);
        $this->assertEquals([1, 2], $model->notes()->pluck('id')->toArray());
    }

    public function testItProcessesMinimumCruise()
    {
        $collection = new Collection();
        $collection->push(
            [
                'EGGD',
                'WOTAN',
                'MC',
                '150',
                'L9 KENET',
                'OCK1A',
                'EGLL',
                ''
            ]
        );

        $this->import->collection($collection);
        $this->assertCount(1, SrdRoute::all());

        $model = SrdRoute::where('origin', 'EGGD')->first();
        $this->assertEquals('EGGD', $model->origin);
        $this->assertEquals('EGLL', $model->destination);
        $this->assertEquals('WOTAN', $model->sid);
        $this->assertEquals('OCK1A', $model->star);
        $this->assertNull($model->minimum_level);
        $this->assertSame(15000, $model->maximum_level);
        $this->assertEquals('L9 KENET', $model->route_segment);
    }

    public function testItProcessesNoRouteString()
    {
        $collection = new Collection();
        $collection->push([
            'EGGD',
            'WOTAN',
            'MC',
            '150',
            null,
            'OCK1A',
            'EGLL',
            ''
        ]);

        $this->import->collection($collection);
        $this->assertCount(1, SrdRoute::all());

        $model = SrdRoute::where('origin', 'EGGD')->first();
        $this->assertEquals('EGGD', $model->origin);
        $this->assertEquals('EGLL', $model->destination);
        $this->assertEquals('WOTAN', $model->sid);
        $this->assertEquals('OCK1A', $model->star);
        $this->assertNull($model->minimum_level);
        $this->assertSame(15000, $model->maximum_level);
        $this->assertEquals('', $model->route_segment);
    }

    public function testItProcessesMultipleModels()
    {
        $collection = new Collection();
        $collection->push(
            [
                'EGGD',
                'WOTAN',
                '140',
                '150',
                'L9 KENET',
                'OCK1A',
                'EGLL',
                ''
            ]
        );
        $collection->push(
            [
                'EGFF',
                'ALVIN',
                '140',
                '150',
                'L9 KENET',
                'OCK1A',
                'EGLL',
                ''
            ]
        );

        $this->import->collection($collection);
        $this->assertCount(2, SrdRoute::all());

        $model1 = SrdRoute::where('origin', 'EGGD')->first();
        $this->assertEquals('EGGD', $model1->origin);
        $this->assertEquals('EGLL', $model1->destination);
        $this->assertEquals('WOTAN', $model1->sid);
        $this->assertEquals('OCK1A', $model1->star);
        $this->assertSame(14000, $model1->minimum_level);
        $this->assertSame(15000, $model1->maximum_level);
        $this->assertEquals('L9 KENET', $model1->route_segment);

        $model2 = SrdRoute::where('origin', 'EGFF')->first();
        $this->assertEquals('EGFF', $model2->origin);
        $this->assertEquals('EGLL', $model2->destination);
        $this->assertEquals('ALVIN', $model2->sid);
        $this->assertEquals('OCK1A', $model2->star);
        $this->assertSame(14000, $model2->minimum_level);
        $this->assertSame(15000, $model2->maximum_level);
        $this->assertEquals('L9 KENET', $model2->route_segment);
    }

    public function testItStartsOnRowTwo()
    {
        $this->assertEquals(2, (new SrdRoutesImport())->startRow());
    }

    public function testItSubscribesToBeforeSheetEvents()
    {
        $this->assertArrayHasKey(BeforeSheet::class, (new SrdRoutesImport())->registerEvents());
    }
}
