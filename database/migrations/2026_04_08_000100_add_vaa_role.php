<?php

use App\Models\User\Role;
use App\Models\User\RoleKeys;
use Illuminate\Database\Migrations\Migration;

return new class() extends Migration {
    public function up(): void
    {
        Role::create([
            'key' => RoleKeys::VAA->value,
            'description' => 'Virtual Airline Administration',
        ]);
    }

    public function down(): void
    {
        Role::where('key', RoleKeys::VAA->value)->delete();
    }
};
