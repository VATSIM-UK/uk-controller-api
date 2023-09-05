<x-filament::page>
    @livewire('stand-predictor-form')

    @if ($this->currentPrediction)
        @foreach ($this->currentPrediction as $allocator => $groups)
            <h2><b>Allocator: {{ $allocator }}</b></h2>
            @if (empty($groups))
                No stands for this allocator.
                @continue
            @endif

            @php
                $rank = 0;
            @endphp
            @while ($rank < count($groups))
                <h3><b>Rank {{ $rank + 1 }}</b></h3>
                <div style="overflow-wrap: break-word">{{ implode(',', $groups[$rank]) }}</div>
                @php
                    $rank++;
                @endphp
            @endwhile
        @endforeach
    @endif
</x-filament::page>
