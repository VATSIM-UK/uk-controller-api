<?php

namespace App\Filament;

use App\BaseFilamentTestCase;
use App\Filament\Resources\SidResource;
use App\Models\Sid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Livewire\Livewire;

class SidResourceTest extends BaseFilamentTestCase
{
    use ChecksDefaultFilamentAccess;

    public function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::now()->startOfSecond());
    }

    public function testItLoadsDataForView()
    {
        Sid::where('id', 1)->update(['initial_heading' => 350]);
        Livewire::test(SidResource\Pages\ViewSid::class, ['record' => 1])
            ->assertSet('data.runway_id', 1)
            ->assertSet('data.identifier', 'TEST1X')
            ->assertSet('data.initial_altitude', 3000)
            ->assertSet('data.initial_heading', 350)
            ->assertSet('data.handoff_id', 1);
    }

    public function testItCreatesASidWithFullData()
    {
        Livewire::test(SidResource\Pages\CreateSid::class)
            ->set('data.runway_id', 2)
            ->set('data.identifier', 'SID1X')
            ->set('data.initial_altitude', 4500)
            ->set('data.initial_heading', 215)
            ->set('data.handoff_id', 1)
            ->call('create')
            ->assertHasNoErrors();;

        $this->assertDatabaseHas(
            'sid',
            [
                'runway_id' => 2,
                'identifier' => 'SID1X',
                'initial_altitude' => 4500,
                'initial_heading' => 215,
                'handoff_id' => 1,
            ]
        );
    }

    public function testItCreatesASidWithMaximumInitialHeading()
    {
        Livewire::test(SidResource\Pages\CreateSid::class)
            ->set('data.runway_id', 2)
            ->set('data.identifier', 'SID1X')
            ->set('data.initial_altitude', 4500)
            ->set('data.initial_heading', 360)
            ->set('data.handoff_id', 1)
            ->call('create')
            ->assertHasNoErrors();;

        $this->assertDatabaseHas(
            'sid',
            [
                'runway_id' => 2,
                'identifier' => 'SID1X',
                'initial_altitude' => 4500,
                'initial_heading' => 360,
                'handoff_id' => 1,
            ]
        );
    }

    public function testItCreatesASidWithMinimumInitialHeading()
    {
        Livewire::test(SidResource\Pages\CreateSid::class)
            ->set('data.runway_id', 2)
            ->set('data.identifier', 'SID1X')
            ->set('data.initial_altitude', 4500)
            ->set('data.initial_heading', 1)
            ->set('data.handoff_id', 1)
            ->call('create')
            ->assertHasNoErrors();;

        $this->assertDatabaseHas(
            'sid',
            [
                'runway_id' => 2,
                'identifier' => 'SID1X',
                'initial_altitude' => 4500,
                'initial_heading' => 1,
                'handoff_id' => 1,
            ]
        );
    }

    public function testItCreatesASidWithMinimumData()
    {
        Livewire::test(SidResource\Pages\CreateSid::class)
            ->set('data.runway_id', 2)
            ->set('data.identifier', 'SID1X')
            ->set('data.initial_altitude', 4500)
            ->call('create')
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'sid',
            [
                'runway_id' => 2,
                'identifier' => 'SID1X',
                'initial_altitude' => 4500,
                'initial_heading' => null,
                'handoff_id' => null,
            ]
        );
    }

    public function testCreateFailsValidationIfInitialAltitudeNotNumber()
    {
        Livewire::test(SidResource\Pages\CreateSid::class)
            ->set('data.runway_id', 2)
            ->set('data.identifier', 'SID1X')
            ->set('data.initial_altitude', 'abc')
            ->call('create')
            ->assertHasErrors(['data.initial_altitude']);
    }

    public function testCreateFailsValidationIfInitialAltitudeTooSmall()
    {
        Livewire::test(SidResource\Pages\CreateSid::class)
            ->set('data.runway_id', 2)
            ->set('data.identifier', 'SID1X')
            ->set('data.initial_altitude', -1)
            ->call('create')
            ->assertHasErrors(['data.initial_altitude']);
    }

    public function testCreateFailsValidationIfInitialAltitudeTooBig()
    {
        Livewire::test(SidResource\Pages\CreateSid::class)
            ->set('data.runway_id', 2)
            ->set('data.identifier', 'SID1X')
            ->set('data.initial_altitude', 100000)
            ->call('create')
            ->assertHasErrors(['data.initial_altitude']);
    }

    public function testCreateFailsValidationIfInitialHeadingTooSmall()
    {
        Livewire::test(SidResource\Pages\CreateSid::class)
            ->set('data.runway_id', 2)
            ->set('data.identifier', 'SID1X')
            ->set('data.initial_altitude', 4500)
            ->set('data.initial_heading', -5)
            ->call('create')
            ->assertHasErrors(['data.initial_heading']);
    }

    public function testCreateFailsValidationIfInitialHeadingNorthAsZero()
    {
        Livewire::test(SidResource\Pages\CreateSid::class)
            ->set('data.runway_id', 2)
            ->set('data.identifier', 'SID1X')
            ->set('data.initial_altitude', 4500)
            ->set('data.initial_heading', 0)
            ->call('create')
            ->assertHasErrors(['data.initial_heading']);
    }

    public function testCreateFailsValidationIfInitialHeadingTooBig()
    {
        Livewire::test(SidResource\Pages\CreateSid::class)
            ->set('data.runway_id', 2)
            ->set('data.identifier', 'SID1X')
            ->set('data.initial_altitude', 4500)
            ->set('data.initial_heading', 361)
            ->call('create')
            ->assertHasErrors(['data.initial_heading']);
    }

    public function testCreateFailsValidationIfSidAlreadyExistsForRunway()
    {
        Livewire::test(SidResource\Pages\CreateSid::class)
            ->set('data.runway_id', 1)
            ->set('data.identifier', 'TEST1X')
            ->set('data.initial_altitude', 4500)
            ->set('data.initial_heading', 123)
            ->call('create')
            ->assertHasErrors(['data.identifier']);
    }

    public function testItLoadsDataForEdit()
    {
        Sid::where('id', 1)->update(['initial_heading' => 350]);
        Livewire::test(SidResource\Pages\EditSid::class, ['record' => 1])
            ->assertSet('data.runway_id', 1)
            ->assertSet('data.identifier', 'TEST1X')
            ->assertSet('data.initial_altitude', 3000)
            ->assertSet('data.initial_heading', 350)
            ->assertSet('data.handoff_id', 1);
    }

    public function testItEditsASidWithFullData()
    {
        Livewire::test(SidResource\Pages\EditSid::class, ['record' => 1])
            ->set('data.runway_id', 2)
            ->set('data.identifier', 'SID1X')
            ->set('data.initial_altitude', 4500)
            ->set('data.initial_heading', 215)
            ->set('data.handoff_id', 1)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'sid',
            [
                'id' => 1,
                'runway_id' => 1,
                'identifier' => 'SID1X',
                'initial_altitude' => 4500,
                'initial_heading' => 215,
                'handoff_id' => 1,
            ]
        );
    }

    public function testItEditsASidWithMaximumInitialHeading()
    {
        Livewire::test(SidResource\Pages\EditSid::class, ['record' => 1])
            ->set('data.identifier', 'SID1X')
            ->set('data.initial_altitude', 4500)
            ->set('data.initial_heading', 360)
            ->set('data.handoff_id', 1)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'sid',
            [
                'id' => 1,
                'runway_id' => 1,
                'identifier' => 'SID1X',
                'initial_altitude' => 4500,
                'initial_heading' => 360,
                'handoff_id' => 1,
            ]
        );
    }

    public function testItEditsASidWithMinimumInitialHeading()
    {
        Livewire::test(SidResource\Pages\EditSid::class, ['record' => 1])
            ->set('data.identifier', 'SID1X')
            ->set('data.initial_altitude', 4500)
            ->set('data.initial_heading', 1)
            ->set('data.handoff_id', 1)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'sid',
            [
                'id' => 1,
                'runway_id' => 1,
                'identifier' => 'SID1X',
                'initial_altitude' => 4500,
                'initial_heading' => 1,
                'handoff_id' => 1,
            ]
        );
    }

    public function testItEditsASidWithMinimumData()
    {
        Livewire::test(SidResource\Pages\EditSid::class, ['record' => 1])
            ->set('data.identifier', 'SID1X')
            ->set('data.initial_altitude', 4500)
            ->set('data.handoff_id', null)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'sid',
            [
                'id' => 1,
                'runway_id' => 1,
                'identifier' => 'SID1X',
                'initial_altitude' => 4500,
                'initial_heading' => null,
                'handoff_id' => null,
            ]
        );
    }

    public function testEditFailsValidationIfInitialAltitudeNotNumber()
    {
        Livewire::test(SidResource\Pages\EditSid::class, ['record' => 1])
            ->set('data.runway_id', 2)
            ->set('data.identifier', 'SID1X')
            ->set('data.initial_altitude', 'abc')
            ->call('save')
            ->assertHasErrors(['data.initial_altitude']);
    }

    public function testEditFailsValidationIfInitialAltitudeTooSmall()
    {
        Livewire::test(SidResource\Pages\EditSid::class, ['record' => 1])
            ->set('data.runway_id', 2)
            ->set('data.identifier', 'SID1X')
            ->set('data.initial_altitude', -1)
            ->call('save')
            ->assertHasErrors(['data.initial_altitude']);
    }

    public function testEditFailsValidationIfInitialAltitudeTooBig()
    {
        Livewire::test(SidResource\Pages\EditSid::class, ['record' => 1])
            ->set('data.runway_id', 2)
            ->set('data.identifier', 'SID1X')
            ->set('data.initial_altitude', 100000)
            ->call('save')
            ->assertHasErrors(['data.initial_altitude']);
    }

    public function testEditFailsValidationIfInitialHeadingTooSmall()
    {
        Livewire::test(SidResource\Pages\EditSid::class, ['record' => 1])
            ->set('data.runway_id', 2)
            ->set('data.identifier', 'SID1X')
            ->set('data.initial_altitude', 4500)
            ->set('data.initial_heading', -5)
            ->call('save')
            ->assertHasErrors(['data.initial_heading']);
    }

    public function testEditFailsValidationIfInitialHeadingNorthAsZero()
    {
        Livewire::test(SidResource\Pages\EditSid::class, ['record' => 1])
            ->set('data.runway_id', 2)
            ->set('data.identifier', 'SID1X')
            ->set('data.initial_altitude', 4500)
            ->set('data.initial_heading', 0)
            ->call('save')
            ->assertHasErrors(['data.initial_heading']);
    }

    public function testEditFailsValidationIfInitialHeadingTooBig()
    {
        Livewire::test(SidResource\Pages\EditSid::class, ['record' => 1])
            ->set('data.runway_id', 2)
            ->set('data.identifier', 'SID1X')
            ->set('data.initial_altitude', 4500)
            ->set('data.initial_heading', 361)
            ->call('save')
            ->assertHasErrors(['data.initial_heading']);
    }

    public function testEditFailsValidationIfSidAlreadyExistsForRunway()
    {
        Sid::where('id', 1)->update(['runway_id' => 2]);
        Livewire::test(SidResource\Pages\EditSid::class, ['record' => 1])
            ->set('data.runway_id', 2)
            ->set('data.identifier', 'TEST1Y')
            ->set('data.initial_altitude', 4500)
            ->set('data.initial_heading', 123)
            ->call('save')
            ->assertHasErrors(['data.identifier']);
    }

    protected function getViewEditRecord(): Model
    {
        return Sid::find(1);
    }

    protected function getResourceClass(): string
    {
        return SidResource::class;
    }

    protected function getEditText(): string
    {
        return 'Edit EGLL/27L - TEST1X';
    }

    protected function getCreateText(): string
    {
        return 'Create sid';
    }

    protected function getViewText(): string
    {
        return 'EGLL/27L - TEST1X';
    }

    protected function getIndexText(): array
    {
        return ['EGLL', 'EGBB'];
    }
}
