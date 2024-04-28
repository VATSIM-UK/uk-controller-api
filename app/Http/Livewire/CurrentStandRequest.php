<?php

namespace App\Http\Livewire;

use App\Filament\Helpers\DisplaysStandStatus;
use App\Models\Stand\StandRequest;
use App\Models\Stand\StandRequestHistory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class CurrentStandRequest extends Component
{
    use DisplaysStandStatus;
    use ShowsRequestTimeInformation;

    public StandRequest $standRequest;

    public function relinquish(int $id): void
    {
        DB::transaction(function () use ($id) {
            $request = StandRequest::where('user_id', Auth::id())
                ->where('id', $id)
                ->first();

            if (!$request) {
                return;
            }

            $request->delete();
            StandRequestHistory::find($id)
                ->update(['deleted_at' => Carbon::now()]);
            $this->dispatch('currentStandRequestRelinquished');
        });
    }

    public function getStandStatusProperty(): array
    {
        return $this->getStandStatus($this->standRequest->stand, $this->getUserAircraft());
    }

    public function getRequestedTimeProperty(): array
    {
        return $this->getRequestTimeViewData($this->standRequest->requested_time);
    }
}
