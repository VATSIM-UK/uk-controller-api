<?php

namespace App\Log;

use App\BaseUnitTestCase;
use Psr\Log\LoggerInterface;

class LoggerFactoryTest extends BaseUnitTestCase
{
    /**
     * Factory under test
     *
     * @var LoggerFactory
     */
    private $factory;

    public function setUp()
    {
        parent::setUp();
        $this->factory = new LoggerFactory;
    }

    public function testItConstructs()
    {
        $this->assertInstanceOf(LoggerFactory::class, $this->factory);
    }

    public function testItReturnsAPsr7Logger()
    {
        $this->assertInstanceOf(LoggerInterface::class, $this->factory->__invoke(['name' => 'logger']));
    }

    public function testTheLoggerHasAName()
    {
        $this->assertEquals('testlogger', $this->factory->__invoke(['name' => 'testlogger'])->getName());
    }
}
