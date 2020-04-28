<?php

use App\Models\Srd\SrdNote;
use App\Models\Srd\SrdRoute;
use Illuminate\Database\Seeder;

class SrdTableSeeder extends Seeder
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
            'minimum_level' => null,
            'maximum_level' => 28000,
            'route_segment' => 'L9 KENET',
            'sid' => 'WOTAN',
            'star' => 'OCK1A',
        ]);
        SrdRoute::create([
            'origin' => 'EGGD',
            'destination' => 'EGLL',
            'minimum_level' => 10000,
            'maximum_level' => 19500,
            'route_segment' => 'L9 KENET',
            'sid' => 'WOTAN',
            'star' => 'OCK1A',
        ]);
        SrdRoute::create([
            'origin' => 'EGGD',
            'destination' => 'EGLL',
            'minimum_level' => 24500,
            'maximum_level' => 66000,
            'route_segment' => 'UL9 KENET',
            'sid' => 'WOTAN',
            'star' => 'OCK1A',
        ]);

        $noteRoute = SrdRoute::create([
            'origin' => 'EGAA',
            'destination' => 'EGLL',
            'minimum_level' => 24500,
            'maximum_level' => 66000,
            'route_segment' => 'LISBO DCT RINGA Q39 NOMSU UQ4 WAL UY53 NUGRA',
            'sid' => null,
            'star' => 'BNN1B',
        ]);

        SrdNote::create([
            'id' => 1,
            'note_text' => 'Text 1',
        ]);

        SrdNote::create([
            'id' => 2,
            'note_text' => 'Text 2',
        ]);

        SrdNote::create([
            'id' => 3,
            'note_text' => 'Text 3',
        ]);

        $noteRoute->notes()->attach([1, 2, 3]);
    }
}
