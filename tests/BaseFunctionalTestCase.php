<?php
namespace App;

use Laravel\Lumen\Testing\DatabaseTransactions;

abstract class BaseFunctionalTestCase extends BaseTestCase
{
    use DatabaseTransactions;
}
