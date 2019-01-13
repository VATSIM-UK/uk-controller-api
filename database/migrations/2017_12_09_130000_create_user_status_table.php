<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'user_status',
            function (Blueprint $table) {
                $table->unsignedTinyInteger('id')->primary();
                $table->string('status');
            }
        );

        DB::table('user_status')->insert(
            [
                [
                    'id' => 1,
                    'status' => 'Active'
                ],
                [
                    'id' => 2,
                    'status' => 'Banned'
                ],
                [
                    'id' => 3,
                    'status' => 'Disabled'
                ],
            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_status');
    }
}
