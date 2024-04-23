<?php

namespace App\Rules\User;

use App\BaseUnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class VatsimCidTest extends BaseUnitTestCase {
    private readonly VatsimCid $rule;

    public function setUp(): void
    {
        parent::setUp();
        $this->rule = $this->app->make(VatsimCid::class);
    }

    public function testItPassesOnValidCid()
    {
        $funcCalled = false;
        $failFunc = function () use (&$funcCalled) {
            $funcCalled = true;
        };

        $this->rule->validate('', 1203533, $failFunc);
        $this->assertFalse($funcCalled);
    }

    #[DataProvider('badDataProvider')]
    public function testItFailsInvalidCid(mixed $value)
    {
        $funcCalled = false;
        $failFunc = function () use (&$funcCalled) {
            $funcCalled = true;
        };

        $this->rule->validate('', $value, $failFunc);
        $this->assertTrue($funcCalled);
    }

    public static function badDataProvider(): array
    {
        return [
            'negative integer' => [-1],
            'below founder cid' => [799999],
            'string' => ['1203533'],
            'null' => [null],
            'array' => [[]],
        ];
    }
}
