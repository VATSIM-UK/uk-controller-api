<?php

namespace App\Filament\AccessCheckingHelpers;

use App\Models\User\RoleKeys;

trait ChecksEditOperationsContributorAccess {
    use BaseChecksEditAccess;

    public static function editRoleProvider(): array
    {
        return [
            'None' => [null, false],
            'Contributor' => [RoleKeys::OPERATIONS_CONTRIBUTOR, true],
            'DSG' => [RoleKeys::DIVISION_STAFF_GROUP, true],
            'Web' => [RoleKeys::WEB_TEAM, true],
            'Operations' => [RoleKeys::OPERATIONS_TEAM, true],
        ];
    }
}
