<div class="mt-4">
    <p class="text-sm sm:text-base text-gray-200 mb-2">Letzte Transaktionen:</p>

    <ul class="list-disc ml-4 sm:ml-6 text-xs sm:text-sm text-gray-400">
        @foreach($transaktionens as $transaction)
            <li class="mb-2">
                <span class="font-semibold">
                    {{ $transaction['type'] ?? 'Unbekannt' }}:
                </span> 
                {{ $transaction['quantity'] ?? 0 }}

                {{-- Statusanzeige (Boolean) --}}
                @php
                    $isClosed = $transaction['status'] ?? false; // true = geschlossen
                @endphp

                @if($isClosed)
                    <span class="text-green-500">(Close)</span>
                @else
                    <span class="text-yellow-400">(Offen)</span>
                @endif

                <span class="text-gray-500">
                    - {{ isset($transaction['created_at'])
            ? \Carbon\Carbon::parse($transaction['created_at'])->format('d.m.Y H:i')
            : 'Unbekanntes Datum' }}
                </span>
            </li>
        @endforeach
    </ul>
</div>