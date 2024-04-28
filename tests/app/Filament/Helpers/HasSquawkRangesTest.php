<?php

namespace App\Filament\Helpers;

use App\BaseFilamentTestCase;
use App\Filament\Resources\CcamsSquawkRangeResource\Pages\ManageCcamsSquawkRange;
use App\Models\Squawk\Ccams\CcamsSquawkRange;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;

class HasSquawkRangesTest extends BaseFilamentTestCase
{
    #[DataProvider('rangeProvider')]
    public function testItHasSquawkRanges(string $first, string $last)
    {
        Livewire::test(ManageCcamsSquawkRange::class)
            ->callTableAction(
                'edit',
                CcamsSquawkRange::findOrFail(1),
                [
                    'first' => $first,
                    'last' => $last
                ]
            )
            ->assertHasNoTableActionErrors();
    }

    public static function rangeProvider(): array
    {
        return [
            'Low' => ['0001', '0005'],
            'Low Crossing Tens' => ['0001', '0312'],
            'Mid range' => ['2324', '4243'],
            'Mid range 2' => ['5241', '5777'],
            'High range' => ['6666', '6771'],
            'High range 2' => ['7571', '7763'],
            'Single squawk' => ['7571', '7571'],
        ];
    }

    #[DataProvider('badRangeProvider')]
    public function testItHasErrorsOnBadSquawks(string|null $first, string|null $last, array $expectedErrors)
    {
        Livewire::test(ManageCcamsSquawkRange::class)
            ->callTableAction(
                'edit',
                CcamsSquawkRange::findOrFail(1),
                [
                    'first' => $first,
                    'last' => $last
                ]
            )
            ->assertHasTableActionErrors($expectedErrors);
    }

    public static function badRangeProvider(): array
    {
        return [
            'First null' => [null, '2312', ['first']],
            'Last null' => ['2114', null, ['last']],
            'First not valid squawk' => ['2218', '2241', ['first']],
            'First not numeric' => ['a231', '2241', ['first']],
            'Last not valid squawk' => ['2231', '2288', ['last']],
            'Last not numeric' => ['2231', '237a', ['last']],
            'Last less than first' => ['2231', '2230', ['last']],
            'Both bad' => ['aaaa', '237a', ['first', 'last']],
        ];
    }
}
