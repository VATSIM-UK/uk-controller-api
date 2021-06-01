<?php

use App\Models\Version\Version;
use Illuminate\Database\Seeder;

class VersionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Version::create(
            [
                'id' => 1,
                'version' => '1.0.0',
                'created_at' => '2017-12-02',
                'updated_at' => '2017-12-03',
                'deleted_at' => '2017-12-04',
            ]
        );

        Version::create(
            [
                'id' => 2,
                'version' => '2.0.0',
                'created_at' => '2017-12-03',
                'updated_at' => null,
                'deleted_at' => null,
            ]
        );

        Version::create(
            [
                'id' => 3,
                'version' => '2.0.1',
                'created_at' => '2017-12-04',
                'updated_at' => null,
                'deleted_at' => null,
            ]
        );
    }
}
