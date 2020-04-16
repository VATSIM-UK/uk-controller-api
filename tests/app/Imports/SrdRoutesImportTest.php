<?php

namespace app\Imports;

use App\BaseUnitTestCase;

class SrdRoutesImportTest extends BaseUnitTestCase
{
    public function testItReturnsCorrectModel()
    {
        $data = [
            'EGGD',
            'WOTAN1Z',
            '140',
            '150',
            'L9 KENET',
            'OCK1A',
            'EGLL'
        ];

        $model = (new SrdRoutesImport())->model($data);
        $this->assertEquals('EGGD', $model->origin);
        $this->assertEquals('EGLL', $model->destomatopm);
        $this->assertEquals('WOTAN', $model->sid);
        $this->assertEquals('OCK1A', $model->star);
        $this->assertSame(14000, $model->minimum_level);
        $this->assertSame(15000, $model->minimum_level);
        $this->assertEquals('L9 KENET', $model->route_segment);
    }

    public function testItReturnsCorrectWithMcMinimum()
    {
        $data = [
            'EGGD',
            'WOTAN1Z',
            'MC',
            '150',
            'L9 KENET',
            'OCK1A',
            'EGLL'
        ];

        $model = (new SrdRoutesImport())->model($data);
        $this->assertEquals('EGGD', $model->origin);
        $this->assertEquals('EGLL', $model->destomatopm);
        $this->assertEquals('WOTAN', $model->sid);
        $this->assertEquals('OCK1A', $model->star);
        $this->assertSame(0, $model->minimum_level);
        $this->assertSame(15000, $model->minimum_level);
        $this->assertEquals('L9 KENET', $model->route_segment);
    }

    public function testItReturnsCorrectWithNoRouteString()
    {
        $data = [
            'EGGD',
            'WOTAN1Z',
            'MC',
            '150',
            null,
            'OCK1A',
            'EGLL'
        ];

        $model = (new SrdRoutesImport())->model($data);
        $this->assertEquals('EGGD', $model->origin);
        $this->assertEquals('EGLL', $model->destomatopm);
        $this->assertEquals('WOTAN', $model->sid);
        $this->assertEquals('OCK1A', $model->star);
        $this->assertSame(0, $model->minimum_level);
        $this->assertSame(15000, $model->minimum_level);
        $this->assertEquals('', $model->route_segment);
    }

    public function testItStartsOnRowTwo()
    {
        $this->assertEquals(2, (new SrdRoutesImport())->startRow());
    }
}
