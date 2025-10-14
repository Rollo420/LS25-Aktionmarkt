<div class="mt-4">
    <p class="text-gray-200 mb-2">Letzte Transaktionen:</p>
    <ul class="list-disc ml-6 text-gray-400">
       @foreach($transaktionens as $transaction)
            <li class="mb-2">
                <span class="font-semibold">{{ $transaction['type'] }}</span>:
                {{ $transaction['quantity']}}
                @switch($transaction['status'])
                    @case('completed')
                        <span class="text-green-500">({{ $transaction['status'] }})</span>
                        @break
                    @case('pending')
                        <span class="text-yellow-500">({{ $transaction['status'] }})</span>
                        @break
                    @case('cancelled')
                        <span class="text-gray-500">({{ $transaction['status'] }})</span>
                        @break
                    @case('failed')
                        <span class="text-red-500">({{ $transaction['status'] }})</span>
                        @break
                    @case('open')
                        <span class="text-yellow-400">({{ $transaction['status'] }})</span>
                        @break
                    @case('closed')
                        <span class="text-blue-500">({{ $transaction['status'] }})</span>
                        @break
                    @default
                        <span class="text-gray-400">({{ $transaction['status'] }})</span>
                @endswitch
                <span class="text-gray-500">
                    - {{ \Carbon\Carbon::parse($transaction['created_at'])->format('d.m.Y H:i') }}
                </span>

            </li>
       @endforeach
    </ul>
</div>
