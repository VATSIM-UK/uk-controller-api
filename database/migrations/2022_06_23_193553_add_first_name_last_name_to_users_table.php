<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFirstNameLastNameToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user', function (Blueprint $table) {
            $table->string('first_name')
                ->after('status')
                ->default('VATSIM')
                ->comment('Users first name');
            $table->string('last_name')
                ->default('User')
                ->after('first_name')
                ->comment('Users last name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user', function (Blueprint $table) {
            $table->dropColumn('first_name');
            $table->dropColumn('last_name');
        });
    }
}
