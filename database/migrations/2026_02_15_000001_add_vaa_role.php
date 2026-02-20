<?php

use App\Models\User\Role;
use App\Models\User\RoleKeys;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        if (!Role::where('key', RoleKeys::VAA)->exists()) {
            Role::create([
                'key' => RoleKeys::VAA,
                'description' => 'Virtual Airline Allocator',
            ]);
        }
    }

    public function down(): void
    {
        Role::where('key', RoleKeys::VAA)->delete();
    }
};
