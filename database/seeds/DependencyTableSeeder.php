<?php

use App\Models\Dependency\Dependency;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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
                    'updated_at' => '2020-04-02 21:00:00',
                    'per_user' => false,
                ],
                [
                    'key' => 'DEPENDENCY_TWO',
                    'uri' => 'dependency/two',
                    'local_file' => 'two.json',
                    'updated_at' => '2020-04-03 21:00:00',
                    'per_user' => false,
                ],
                [
                    'key' => 'USER_DEPENDENCY_ONE',
                    'uri' => 'dependency/userone',
                    'local_file' => 'userone.json',
                    'updated_at' => '2020-04-02 21:00:00',
                    'per_user' => true,
                ],
                [
                    'key' => 'USER_DEPENDENCY_TWO',
                    'uri' => 'dependency/usertwo',
                    'local_file' => 'usertwo.json',
                    'updated_at' => '2020-04-03 21:00:00',
                    'per_user' => true,
                ],
                [
                    'key' => 'USER_DEPENDENCY_THREE',
                    'uri' => 'dependency/userthree',
                    'local_file' => 'userthree.json',
                    'updated_at' => '2020-04-01 21:00:00',
                    'per_user' => true,
                ],
                [
                    'key' => 'DEPENDENCY_THREE',
                    'uri' => 'dependency/three',
                    'local_file' => 'three.json',
                    'updated_at' => null,
                    'per_user' => false,
                ],
            ]
        );

        DB::table('dependency_user')->insert(
            [
                [
                    'dependency_id' => 3,
                    'user_id' => UserTableSeeder::ACTIVE_USER_CID,
                    'updated_at' => '2020-04-04 21:00:00',
                ],
                [
                    'dependency_id' => 4,
                    'user_id' => UserTableSeeder::ACTIVE_USER_CID,
                    'updated_at' => '2020-04-05 21:00:00',
                ]
            ]
        );
    }
}
