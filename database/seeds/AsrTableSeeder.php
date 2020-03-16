<?php

use App\Models\AltimeterSettingRegions\AltimeterSettingRegion;
use Illuminate\Database\Seeder;

class AsrTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create ASRs and attach airfields
        $bobbingtonAsr = AltimeterSettingRegion::create(
            [
                'name' => 'Bobbington',
                'key' => 'ASR_BOBBINGTON',
            ]
        );

        $bobbingtonAsr->airfields()->attach([1, 3]);

        $toppingtonAsr = AltimeterSettingRegion::create(
            [
                'name' => 'Toppington',
                'key' => 'ASR_TOPPINGTON',
            ]
        );

        $toppingtonAsr->airfields()->attach([2]);
    }
}
