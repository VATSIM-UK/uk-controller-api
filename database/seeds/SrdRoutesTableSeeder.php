<?php

use App\Models\Srd\SrdRoute;
use App\Models\Version\Version;
use Illuminate\Database\Seeder;

class SrdRoutesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SrdRoute::create([
            'origin' => 'EGGD',
            'destination' => 'EGLL',
            'min_level' => null,
            'max_level' => 28000,
            'route_segment' => 'L9 KENET',
            'sid' => 'WOTAN',
            'star' => 'OCK1A',
        ]);
        SrdRoute::create([
            'origin' => 'EGGD',
            'destination' => 'EGLL',
            'min_level' => 10000,
            'max_level' => 19500,
            'route_segment' => 'L9 KENET',
            'sid' => 'WOTAN',
            'star' => 'OCK1A',
        ]);
        SrdRoute::create([
            'origin' => 'EGGD',
            'destination' => 'EGLL',
            'min_level' => 24500,
            'max_level' => 66000,
            'route_segment' => 'UL9 KENET',
            'sid' => 'WOTAN',
            'star' => 'OCK1A',
        ]);

        SrdRoute::create([
            'origin' => 'EGAA',
            'destination' => 'EGLL',
            'min_level' => 24500,
            'max_level' => 66000,
            'route_segment' => 'LISBO DCT RINGA Q39 NOMSU UQ4 WAL UY53 NUGRA',
            'sid' => null,
            'star' => 'BNN1B',
        ]);
    }
}
