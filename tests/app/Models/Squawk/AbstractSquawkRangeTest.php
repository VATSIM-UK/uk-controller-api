<?php

namespace App\Models\Squawk;

use App\BaseUnitTestCase;
use App\Models\Squawk\Ccams\CcamsSquawkRange;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;

class AbstractSquawkRangeTest extends BaseUnitTestCase
{
    #[DataProvider('exceptionTestProvider')]
    public function testItThrowsExceptionsOnBadData(
        string $first,
        string $last,
        string $exceptionClass,
        string $exceptionMessage
    )
    {
        $this->expectException($exceptionClass);
        $this->expectExceptionMessage($exceptionMessage);
        $range = new CcamsSquawkRange(['first' => $first, 'last' => $last]);
        $range->getAllSquawksInRange();
    }

    public static function exceptionTestProvider(): array
    {
        return [
            ['111', '0101', InvalidArgumentException::class, 'Invalid first squawk of range: 111'],
            [
                '00001',
                '0101',
                InvalidArgumentException::class,
                'Invalid first squawk of range: 00001'
            ],
            [
                '0108',
                '0101',
                InvalidArgumentException::class,
                'Invalid first squawk of range: 0108'
            ],
            [
                '0190',
                '0101',
                InvalidArgumentException::class,
                'Invalid first squawk of range: 0190'
            ],
            [
                '0981',
                '0101',
                InvalidArgumentException::class,
                'Invalid first squawk of range: 0981'
            ],
            ['0101', '111', InvalidArgumentException::class, 'Invalid last squawk of range: 111'],
            [
                '0101',
                '00001',
                InvalidArgumentException::class,
                'Invalid last squawk of range: 00001'
            ],
            ['0101', '0108', InvalidArgumentException::class, 'Invalid last squawk of range: 0108'],
            ['0101', '0190', InvalidArgumentException::class, 'Invalid last squawk of range: 0190'],
            ['0101', '0981', InvalidArgumentException::class, 'Invalid last squawk of range: 0981'],
        ];
    }

    #[DataProvider('rangeProvider')]
    public function testItReturnsCorrectSquawksInRange(string $first, string $last, array $expected)
    {
        $range = new CcamsSquawkRange(['first' => $first, 'last' => $last]);
        $expected = new Collection($expected);
        $this->assertEquals($expected, $range->getAllSquawksInRange());
    }

    public static function rangeProvider(): array
    {
        return [
            // Within boundary
            ['0000', '0000', ['0000']],
            ['7777', '7777', ['7777']],
            ['0000', '0001', ['0000', '0001']],
            ['0000', '0007', ['0000', '0001', '0002', '0003', '0004', '0005', '0006', '0007']],
            ['0010', '0017', ['0010', '0011', '0012', '0013', '0014', '0015', '0016', '0017']],
            ['0170', '0177', ['0170', '0171', '0172', '0173', '0174', '0175', '0176', '0177']],

            // Across boundary
            ['0105', '0112', ['0105', '0106', '0107', '0110', '0111', '0112']],
            ['0176', '0202', ['0176', '0177', '0200', '0201', '0202']],
            ['1776', '2003', ['1776', '1777', '2000', '2001', '2002', '2003']],
        ];
    }

    #[DataProvider('inRangeProvider')]
    public function testItReturnsIfSquawkIsInRange(
        string $first,
        string $last,
        string $code,
        bool $inRange
    )
    {
        $range = new CcamsSquawkRange(['first' => $first, 'last' => $last]);
        $this->assertEquals($inRange, $range->squawkInRange($code));
    }

    public static function inRangeProvider(): array
    {
        return [
            ['0000', '0007', '0000', true],
            ['0000', '0007', '0005', true],
            ['0000', '0007', '0007', true],
            ['1000', '1007', '0777', false],
            ['1000', '1007', '1010', false],
            ['1000', '1020', '1010', true],
            ['1000', '1020', '1011', true],
        ];
    }
}
