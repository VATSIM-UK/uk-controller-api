<?php

use App\Models\Dependency\Dependency;

class DependencyTableSeeder extends \Illuminate\Database\Seeder
{
    public function run()
    {
        Dependency::insert(
            [
                [
                    'key' => 'DEPENDENCY_ONE',
                    'uri' => '/one',
                    'local_file' => 'one.json',
                ],
                [
                    'key' => 'DEPENDENCY_TWO',
                    'uri' => '/two',
                    'local_file' => 'two.json',
                ]
            ]
        );
    }
}
