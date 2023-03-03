<?php

use App\Models\User\Role;
use App\Models\User\RoleKeys;
use Illuminate\Database\Migrations\Migration;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Role::create(
            [
                'key' => RoleKeys::OPERATIONS_CONTRIBUTOR,
                'description' => 'Operations Contributor',
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
