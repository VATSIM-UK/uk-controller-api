<?php

namespace App\Rules\Runway;

use App\BaseUnitTestCase;
use PHPUnit\Metadata\Api\DataProvider;

class RunwayIdentifierTest extends BaseUnitTestCase
{
    private RunwayIdentifier $rule;

    public function setUp(): void
    {
        parent::setUp();
        $this->rule = $this->app->make(RunwayIdentifier::class);
    }

    #[DataProvider('goodDataProvider')]
    public function testItPassesValidation(string $identifier)
    {
        $this->assertTrue($this->rule->passes('', $identifier));
    }

    public static function goodDataProvider(): array
    {
        return [
            'Normal runway less than 10' => ['09'],
            'Normal runway more than 10' => ['27'],
            'Normal runway more than 30' => ['36'],
            'Normal runway less than 10 with L modifier' => ['09L'],
            'Normal runway less than 10 with R modifier' => ['09R'],
            'Normal runway less than 10 with G modifier' => ['09G'],
            'Normal runway more than 10 with L modifier' => ['27L'],
            'Normal runway more than 10 with R modifier' => ['27R'],
            'Normal runway more than 10 with G modifier' => ['27G'],
            'Normal runway more than 30 with L modifier' => ['36L'],
            'Normal runway more than 30 with R modifier' => ['36R'],
            'Normal runway more than 30 with G modifier' => ['36G'],
        ];
    }

    #[DataProvider('badDataProvider')]
    public function testItFailsValidation(?string $identifier)
    {
        $this->assertFalse($this->rule->passes('', $identifier));
    }

    public static function badDataProvider(): array
    {
        return [
            'Is null' => [null],
            'Runway 0' => ['00'],
            'Only one zero' => ['0'],
            '1 digit' => ['9'],
            '1 digit with L modifier' => ['9L'],
            '1 digit with R modifier' => ['9R'],
            '1 digit with G modifier' => ['9G'],
            '3 digits' => ['333'],
            '3 digits with L modifier' => ['333L'],
            '3 digits with R modifier' => ['333R'],
            '3 digits with G modifier' => ['333G'],
            'Greater than 36' => ['37'],
            'Greater than 36 with L modifier' => ['37L'],
            'Greater than 36 with R modifier' => ['37R'],
            'Greater than 36 with G modifier' => ['37G'],
            'Only modifier' => ['L'],
            'Only modifiers' => ['LLL'],
            'Two digits unknown modifier' => ['37X'],
            'Two digits less than 10 unknown modifier' => ['09Y'],
        ];
    }
}
