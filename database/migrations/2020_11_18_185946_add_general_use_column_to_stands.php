<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGeneralUseColumnToStands extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stands', function (Blueprint $table) {
            $table->boolean('general_use')
                ->after('wake_category_id')
                ->default(false)
                ->comment(
                    'Whether the stand should be available for general use, ' .
                    'for example Cargo stands would not be, nor would Heathrow T5 as this is purely for BA'
                );
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
            $table->dropColumn('general_use');
        });
    }
}
