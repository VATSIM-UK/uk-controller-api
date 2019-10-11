<?php

use App\Models\AltimeterSettingRegions\AltimeterSettingRegion;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAsrKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('altimeter_setting_region', function(Blueprint $table) {
            $table->string('key')->after('id')->comment('String key to identify the ASR');
            $table->dropColumn('variation');

            $table->dropUnique('altimeter_setting_region_name_unique');
        });

        $this->updateAsrKeys();

        Schema::table('altimeter_setting_region', function(Blueprint $table) {
            $table->unique('key');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('altimeter_setting_region', function(Blueprint $table) {
            $table->smallInteger('variation')->after('station');
            $table->dropUnique('altimeter_setting_region_key_unique');
            $table->dropColumn('key');

            $table->unique('name');
        });
    }

    private function updateAsrKeys() : void
    {
        AltimeterSettingRegion::all()->each(function (AltimeterSettingRegion $asr) {
            $asr->key = 'ASR_' . strtoupper($asr->name);
            $asr->save();
        });
    }
}
