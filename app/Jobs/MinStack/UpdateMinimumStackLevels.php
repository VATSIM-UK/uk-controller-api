<?php

namespace App\Jobs\MinStack;

use App\Services\MinStackLevelService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class UpdateMinimumStackLevels implements ShouldQueue
{
    use Queueable, Dispatchable, SerializesModels;

    private Collection $metars;

    public function __construct(Collection $metars)
    {
        $this->metars = $metars;
    }

    public function handle(MinStackLevelService $service): void
    {
        $service->updateMinimumStackLevelsFromMetars($this->metars);
    }
}
