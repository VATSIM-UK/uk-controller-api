<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserVersionLastLogin extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user', function (Blueprint $table) {
            $table->timestamp('last_login')
                ->after('status')
                ->nullable(true)
                ->default(null);
            $table->ipAddress('last_login_ip')
                ->after('last_login')
                ->nullable(true)
                ->default(null);
            $table->unsignedInteger('last_version')
                ->after('last_login_ip')
                ->nullable(true)
                ->default(null);

            $table->foreign('last_version')
                ->references('id')->on('version');
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
            $table->dropForeign('user_last_version_foreign');
            $table->dropColumn('last_version');
            $table->dropColumn('last_login_ip');
            $table->dropColumn('last_login');
        });
    }
}
