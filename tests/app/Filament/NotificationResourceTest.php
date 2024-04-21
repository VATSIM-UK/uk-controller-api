<?php

namespace App\Filament;

use App\BaseFilamentTestCase;
use App\Filament\Resources\NotificationResource;
use App\Filament\Resources\NotificationResource\Pages\EditNotification;
use App\Filament\Resources\NotificationResource\Pages\ListNotifications;
use App\Filament\Resources\NotificationResource\Pages\ViewNotification;
use App\Filament\Resources\NotificationResource\RelationManagers\ControllersRelationManager;
use App\Models\Controller\ControllerPosition;
use App\Models\Notification\Notification;
use Carbon\Carbon;
use Filament\Tables\Actions\AttachAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Livewire\Livewire;

class NotificationResourceTest extends BaseFilamentTestCase
{
    use ChecksDefaultFilamentAccess;
    use ChecksDefaultFilamentActionVisibility;

    public function testItCanFilterForControllerRelevantNotifications()
    {
        $notification1 = Notification::factory()->create();
        $notification2 = Notification::factory()->create();
        $notification3 = Notification::factory()->create();
        $notification4 = Notification::factory()->create();

        $notification2->controllers()->sync([1, 2]);
        $notification3->controllers()->sync([1, 3]);
        $notification4->controllers()->sync([3, 4]);

        Livewire::test(ListNotifications::class)
            ->assertCanSeeTableRecords([$notification1, $notification2, $notification3, $notification4])
            ->filterTable('controllers', ['values' => 1])
            ->assertCanSeeTableRecords([$notification2, $notification3])
            ->assertCanNotSeeTableRecords([$notification1, $notification4]);
    }

    public function testItCanFilterForUnreadNotifications()
    {
        $notification1 = Notification::factory()->create();
        $notification2 = Notification::factory()->create();
        $notification3 = Notification::factory()->create();
        $notification4 = Notification::factory()->create();

        $notification1->readBy()->sync(self::ACTIVE_USER_CID);
        $notification4->readBy()->sync(self::ACTIVE_USER_CID);

        Livewire::test(ListNotifications::class)
            ->assertCanSeeTableRecords([$notification1, $notification2, $notification3, $notification4])
            ->filterTable('unread')
            ->assertCanSeeTableRecords([$notification2, $notification3])
            ->assertCanNotSeeTableRecords([$notification1, $notification4]);
    }

    public function testItCanFilterForActiveNotifications()
    {
        $notification1 = Notification::factory()->create();
        $notification2 = Notification::factory()->finished()->create();
        $notification3 = Notification::factory()->notStarted()->create();
        $notification4 = Notification::factory()->create();

        Livewire::test(ListNotifications::class)
            ->assertCanSeeTableRecords([$notification1, $notification2, $notification3, $notification4])
            ->filterTable('active')
            ->assertCanNotSeeTableRecords([$notification2, $notification3])
            ->assertCanSeeTableRecords([$notification1, $notification4]);
    }

    public function testItLoadsDataForView()
    {
        $notification = Notification::factory()->create();

        Livewire::test(ViewNotification::class, ['record' => $notification->id])
            ->assertSet('data.title', $notification->title)
            ->assertSet('data.body', $notification->body)
            ->assertSet('data.link', $notification->link)
            ->assertSet('data.valid_from', $notification->valid_from->format('Y-m-d H:i'))
            ->assertSet('data.valid_to', $notification->valid_to->format('Y-m-d H:i'));
    }

