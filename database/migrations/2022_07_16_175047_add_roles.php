<?php

use App\Models\User\Role;
use App\Models\User\RoleKeys;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up()
    {
        Role::create(
            [
                'key' => RoleKeys::DIVISION_STAFF_GROUP,
                'description' => 'Division Staff Group',
            ]
        );
        Role::create(
            [
                'key' => RoleKeys::WEB_TEAM,
                'description' => 'Web Services',
            ]
        );
        Role::create(
            [
                'key' => RoleKeys::OPERATIONS_TEAM,
                'description' => 'Operations',
            ]
        );
    }

    public function down()
    {
        Role::all()->each(function (Role $role) {
            $role->delete();
        });
    }
};
