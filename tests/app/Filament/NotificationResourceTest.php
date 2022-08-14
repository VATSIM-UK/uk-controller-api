<?php

namespace App\Filament;

use App\BaseFilamentTestCase;
use App\Filament\Resources\NotificationResource;
use App\Filament\Resources\NotificationResource\Pages\EditNotification;
use App\Filament\Resources\NotificationResource\Pages\ViewNotification;
use App\Models\Notification\Notification;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Livewire\Livewire;

class NotificationResourceTest extends BaseFilamentTestCase
{
    use ChecksDefaultFilamentAccess;

    public function testItLoadsDataForView()
    {
        $notification = Notification::factory()->create();

        Livewire::test(ViewNotification::class, ['record' => $notification->id])
            ->assertSet('data.title', $notification->title)
            ->assertSet('data.body', $notification->body)
            ->assertSet('data.link', $notification->link)
            ->assertSet('data.valid_from', $notification->valid_from)
            ->assertSet('data.valid_to', $notification->valid_to);
    }

    public function testItCreatesANotification()
    {
        $validFrom = Carbon::now()->addHours(4);
        $validTo = Carbon::now()->addHours(6);

        Livewire::test(NotificationResource\Pages\CreateNotification::class)
            ->set('data.title', 'Title')
            ->set('data.body', 'Body')
            ->set('data.link', 'https://vatsim.uk')
            ->set('data.valid_from', $validFrom)
            ->set('data.valid_to', $validTo)
            ->call('create')
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'notifications',
            [
                'title' => 'Title',
                'body' => 'Body',
                'link' => 'https://vatsim.uk',
                'valid_from' => $validFrom->startOfMinute()->toDateTimeString(),
                'valid_to' => $validTo->startOfMinute()->toDateTimeString(),
            ]
        );
    }

    public function testItCreatesAWithoutALink()
    {
        $validFrom = Carbon::now()->addHours(4);
        $validTo = Carbon::now()->addHours(6);

        Livewire::test(NotificationResource\Pages\CreateNotification::class)
            ->set('data.title', 'Title')
            ->set('data.body', 'Body')
            ->set('data.valid_from', $validFrom)
            ->set('data.valid_to', $validTo)
            ->call('create')
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'notifications',
            [
                'title' => 'Title',
                'body' => 'Body',
                'link' => null,
                'valid_from' => $validFrom->startOfMinute()->toDateTimeString(),
                'valid_to' => $validTo->startOfMinute()->toDateTimeString(),
            ]
        );
    }

    public function testNotificationCreationFailsTitleTooLong()
    {
        Livewire::test(NotificationResource\Pages\CreateNotification::class)
            ->set('data.title', Str::padLeft('', 256, 'a'))
            ->set('data.body', 'Body')
            ->set('data.valid_from', Carbon::now()->addHours(4))
            ->set('data.valid_to', Carbon::now()->addHours(6))
            ->call('create')
            ->assertHasErrors(['data.title']);
    }

    public function testNotificationCreationFailsBodyTooLong()
    {
        Livewire::test(NotificationResource\Pages\CreateNotification::class)
            ->set('data.title', 'Title')
            ->set('data.body', Str::padLeft('', 65536, 'a'))
            ->set('data.valid_from', Carbon::now()->addHours(4))
            ->set('data.valid_to', Carbon::now()->addHours(6))
            ->call('create')
            ->assertHasErrors(['data.body']);
    }

    public function testNotificationCreationFailsValidToBeforeValidFrom()
    {
        Livewire::test(NotificationResource\Pages\CreateNotification::class)
            ->set('data.title', 'Title')
            ->set('data.body', 'Body')
            ->set('data.valid_from', Carbon::now()->addHours(4))
            ->set('data.valid_to', Carbon::now()->addHours(4)->subMinute())
            ->call('create')
            ->assertHasErrors(['data.valid_to']);
    }

    public function testNotificationCreationFailsLinkNotUrl()
    {
        Livewire::test(NotificationResource\Pages\CreateNotification::class)
            ->set('data.title', 'Title')
            ->set('data.body', 'Body')
            ->set('data.link', 'abc')
            ->set('data.valid_from', Carbon::now()->addHours(4))
            ->set('data.valid_to', Carbon::now()->addHours(6))
            ->call('create')
            ->assertHasErrors(['data.link']);
    }

    public function testItLoadsDataForEdit()
    {
        $notification = Notification::factory()->create();

        Livewire::test(EditNotification::class, ['record' => $notification->id])
            ->assertSet('data.title', $notification->title)
            ->assertSet('data.body', $notification->body)
            ->assertSet('data.link', $notification->link)
            ->assertSet('data.valid_from', $notification->valid_from)
            ->assertSet('data.valid_to', $notification->valid_to);
    }

    public function testItEditsANotification()
    {
        $validFrom = Carbon::now()->addHours(2);
        $validTo = Carbon::now()->addHours(3);

        $notification = Notification::factory()->create();

        Livewire::test(EditNotification::class, ['record' => $notification->id])
            ->set('data.title', 'Title')
            ->set('data.body', 'Body')
            ->set('data.link', 'https://vatsim.uk')
            ->set('data.valid_from', $validFrom)
            ->set('data.valid_to', $validTo)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'notifications',
            [
                'id' => $notification->id,
                'title' => 'Title',
                'body' => 'Body',
                'link' => 'https://vatsim.uk',
                'valid_from' => $validFrom->startOfMinute()->toDateTimeString(),
                'valid_to' => $validTo->startOfMinute()->toDateTimeString(),
            ]
        );
    }

    public function testItEditsANotificationWithoutALink()
    {
        $validFrom = Carbon::now()->addHours(4);
        $validTo = Carbon::now()->addHours(6);

        $notification = Notification::factory()->create();

        Livewire::test(EditNotification::class, ['record' => $notification->id])
            ->set('data.title', 'Title')
            ->set('data.body', 'Body')
            ->set('data.link', null)
            ->set('data.valid_from', $validFrom)
            ->set('data.valid_to', $validTo)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas(
            'notifications',
            [
                'id' => $notification->id,
                'title' => 'Title',
                'body' => 'Body',
                'link' => null,
                'valid_from' => $validFrom->startOfMinute()->toDateTimeString(),
                'valid_to' => $validTo->startOfMinute()->toDateTimeString(),
            ]
        );
    }

    public function testNotificationEditFailsTitleTooLong()
    {
        $notification = Notification::factory()->create();

        Livewire::test(EditNotification::class, ['record' => $notification->id])
            ->set('data.title', Str::padLeft('', 256, 'a'))
            ->set('data.body', 'Body')
            ->set('data.valid_from', Carbon::now()->addHours(4))
            ->set('data.valid_to', Carbon::now()->addHours(6))
            ->call('save')
            ->assertHasErrors(['data.title']);
    }

    public function testNotificationEditFailsBodyTooLong()
    {
        $notification = Notification::factory()->create();

        Livewire::test(EditNotification::class, ['record' => $notification->id])
            ->set('data.title', 'Title')
            ->set('data.body', Str::padLeft('', 65536, 'a'))
            ->set('data.valid_from', Carbon::now()->addHours(4))
            ->set('data.valid_to', Carbon::now()->addHours(6))
            ->call('save')
            ->assertHasErrors(['data.body']);
    }

    public function testNotificationEditFailsValidToBeforeValidFrom()
    {
        $notification = Notification::factory()->create();

        Livewire::test(EditNotification::class, ['record' => $notification->id])
            ->set('data.title', 'Title')
            ->set('data.body', 'Body')
            ->set('data.valid_from', Carbon::now()->addHours(4))
            ->set('data.valid_to', Carbon::now()->addHours(4)->subMinute())
            ->call('save')
            ->assertHasErrors(['data.valid_to']);
    }

    public function testNotificationEditsFailsLinkNotUrl()
    {
        $notification = Notification::factory()->create();

        Livewire::test(EditNotification::class, ['record' => $notification->id])
            ->set('data.title', 'Title')
            ->set('data.body', 'Body')
            ->set('data.link', 'abc')
            ->set('data.valid_from', Carbon::now()->addHours(4))
            ->set('data.valid_to', Carbon::now()->addHours(6))
            ->call('save')
            ->assertHasErrors(['data.link']);
    }

    protected function getViewEditRecord(): Model
    {
        return Notification::findOrFail(1);
    }

    protected function getResourceClass(): string
    {
        return NotificationResource::class;
    }

    protected function getEditText(): string
    {
        return 'Edit Foo';
    }

    protected function getCreateText(): string
    {
        return 'Create notification';
    }

    protected function getViewText(): string
    {
        return 'View Foo';
    }

    protected function getIndexText(): array
    {
        return ['Foo'];
    }
}
