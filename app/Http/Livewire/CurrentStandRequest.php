<?php

namespace App\Http\Livewire;

use App\Models\Stand\StandRequest;
use App\Models\Stand\StandRequestHistory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class CurrentStandRequest extends Component
{
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
            $this->emit('currentStandRequestRelinquished');
        });
    }
}
