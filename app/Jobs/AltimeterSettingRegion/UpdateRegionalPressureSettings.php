<?php

namespace App\Jobs\AltimeterSettingRegion;

use App\Services\RegionalPressureService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class UpdateRegionalPressureSettings implements ShouldQueue
{
    use Dispatchable;
    use Queueable;
    use SerializesModels;

    private Collection $metars;

    public function __construct(Collection $metars)
    {
        $this->metars = $metars;
    }

    public function handle(RegionalPressureService $service): void
    {
        $service->updateRegionalPressuresFromMetars($this->metars);
    }
}
