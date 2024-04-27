<?php

namespace App\Filament;

use App\BaseFilamentTestCase;
use App\Filament\AccessCheckingHelpers\ChecksManageRecordsFilamentAccess;
use App\Filament\Resources\OrcamSquawkRangeResource;
use App\Filament\Resources\OrcamSquawkRangeResource\Pages\ManageOrcamSquawkRanges;
use App\Models\Squawk\Orcam\OrcamSquawkRange;
use Livewire\Livewire;

class OrcamSquawkRangeResourceTest extends BaseFilamentTestCase
{
    use ChecksManageRecordsFilamentAccess;
    use ChecksDefaultFilamentActionVisibility;

    public function testItCreatesASquawkRange()
    {
        Livewire::test(ManageOrcamSquawkRanges::class)
            ->callAction(
                'create',
                [
                    'first' => '1234',
                    'last' => '2345',
                    'origin' => 'EG',
                ]
            )
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'orcam_squawk_ranges',
            [
                'first' => '1234',
                'last' => '2345',
                'origin' => 'EG',
            ]
        );
    }

    public function testItDoesntCreateARangeIfFirstInvalid()
    {
        Livewire::test(ManageOrcamSquawkRanges::class)
            ->callAction(
                'create',
                [
                    'first' => '123a',
                    'last' => '2345',
                    'origin' => 'EG',
                ]
            )
            ->assertHasActionErrors(['first']);
    }

    public function testItDoesntCreateARangeIfLastInvalid()
    {
        Livewire::test(ManageOrcamSquawkRanges::class)
            ->callAction(
                'create',
                [
                    'first' => '1234',
                    'last' => '234b',
                    'origin' => 'EG',
                ]
            )
            ->assertHasActionErrors(['last']);
    }

    public function testItDoesntCreateARangeIfOriginMissing()
    {
        Livewire::test(ManageOrcamSquawkRanges::class)
            ->callAction(
                'create',
                [
                    'first' => '1234',
                    'last' => '2345',
                ]
            )
            ->assertHasActionErrors(['origin']);
    }

    public function testItDoesntCreateARangeIfOriginInvalid()
    {
        Livewire::test(ManageOrcamSquawkRanges::class)
            ->callAction(
                'create',
                [
                    'first' => '1234',
                    'last' => '2345',
                    'origin' => 'EGA,',
                ]
            )
            ->assertHasActionErrors(['origin']);
    }

    public function testItEditsASquawkRange()
    {
        Livewire::test(ManageOrcamSquawkRanges::class)
            ->callTableAction(
                'edit',
                OrcamSquawkRange::findOrFail(1),
                [
                    'first' => '3456',
                    'last' => '4567',
                    'origin' => 'AF',
                ]
            )
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'orcam_squawk_ranges',
            [
                'id' => 1,
                'first' => '3456',
                'last' => '4567',
                'origin' => 'AF',
            ]
        );
    }

    public function testItDoesntEditARangeIfFirstInvalid()
    {
        Livewire::test(ManageOrcamSquawkRanges::class)
            ->callTableAction(
                'edit',
                OrcamSquawkRange::findOrFail(1),
                [
                    'first' => '345a',
                    'last' => '4567',
                    'origin' => 'AF',
                ]
            )
            ->assertHasTableActionErrors(['first']);
    }

    public function testItDoesntEditARangeIfLastInvalid()
    {
        Livewire::test(ManageOrcamSquawkRanges::class)
            ->callTableAction(
                'edit',
                OrcamSquawkRange::findOrFail(1),
                [
                    'first' => '3456',
                    'last' => '456a',
                    'origin' => 'AF',
                ]
            )
            ->assertHasTableActionErrors(['last']);
    }

    public function testItDoesntEditARangeIfOriginMissing()
    {
        Livewire::test(ManageOrcamSquawkRanges::class)
            ->callTableAction(
                'edit',
                OrcamSquawkRange::findOrFail(1),
                [
                    'first' => '3456',
                    'last' => '4567',
                    'origin' => null,
                ]
            )
            ->assertHasTableActionErrors(['origin']);
    }

    public function testItDoesntEditARangeIfOriginInvalid()
    {
        Livewire::test(ManageOrcamSquawkRanges::class)
            ->callTableAction(
                'edit',
                OrcamSquawkRange::findOrFail(1),
                [
                    'first' => '3456',
                    'last' => '4567',
                    'origin' => 'AAAAAAAAAA',
                ]
            )
            ->assertHasTableActionErrors(['origin']);
    }

    protected function getCreateText(): string
    {
        return 'Create orcam squawk range';
    }

    protected function getIndexText(): array
    {
        return ['Orcam Squawk Ranges', '0101', '0101', 'KJ'];
    }

    protected static function resourceClass(): string
    {
        return OrcamSquawkRangeResource::class;
    }

    protected static function resourceListingClass(): string
    {
        return ManageOrcamSquawkRanges::class;
    }

    protected static function resourceRecordClass(): string
    {
        return OrcamSquawkRange::class;
    }

    protected static function resourceId(): int|string
    {
        return 1;
    }

    protected static function writeResourceTableActions(): array
    {
        return ['edit'];
    }

    protected static function writeResourcePageActions(): array
    {
        return ['create'];
    }
}
