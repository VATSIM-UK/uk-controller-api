<?php

namespace App\Services;

use Carbon\Carbon;

class AiracService
{
    private function getBaseAiracDate(): Carbon
    {
        return Carbon::parse('2021-02-25 00:00:00');
    }

    public function getNextAiracDayFromDate(Carbon $date): Carbon
    {
        $difference = $this->getBaseAiracDate()->diffInDays();
        return $difference % 28 === 0
            ? $date->startOfDay()
            : $date->addDays(28 - ($difference % 28))->startOfDay();
    }

    public function getNextAiracDay(): Carbon
    {
        return $this->getNextAiracDayFromDate(Carbon::now());
    }
}
