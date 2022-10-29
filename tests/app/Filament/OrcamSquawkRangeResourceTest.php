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
    use ChecksFilamentActionVisibility;

    public function testItCreatesASquawkRange()
    {
        Livewire::test(ManageOrcamSquawkRanges::class)
            ->callPageAction(
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
            ->callPageAction(
                'create',
                [
                    'first' => '123a',
                    'last' => '2345',
                    'origin' => 'EG',
                ]
            )
            ->assertHasPageActionErrors(['first']);
    }

    public function testItDoesntCreateARangeIfLastInvalid()
    {
        Livewire::test(ManageOrcamSquawkRanges::class)
            ->callPageAction(
                'create',
                [
                    'first' => '1234',
                    'last' => '234b',
                    'origin' => 'EG',
                ]
            )
            ->assertHasPageActionErrors(['last']);
    }

    public function testItDoesntCreateARangeIfOriginMissing()
    {
        Livewire::test(ManageOrcamSquawkRanges::class)
            ->callPageAction(
                'create',
                [
                    'first' => '1234',
                    'last' => '2345',
                ]
            )
            ->assertHasPageActionErrors(['origin']);
    }

    public function testItDoesntCreateARangeIfOriginInvalid()
    {
        Livewire::test(ManageOrcamSquawkRanges::class)
            ->callPageAction(
                'create',
                [
                    'first' => '1234',
                    'last' => '2345',
                    'origin' => 'EGA,',
                ]
            )
            ->assertHasPageActionErrors(['origin']);
    }

    protected function getCreateText(): string
    {
        return 'Create orcam squawk range';
    }

    protected function getIndexText(): array
    {
        return ['Orcam Squawk Ranges', '0101', '0101', 'KJ'];
    }

    protected function resourceClass(): string
    {
        return OrcamSquawkRangeResource::class;
    }

    protected function resourceListingClass(): string
    {
        return ManageOrcamSquawkRanges::class;
    }

    protected function resourceRecordClass(): string
    {
        return OrcamSquawkRange::class;
    }

    protected function resourceId(): int|string
    {
        return 1;
    }

    protected function writeResourceTableActions(): array
    {
        return ['edit'];
    }

    protected function writeResourcePageActions(): array
    {
        return ['create'];
    }
}
