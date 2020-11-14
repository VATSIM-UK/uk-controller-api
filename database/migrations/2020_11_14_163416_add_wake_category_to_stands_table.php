<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddWakeCategoryToStandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stands', function (Blueprint $table) {
            $table->unsignedTinyInteger('wake_category_id')
                ->after('longitude')
                ->default(DB::table('wake_categories')->where('code', 'LM')->first()->id)
                ->comment('The maximum wake category that can occupy this stand');

            $table->foreign('wake_category_id')->references('id')->on('wake_categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stands', function (Blueprint $table) {
            $table->dropForeign('stands_wake_category_id_foreign');
            $table->dropColumn('wake_category_id');
        });
    }
}
