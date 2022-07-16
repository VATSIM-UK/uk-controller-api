<?php

use App\Models\User\Role;
use App\Models\User\RoleKeys;
use App\Models\User\User;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    const USERS = [1203533, 1258635, 1169992, 1294298];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $webRole = Role::where('key', RoleKeys::WEB_TEAM)->firstOrFail()->id;
        foreach (self::USERS as $cid) {
            $user = User::find($cid);
            if (!$user) {
                continue;
            }

            $user->roles()->sync([$webRole]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
