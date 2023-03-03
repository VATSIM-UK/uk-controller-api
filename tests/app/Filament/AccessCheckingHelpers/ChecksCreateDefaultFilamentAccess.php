<?php

namespace App\Filament\AccessCheckingHelpers;

use App\Models\User\RoleKeys;

trait ChecksCreateDefaultFilamentAccess
{
    use BaseChecksCreateAccess;
    use HasResourceClass;

    public static function createRoleProvider(): array
    {
        return [
            'None' => [null, false],
            'Contributor' => [RoleKeys::OPERATIONS_CONTRIBUTOR, false],
            'DSG' => [RoleKeys::DIVISION_STAFF_GROUP, true],
            'Web' => [RoleKeys::WEB_TEAM, true],
            'Operations' => [RoleKeys::OPERATIONS_TEAM, true],
        ];
    }
}
