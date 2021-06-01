<?php

use App\Services\SectorfileService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ConvertNavaidCoordinateFormat extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add the new columns
        Schema::table('navaids', function (Blueprint $table) {
            $table->decimal('latitude_new', 10, 7)->after('latitude');
            $table->decimal('longitude_new', 10, 7)->after('longitude');
        });

        // Copy data into new columns
        DB::table('navaids')->get()->each(function ($navaid) {
            $coordinate = SectorfileService::coordinateFromSectorfile($navaid->latitude, $navaid->longitude);

            DB::table('navaids')->where('id', $navaid->id)
                ->update(
                    [
                        'latitude_new' => $coordinate->getLat(),
                        'longitude_new' => $coordinate->getLng()
                    ]
                );
        });

        // Drop old columns
        Schema::table('navaids', function (Blueprint $table) {
            $table->dropColumn('latitude');
            $table->dropColumn('longitude');
        });

        // Rename new columns
        DB::statement('ALTER TABLE navaids CHANGE latitude_new latitude DECIMAL(10, 7)');
        DB::statement('ALTER TABLE navaids CHANGE longitude_new longitude DECIMAL(10, 7)');
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
}
