<?php

use App\Services\SectorfileService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddDecimalLatLongToStands extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->addNewColumns();
        $this->migrateData();
        $this->dropOldColumns();
        $this->renameNewColumns();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // There is no return
    }

    private function addNewColumns(): void
    {
        Schema::table('stands', function (Blueprint $table) {
            $table->double('lat_new', 10, 8)
                ->after('identifier')
                ->comment('The latitude of the stand in decimal degrees');
            $table->double('lon_new', 11, 8)
                ->after('lat_new')
                ->comment('The latitude of the stand in decimal degrees');
        });
    }

    private function migrateData(): void
    {
        foreach (DB::table('stands')->select()->get() as $stand)
        {
            $latLong = SectorfileService::coordinateFromSectorfile($stand->latitude, $stand->longitude);
            DB::table('stands')
                ->where('id', $stand->id)
                ->update(['lat_new' => $latLong->getLat(), 'lon_new' => $latLong->getLng()]);
        }
    }

    private function dropOldColumns(): void
    {
        Schema::table('stands', function (Blueprint $table) {
            $table->dropColumn('latitude');
            $table->dropColumn('longitude');
        });
    }

    private function renameNewColumns(): void
    {
        DB::statement('ALTER TABLE stands RENAME COLUMN lat_new TO latitude');
        DB::statement('ALTER TABLE stands RENAME COLUMN lon_new TO longitude');
    }
}
