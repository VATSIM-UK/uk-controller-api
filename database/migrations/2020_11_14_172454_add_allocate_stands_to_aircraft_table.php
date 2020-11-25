<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddAllocateStandsToAircraftTable extends Migration
{
    const LIGHT_STAND_ALLOCATIONS = [
        'DH8A',
        'DH8B',
        'DH8C',
        'DH8D',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('aircraft', function (Blueprint $table) {
            $table->boolean('allocate_stands')
                ->after('wake_category_id')
                ->comment('Should this aircraft type be allocated stands');
        });

        DB::table('aircraft')
            ->where('wake_category_id', '<>', DB::table('wake_categories')->where('code', 'L')->first()->id)
            ->orWhereIn('code', self::LIGHT_STAND_ALLOCATIONS)
            ->update(['allocate_stands' => true]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('aircraft', function (Blueprint $table) {
            $table->dropColumn('allocate_stands');
        });
    }
}