    public function testViewingTheNotificationMarksItAsReadByTheUser()
    {
        $notification = Notification::factory()->create();

        Livewire::test(ViewNotification::class, ['record' => $notification->id]);

        $this->assertDatabaseHas(
            'notification_user',
            [
                'user_id' => self::ACTIVE_USER_CID,
                'notification_id' => $notification->id,
            ]
        );
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
            ->assertSet('data.valid_from', $notification->valid_from->format('Y-m-d H:i'))
            ->assertSet('data.valid_to', $notification->valid_to->format('Y-m-d H:i'));
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

    public function testItDisplaysControllers()
    {
        $notification = Notification::factory()->create();
        $notification->controllers()->sync([1, 2]);

        Livewire::test(ControllersRelationManager::class, ['ownerRecord' => $notification, 'pageClass' => EditNotification::class])
            ->assertCountTableRecords(2)
            ->assertCanSeeTableRecords([ControllerPosition::find(1), ControllerPosition::find(2)])
            ->assertHasNoTableActionErrors();
    }

    public function testItAddsAllControllersIfGlobalSelected()
    {
        $notification = Notification::factory()->create();
        Livewire::test(ControllersRelationManager::class, ['ownerRecord' => $notification, 'pageClass' => EditNotification::class])
            ->callTableAction(
                AttachAction::class,
                null,
            data: [
                    'global' => true,
                ],
            )
            ->assertHasNoTableActionErrors();

        $this->assertDatabaseHas(
            'controller_position_notification',
            [
                'controller_position_id' => 1,
                'notification_id' => $notification->id,
            ]
        );

        $this->assertDatabaseHas(
            'controller_position_notification',
            [
                'controller_position_id' => 2,
                'notification_id' => $notification->id,
            ]
        );

        $this->assertDatabaseHas(
            'controller_position_notification',
            [
                'controller_position_id' => 3,
                'notification_id' => $notification->id,
            ]
        );

        $this->assertDatabaseHas(
            'controller_position_notification',
            [
                'controller_position_id' => 4,
                'notification_id' => $notification->id,
            ]
        );
    }

    public function testItUpdatesControllersIfGlobalSelected()
    {
        $notification = Notification::factory()->create();
        $notification->controllers()->sync([1, 2]);

        Livewire::test(ControllersRelationManager::class, ['ownerRecord' => $notification, 'pageClass' => EditNotification::class])
            ->callTableAction(
                AttachAction::class,
            data: [
                    'global' => true,
                ],
            )
            ->assertHasNoTableActionErrors();

        $this->assertDatabaseHas(
            'controller_position_notification',
            [
                'controller_position_id' => 1,
                'notification_id' => $notification->id,
            ]
        );

        $this->assertDatabaseHas(
            'controller_position_notification',
            [
                'controller_position_id' => 2,
                'notification_id' => $notification->id,
            ]
        );

        $this->assertDatabaseHas(
            'controller_position_notification',
            [
                'controller_position_id' => 3,
                'notification_id' => $notification->id,
            ]
        );

        $this->assertDatabaseHas(
            'controller_position_notification',
            [
                'controller_position_id' => 4,
                'notification_id' => $notification->id,
            ]
        );
    }

    public function testItAddsAllControllersByPositionLevel()
    {
        $notification = Notification::factory()->create();
        Livewire::test(ControllersRelationManager::class, ['ownerRecord' => $notification, 'pageClass' => EditNotification::class])
            ->callTableAction(
                AttachAction::class,
            data: [
                    'position_level' => ['TWR', 'APP'],
                ],
            )
            ->assertHasNoTableActionErrors();

        $this->assertDatabaseHas(
            'controller_position_notification',
            [
                'controller_position_id' => 1,
                'notification_id' => $notification->id,
            ]
        );

        $this->assertDatabaseHas(
            'controller_position_notification',
            [
                'controller_position_id' => 2,
                'notification_id' => $notification->id,
            ]
        );

        $this->assertDatabaseMissing(
            'controller_position_notification',
            [
                'controller_position_id' => 3,
                'notification_id' => $notification->id,
            ]
        );

        $this->assertDatabaseMissing(
            'controller_position_notification',
            [
                'controller_position_id' => 4,
                'notification_id' => $notification->id,
            ]
        );
    }

    public function testItUpdatesControllersIfPositionLevelSelected()
    {
        $notification = Notification::factory()->create();
        $notification->controllers()->sync([1, 2]);

        Livewire::test(ControllersRelationManager::class, ['ownerRecord' => $notification, 'pageClass' => EditNotification::class])
            ->callTableAction(
                AttachAction::class,
            data: [
                    'position_level' => ['CTR'],
                ],
            )
            ->assertHasNoTableActionErrors();

        $this->assertDatabaseHas(
            'controller_position_notification',
            [
                'controller_position_id' => 1,
                'notification_id' => $notification->id,
            ]
        );

        $this->assertDatabaseHas(
            'controller_position_notification',
            [
                'controller_position_id' => 2,
                'notification_id' => $notification->id,
            ]
        );

        $this->assertDatabaseHas(
            'controller_position_notification',
            [
                'controller_position_id' => 3,
                'notification_id' => $notification->id,
            ]
        );

        $this->assertDatabaseHas(
            'controller_position_notification',
            [
                'controller_position_id' => 4,
                'notification_id' => $notification->id,
            ]
        );
    }

    public function testItAddsControllers()
    {
        $notification = Notification::factory()->create();
        Livewire::test(ControllersRelationManager::class, ['ownerRecord' => $notification, 'pageClass' => EditNotification::class])
            ->callTableAction(
                AttachAction::class,
            data: [
                    'global' => false,
                    'controllers' => [
                        1,
                        3,
                    ],
                ],
            )
            ->assertHasNoTableActionErrors();

        $this->assertDatabaseHas(
            'controller_position_notification',
            [
                'controller_position_id' => 1,
                'notification_id' => $notification->id,
            ]
        );

        $this->assertDatabaseMissing(
            'controller_position_notification',
            [
                'controller_position_id' => 2,
                'notification_id' => $notification->id,
            ]
        );

        $this->assertDatabaseHas(
            'controller_position_notification',
            [
                'controller_position_id' => 3,
                'notification_id' => $notification->id,
            ]
        );

        $this->assertDatabaseMissing(
            'controller_position_notification',
            [
                'controller_position_id' => 4,
                'notification_id' => $notification->id,
            ]
        );
    }

    public function testItUpdatesControllers()
    {
        $notification = Notification::factory()->create();
        $notification->controllers()->sync([2]);

        Livewire::test(ControllersRelationManager::class, ['ownerRecord' => $notification, 'pageClass' => EditNotification::class])
            ->callTableAction(
                AttachAction::class,
            data: [
                    'global' => false,
                    'controllers' => [
                        1,
                        3,
                    ],
                ],
            )
            ->assertHasNoTableActionErrors();

        $this->assertDatabaseHas(
            'controller_position_notification',
            [
                'controller_position_id' => 1,
                'notification_id' => $notification->id,
            ]
        );

        $this->assertDatabaseHas(
            'controller_position_notification',
            [
                'controller_position_id' => 2,
                'notification_id' => $notification->id,
            ]
        );

        $this->assertDatabaseHas(
            'controller_position_notification',
            [
                'controller_position_id' => 3,
                'notification_id' => $notification->id,
            ]
        );

        $this->assertDatabaseMissing(
            'controller_position_notification',
            [
                'controller_position_id' => 4,
                'notification_id' => $notification->id,
            ]
        );
    }

    public function testItErrorsIfNoControllersSelected()
    {
        $notification = Notification::factory()->create();

        Livewire::test(ControllersRelationManager::class, ['ownerRecord' => $notification, 'pageClass' => EditNotification::class])
            ->callTableAction(
                AttachAction::class,
            data: [
                    'global' => false,
                    'controllers' => [],
                ],
            )
            ->assertHasTableActionErrors(['controllers']);
    }

    protected static function resourceClass(): string
    {
        return NotificationResource::class;
    }

    protected function getEditText(): string
    {
        return 'Edit Foo';
    }

    protected function getCreateText(): string
    {
        return 'Create Notification';
    }

    protected function getViewText(): string
    {
        return 'View Foo';
    }

    protected function getIndexText(): array
    {
        return ['Foo'];
    }

    protected static function resourceId(): int|string
    {
        return 1;
    }

    protected static function resourceRecordClass(): string
    {
        return Notification::class;
    }

    protected static function resourceListingClass(): string
    {
        return ListNotifications::class;
    }

    protected static function writeResourceTableActions(): array
    {
        return [
            'edit',
        ];
    }

    protected static function readOnlyResourceTableActions(): array
    {
        return [
            'view',
        ];
    }

    protected static function writeResourcePageActions(): array
    {
        return [
            'create',
        ];
    }

    protected static function tableActionRecordClass(): array
    {
        return [ControllersRelationManager::class => ControllerPosition::class];
    }

    protected static function tableActionRecordId(): array
    {
        return [ControllersRelationManager::class => 1];
    }

    protected static function writeTableActions(): array
    {
        return [
            ControllersRelationManager::class => [
                'attach',
                'detach',
            ],
        ];
    }

    protected function getEditRecord(): Model
    {
        return Notification::findOrFail(1);
    }

    protected function getViewRecord(): Model
    {
        return Notification::findOrFail(1);
    }
}
