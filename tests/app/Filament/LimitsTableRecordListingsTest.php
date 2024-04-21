<?php

namespace App\Filament;

use App\BaseUnitTestCase;
use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use Filament\Facades\Filament;

class LimitsTableRecordListingsTest extends BaseUnitTestCase
{
    public function testItLimitsHowManyRecordsCanBeLoadedInATable()
    {
        $testCases = [];
        foreach (Filament::getResources() as $resource) {
            $testCases[$resource] = call_user_func($resource . '::getPages')['index']->getPage();

            foreach (call_user_func($resource . '::getRelations') as $resourceManager) {
                $testCases[$resourceManager] = $resourceManager;
            }
        }

        foreach ($testCases as $class) {
            $this->assertTrue(
                in_array(LimitsTableRecordListingOptions::class, class_uses_recursive($class)),
                sprintf('Expected class %s to use LimitsTableRecordListingOptions, but it did not', $class)
            );
        }
    }
}
