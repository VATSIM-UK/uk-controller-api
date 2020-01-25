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
                    'uri' => 'dependency/one',
                    'local_file' => 'one.json',
                ],
                [
                    'key' => 'DEPENDENCY_TWO',
                    'uri' => 'dependency/two',
                    'local_file' => 'two.json',
                ]
            ]
        );
    }
}
