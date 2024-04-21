<?php

namespace App\Filament;

use App\BaseFilamentTestCase;
use App\Filament\AccessCheckingHelpers\ChecksManageRecordsFilamentAccess;
use App\Filament\Resources\NonAssignableSquawkCodeResource;
use App\Filament\Resources\NonAssignableSquawkCodeResource\Pages\ManageNonAssignnableSquawkCodeRanges;
use App\Models\Squawk\Reserved\NonAssignableSquawkCode;
use Illuminate\Support\Str;
use Livewire\Livewire;

class NonAssignableSquawkCodeResourceTest extends BaseFilamentTestCase
{
    use ChecksManageRecordsFilamentAccess;
    use ChecksDefaultFilamentActionVisibility;

    public function testItCreatesASquawkCode()
    {
        Livewire::test(ManageNonAssignnableSquawkCodeRanges::class)
            ->callAction(
                'create',
                [
                    'code' => '2512',
                    'description' => 'abc',
                ]
            )
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'non_assignable_squawk_codes',
            [
                'code' => '2512',
                'description' => 'abc',
            ]
        );
    }

    public function testItDoesntCreateIfCodeNotUnique()
    {
        Livewire::test(ManageNonAssignnableSquawkCodeRanges::class)
            ->callAction(
                'create',
                [
                    'code' => '1234',
                    'description' => 'abc',
                ]
            )
            ->assertHasActionErrors(['code']);
    }

    public function testItDoesntCreateIfCodeInvalid()
    {
        Livewire::test(ManageNonAssignnableSquawkCodeRanges::class)
            ->callAction(
                'create',
                [
                    'code' => '123a',
                    'description' => 'abc',
                ]
            )
            ->assertHasActionErrors(['code']);
    }

    public function testItDoesntCreateARangeIfDescriptionMissing()
    {
        Livewire::test(ManageNonAssignnableSquawkCodeRanges::class)
            ->callAction(
                'create',
                [
                    'first' => '1234',
                ]
            )
            ->assertHasActionErrors(['description']);
    }

    public function testItDoesntCreateARangeIfDescriptionTooShort()
    {
        Livewire::test(ManageNonAssignnableSquawkCodeRanges::class)
            ->callAction(
                'create',
                [
                    'first' => '1234',
                    'description' => '',
                ]
            )
            ->assertHasActionErrors(['description']);
    }

    public function testItDoesntCreateARangeIfDescriptionTooLong()
    {
        Livewire::test(ManageNonAssignnableSquawkCodeRanges::class)
            ->callAction(
                'create',
                [
                    'first' => '1234',
                    'description' => Str::padRight('', 256, 'a'),
                ]
            )
            ->assertHasActionErrors(['description']);
    }

    public function testItEditsASquawkCode()
    {
        Livewire::test(ManageNonAssignnableSquawkCodeRanges::class)
            ->callTableAction(
                'edit',
                NonAssignableSquawkCode::findOrFail(1),
                [
                    'code' => '5621',
                    'description' => 'abc',
                ]
            )
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'non_assignable_squawk_codes',
            [
                'id' => 1,
                'code' => '5621',
                'description' => 'abc',
            ]
        );
    }

    public function testItEditsASquawkCodeToChangeTheDescription()
    {
        Livewire::test(ManageNonAssignnableSquawkCodeRanges::class)
            ->callTableAction(
                'edit',
                NonAssignableSquawkCode::findOrFail(1),
                [
                    'code' => '7500',
                    'description' => 'abc',
                ]
            )
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'non_assignable_squawk_codes',
            [
                'id' => 1,
                'code' => '7500',
                'description' => 'abc',
            ]
        );
    }

    public function testItEditingFailsIfNewCodeIsNotUnique()
    {
        Livewire::test(ManageNonAssignnableSquawkCodeRanges::class)
            ->callTableAction(
                'edit',
                NonAssignableSquawkCode::findOrFail(1),
                [
                    'code' => '7600',
                    'description' => 'abc',
                ]
            )
            ->assertHasTableActionErrors(['code']);
    }

    public function testItEditingFailsIfNewCodeIsInvalid()
    {
        Livewire::test(ManageNonAssignnableSquawkCodeRanges::class)
            ->callTableAction(
                'edit',
                NonAssignableSquawkCode::findOrFail(1),
                [
                    'code' => '760a',
                    'description' => 'abc',
                ]
            )
            ->assertHasTableActionErrors(['code']);
    }

    public function testItEditingFailsIfNewDescriptionIsMissing()
    {
        Livewire::test(ManageNonAssignnableSquawkCodeRanges::class)
            ->callTableAction(
                'edit',
                NonAssignableSquawkCode::findOrFail(1),
                [
                    'code' => '7231',
                    'description' => null,
                ]
            )
            ->assertHasTableActionErrors(['description']);
    }

    public function testItEditingFailsIfNewDescriptionIsTooShort()
    {
        Livewire::test(ManageNonAssignnableSquawkCodeRanges::class)
            ->callTableAction(
                'edit',
                NonAssignableSquawkCode::findOrFail(1),
                [
                    'code' => '7231',
                    'description' => '',
                ]
            )
            ->assertHasTableActionErrors(['description']);
    }

    public function testItEditingFailsIfNewDescriptionIsTooLong()
    {
        Livewire::test(ManageNonAssignnableSquawkCodeRanges::class)
            ->callTableAction(
                'edit',
                NonAssignableSquawkCode::findOrFail(1),
                [
                    'code' => '7231',
                    'description' => Str::padRight('', 256, 'a'),
                ]
            )
            ->assertHasTableActionErrors(['description']);
    }

    protected function getCreateText(): string
    {
        return 'Create non assignable squawk code';
    }

    protected function getIndexText(): array
    {
        return ['Non Assignable Squawk Codes', '7500', 'Never to be used on the VATSIM network'];
    }

    protected static function resourceClass(): string
    {
        return NonAssignableSquawkCodeResource::class;
    }

    protected static function resourceListingClass(): string
    {
        return ManageNonAssignnableSquawkCodeRanges::class;
    }

    protected static function resourceRecordClass(): string
    {
        return NonAssignableSquawkCode::class;
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
