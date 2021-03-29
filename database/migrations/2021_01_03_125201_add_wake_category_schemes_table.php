<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddWakeCategorySchemesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wake_category_schemes', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique()->comment('Key for the scheme');
            $table->string('name')->comment('The name of the scheme');
        });

        DB::table('wake_category_schemes')
            ->insert(
                [
                    [
                        'name' => 'UK',
                        'key' => 'UK',
                    ],
                    [
                        'name' => 'RECAT-EU',
                        'key' => 'RECAT_EU',
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
        Schema::dropIfExists('wake_category_schemes');
    }
}
