<?php


namespace App\Helpers\MinStack;

use App\Services\MetarService;

class LowestMinStackCalculation implements MinStackCalculableInterface
{
    /**
     * @var MinStackDataProviderInterface
     */
    private $dataProviders;
    /**
     * @var MetarService
     */
    private $metarService;

    /**
     * LowestMinStackCalculation constructor.
     * @param MetarService $metarService
     * @param MinStackDataProviderInterface[] $dataProviders
     */
    public function __construct(MetarService $metarService, MinStackDataProviderInterface ...$dataProviders)
    {
        $this->dataProviders = $dataProviders;
        $this->metarService = $metarService;
    }

    /**
     * Return the minimum stack level
     *
     * @return int|null
     */
    public function calculateMinStack(): ?int
    {
        $minQnh = 9999;
        $minQnhProvider = null;
        foreach ($this->dataProviders as $dataProvider) {
            $qnh = $this->metarService->getQnhFromVatsimMetar($dataProvider->calculationFacility());
            if ($qnh === null) {
                continue;
            }

            if ($qnh < $minQnh) {
                $minQnh = $qnh;
                $minQnhProvider = $dataProvider;
            }
        }

        // No QNHs, stop
        if ($minQnh === 9999) {
            return null;
        }

        return MinStackCalculator::calculateMinStack(
            $minQnhProvider,
            $minQnh
        );
    }
}
