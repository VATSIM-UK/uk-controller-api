<?php

namespace App\Filament;

use App\BaseFilamentTestCase;
use App\Filament\Resources\ActivityResource;
use App\Filament\Resources\StandResource;
use App\Models\User\Role;
use App\Models\User\RoleKeys;
use App\Models\User\User;
use Spatie\Activitylog\Models\Activity;

class ActivityResourceTest extends BaseFilamentTestCase
{
    private readonly Activity $activity;

    public function setUp(): void
    {
        parent::setUp();
        $this->activity = activity('test')
            ->log('ohai');
    }

    /**
     * @dataProvider indexRoleProvider
     */
    public function testItCanBeIndexed(?RoleKeys $role, bool $shouldBeAllowed)
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        if ($role) {
            $user->roles()->sync([Role::idFromKey($role)]);
        }

        if ($shouldBeAllowed) {
            $this->get(ActivityResource::getUrl())
                ->assertSuccessful();
        } else {
            $this->get(ActivityResource::getUrl())
                ->assertForbidden();
        }
    }

    private function indexRoleProvider(): array
    {
        return [
            'None' => [null, false],
            'DSG' => [RoleKeys::DIVISION_STAFF_GROUP, true],
            'Web' => [RoleKeys::WEB_TEAM, true],
            'Operations' => [RoleKeys::OPERATIONS_TEAM, false],
        ];
    }

    /**
     * @dataProvider viewRoleProvider
     */
    public function testItCanBeViewed(?RoleKeys $role, bool $shouldBeAllowed)
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        if ($role) {
            $user->roles()->sync([Role::idFromKey($role)]);
        }

        if ($shouldBeAllowed) {
            $this->get(ActivityResource::getUrl('view', ['record' => $this->activity]))
                ->assertSuccessful();
        } else {
            $this->get(ActivityResource::getUrl('view', ['record' => $this->activity]))
                ->assertForbidden();
        }
    }

    private function viewRoleProvider(): array
    {
        return [
            'None' => [null, false],
            'DSG' => [RoleKeys::DIVISION_STAFF_GROUP, true],
            'Web' => [RoleKeys::WEB_TEAM, true],
            'Operations' => [RoleKeys::OPERATIONS_TEAM, false],
        ];
    }
}
