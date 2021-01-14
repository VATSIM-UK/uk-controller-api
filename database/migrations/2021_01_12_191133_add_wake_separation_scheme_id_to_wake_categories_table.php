<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddWakeSeparationSchemeIdToWakeCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add the column
        Schema::table('wake_categories', function (Blueprint $table) {
            $table->unsignedBigInteger('scheme_id')
                ->after('id')
                ->comment('The scheme that this wake category applies to');
        });

        // Update data
        $ukScheme = DB::table('wake_category_schemes')
            ->where('key', 'UK')
            ->orderByDesc('id')
            ->first()
            ->id;

        DB::table('wake_categories')
            ->update(['scheme_id' => $ukScheme]);

        // Add indexes
        Schema::table('wake_categories', function (Blueprint $table) {
            $table->unique(['scheme_id', 'code'], 'wake_category_code');
            $table->unique(['scheme_id', 'relative_weighting'], 'wake_category_weighting');
            $table->foreign('scheme_id', 'wake_categories_scheme_id')
                ->references('id')
                ->on('wake_category_schemes')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // We don't want to go back...
    }
}
