<?php

use App\Models\Dependency\Dependency;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddPerUserDependencies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Dependency::where('key', 'DEPENDENCY_HOLD_PROFILE')
            ->update(['per_user' => 1]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Dependency::where('key', 'DEPENDENCY_HOLD_PROFILE')
            ->update(['per_user' => 0]);
    }
}
