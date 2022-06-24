<?php

namespace App\Console\Commands;

use App\BaseFunctionalTestCase;
use Illuminate\Support\Facades\Artisan;

class DataAdminCreateTest extends BaseFunctionalTestCase
{
    const ARTISAN_COMMAND = 'user:create-data-admin';

    public function testItReturnsSuccess()
    {
        $this->assertEquals(0, Artisan::call(self::ARTISAN_COMMAND));
    }

    public function testItCreatesAToken()
    {
        Artisan::call(self::ARTISAN_COMMAND);
        $token = explode(PHP_EOL, Artisan::output())[1];
        $this->get('/api/dataadmin', ['Authorization' => 'Bearer ' . $token])->assertStatus(200);
    }
}
