<?php

namespace App\Helpers\MinStack;

use App\Services\MetarService;

/**
 * Class DirectMinStackCalculation
 *
 * Calculates Min Stack Level by going straight for the airfield QNH
 */
class DirectMinStackCalculation implements MinStackCalculableInterface
{
    /**
     * @var MinStackDataProviderInterface
     */
    private $dataProvider;

    /**
     * @var MetarService
     */
    private $metarService;

    /**
     * DirectMinStackCalculation constructor.
     * @param MinStackDataProviderInterface $dataProvider
     * @param MetarService $metarService
     */
    public function __construct(MinStackDataProviderInterface $dataProvider, MetarService $metarService)
    {
        $this->dataProvider = $dataProvider;
        $this->metarService = $metarService;
    }

    /**
     * Return the minimum stack level
     *
     * @return int|null
     */
    public function calculateMinStack(): ?int
    {
        $qnh = $this->metarService->getQnhFromVatsimMetar($this->dataProvider->calculationFacility());
        if ($qnh === null) {
            return null;
        }

        return MinStackCalculator::calculateMinStack(
            $this->dataProvider,
            $qnh
        );
    }
}
