<?php

namespace App\Filament;

use App\BaseUnitTestCase;
use App\Filament\Resources\ActivityResource;
use App\Filament\Resources\TranslatesStrings;
use Filament\Facades\Filament;

class UsesStringTranslationHelpersTest extends BaseUnitTestCase
{
    private const EXEMPTIONS = [
        ActivityResource::class,
    ];

    public function testItUsesStringTranslationHelpers()
    {
        $testCases = [];
        foreach (Filament::getResources() as $resource) {
            if (in_array($resource, self::EXEMPTIONS)) {
                continue;
            }

            $testCases[$resource] = $resource;

            foreach (call_user_func($resource . '::getRelations') as $resourceManager) {
                $testCases[$resourceManager] = $resourceManager;
            }
        }

        foreach ($testCases as $class) {
            $this->assertTrue(
                in_array(TranslatesStrings::class, class_uses_recursive($class)),
                sprintf('Expected class %s to use TranslatesStrings, but it did not', $class)
            );
        }
    }
}
