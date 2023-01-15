<?php

use App\Models\IntentionCode\IntentionCode;
use Illuminate\Database\Seeder;

class IntentionCodeSeeder extends Seeder
{
    public function run()
    {
        IntentionCode::create(
            [
                'description' => 'A1 code',
                'code' => [
                    'type' => 'single_code',
                    'code' => 'A1',
                ],
                'conditions' => [
                    [
                        'type' => 'arrival_airfields',
                        'airfields' => ['EGLL'],
                    ],
                ],
                'priority' => 1,
            ]
        );
    }
}
