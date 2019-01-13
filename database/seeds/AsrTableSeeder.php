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
        AltimeterSettingRegion::create(
            [
                'name' => 'Skerry',
                'station' => '["EGPC","EGPA"]',
                'variation' => 1,
            ]
        );
        AltimeterSettingRegion::create(
            [
                'name' => 'Portree',
                'station' => '["EGEO","EGPU","EGPR","EGPL","EGPO"]',
                'variation' => 0,
            ]
        );
        AltimeterSettingRegion::create(
            [
                'name' => 'Rattray',
                'station' => '["EGPD"]',
                'variation' => 2,
            ]
        );
        AltimeterSettingRegion::create(
            [
                'name' => 'Tyne',
                'station' => '["EGNT","EGNV","EGQM","EGOM","EGNC"]',
                'variation' => 0,
            ]
        );
        AltimeterSettingRegion::create(
            [
                'name' => 'Belfast',
                'station' => '["EGAA","EGAC","EGAE"]',
                'variation' => 0,
            ]
        );
        AltimeterSettingRegion::create(
            [
                'name' => 'Holyhead',
                'station' => '["EGOV","EGCK","EGNS","EGOY","EGCW"]',
                'variation' => 0,
            ]
        );
        AltimeterSettingRegion::create(
            [
                'name' => 'Barnsley',
                'station' =>
                    '["EGBE","EGBB","EGNX","EGNM","EGCN","EGWC","EGBO","EGOS",' .
                    '"EGXE","EGXC","EGXG","EGNE","EGYD","EGNJ"]',
                'variation' => 0,
            ]
        );
        AltimeterSettingRegion::create(
            [
                'name' => 'Humber',
                'station' => '["EGNJ","EGXV","EGXC"]',
                'variation' => 0,
            ]
        );
        AltimeterSettingRegion::create(
            [
                'name' => 'Scillies',
                'station' => '["EGDR","EGHE","EGHT"]',
                'variation' => 0,
            ]
        );
        AltimeterSettingRegion::create(
            [
                'name' => 'Wessex',
                'station' => '["EGHQ","EGTE","EGFE","EGFH","EGHC"]',
                'variation' => 0,
            ]
        );
        AltimeterSettingRegion::create(
            [
                'name' => 'Chatham',
                'station' => '["EGKA","EGHR","EGMD","EGVO","EGMC","EGTO","EGKB","EGLL","EGWU","EGSC","EGSS","EGGW"]',
                'variation' => 0,
            ]
        );
        AltimeterSettingRegion::create(
            [
                'name' => 'Portland',
                'station' => '["EGHI","EGHH","EGTE","EGDY","EGDX"]',
                'variation' => 0,
            ]
        );
        AltimeterSettingRegion::create(
            [
                'name' => 'Yarmouth',
                'station' => '["EGSH","EGYM","EGXH","EGUL","EGUN","EGYC"]',
                'variation' => 0,
            ]
        );
        AltimeterSettingRegion::create(
            [
                'name' => 'Cotswold',
                'station' => '["EGGD","EGFF","EGBP","EGBJ","EGVN","EGTK","EGTC"]',
                'variation' => 0,
            ]
        );
        AltimeterSettingRegion::create(
            [
                'name' => 'Shetland',
                'station' => '["EGPB","EGPM"]',
                'variation' => 0,
            ]
        );
        AltimeterSettingRegion::create(
            [
                'name' => 'Orkney',
                'station' => '["EGPD","EGPS","EGOS","EGQK","EGPC","EGPE"]',
                'variation' => 0,
            ]
        );
        AltimeterSettingRegion::create(
            [
                'name' => 'Marlin',
                'station' => '["EGPB","EGPM"]',
                'variation' => 2,
            ]
        );
        AltimeterSettingRegion::create(
            [
                'name' => 'Petrel',
                'station' => '["EGPD","EGQL"]',
                'variation' => 2,
            ]
        );
        AltimeterSettingRegion::create(
            [
                'name' => 'Skua',
                'station' => '["EGNT","EGNV","EGPD"]',
                'variation' => 3,
            ]
        );
        AltimeterSettingRegion::create(
            [
                'name' => 'Puffin',
                'station' => '["EGPB","EGPM"]',
                'variation' => 2,
            ]
        );
    }
}
