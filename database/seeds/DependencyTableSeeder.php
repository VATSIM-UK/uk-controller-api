<?php

use App\Models\Dependency\Dependency;
use Illuminate\Database\Seeder;

class DependencyTableSeeder extends Seeder
{
    public function run()
    {
        Dependency::insert(
            [
                [
                    'key' => 'DEPENDENCY_ONE',
                    'local_file' => 'one.json',
                ],
                [
                    'key' => 'DEPENDENCY_TWO',
                    'local_file' => 'two.json',
                ]
            ]
        );
    }
}
