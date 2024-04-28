<?php

namespace App\Filament;

use App\BaseFilamentTestCase;
use App\Filament\AccessCheckingHelpers\ChecksListingFilamentAccess;
use App\Filament\AccessCheckingHelpers\ChecksViewFilamentAccess;
use App\Filament\Resources\PluginLogResource;
use App\Models\User\RoleKeys;
use App\Models\Plugin\PluginLog;
use Illuminate\Database\Eloquent\Model;

class PluginLogResourceTest extends BaseFilamentTestCase
{
    use ChecksListingFilamentAccess;
    use ChecksViewFilamentAccess;

    public static function indexRoleProvider(): array
    {
        return [
            'None' => [null, false],
            'Contributor' => [RoleKeys::OPERATIONS_CONTRIBUTOR, false],
            'DSG' => [RoleKeys::DIVISION_STAFF_GROUP, true],
            'Web' => [RoleKeys::WEB_TEAM, true],
            'Operations' => [RoleKeys::OPERATIONS_TEAM, false],
        ];
    }

    public static function viewRoleProvider(): array
    {
        return [
            'None' => [null, false],
            'Contributor' => [RoleKeys::OPERATIONS_CONTRIBUTOR, false],
            'DSG' => [RoleKeys::DIVISION_STAFF_GROUP, true],
            'Web' => [RoleKeys::WEB_TEAM, true],
            'Operations' => [RoleKeys::OPERATIONS_TEAM, false],
        ];
    }

    protected function getIndexText(): array
    {
        return ['Plugin Logs'];
    }

    protected function getViewText(): string
    {
        return 'View Plugin Log';
    }

    protected function getViewRecord(): Model
    {
        return PluginLog::query()->firstOrFail();
    }

    protected static function resourceClass(): string
    {
        return PluginLogResource::class;
    }
}
