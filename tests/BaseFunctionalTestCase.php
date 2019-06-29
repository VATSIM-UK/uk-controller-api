<?php
namespace App;

use Illuminate\Foundation\Testing\DatabaseTransactions;

abstract class BaseFunctionalTestCase extends BaseTestCase
{
    use DatabaseTransactions;
}
