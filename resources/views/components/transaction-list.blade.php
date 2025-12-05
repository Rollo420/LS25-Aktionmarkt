<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Typ</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Details</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Betrag</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Datum</th>
            </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            @foreach($transactions as $transaction)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            @php $type = $transaction->type; @endphp
                            @switch($type)
                                @case('buy')
                                    <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-green-900 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                                        </svg>
                                    </div>
                                @break
                                @case('sell')
                                    <div class="w-8 h-8 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-withe-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                        </svg>
                                    </div>
                                @break
                                @case('deposit')
                                    <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                    </div>
                                @break
                                @case('withdraw')
                                    <div class="w-8 h-8 bg-orange-100 dark:bg-orange-900 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                        </svg>
                                    </div>
                                @break
                                @case('transfer')
                                    <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                        </svg>
                                    </div>
                                @break
                                @case('dividend')
                                    <div class="w-8 h-8 bg-yellow-100 dark:bg-yellow-900 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                        </svg>
                                    </div>
                                @break
                                @default
                                    <div class="w-8 h-8 bg-gray-100 dark:bg-gray-900 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                            @endswitch
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                @switch($type)
                                    @case('buy') Kauf @break
                                    @case('sell') Verkauf @break
                                    @case('deposit') Einzahlung @break
                                    @case('withdraw') Auszahlung @break
                                    @case('transfer') Überweisung @break
                                    @case('dividend') Dividende @break
                                    @default {{ $type ?? 'Unbekannt' }}
                                @endswitch
                            </div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                    @if($type === 'dividend')
                        {{ number_format($transaction->quantity, 0, ',', '.') }}x {{ $transaction->stock?->name ?? 'Aktien' }} x {{ number_format($transaction->price_at_buy, 2, ',', '.') }} €
                    @elseif(in_array($type, ['buy', 'sell']))
                        {{ number_format($transaction->quantity, 0, ',', '.') }} Aktien <br> <span class="text-xs">{{ $transaction->stock?->name ?? '' }}</span>
                    @else
                        -
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    @if($type === 'dividend')
                        <span class="text-green-600 dark:text-green-400">+{{ number_format($transaction->quantity * $transaction->price_at_buy, 2, ',', '.') }} €</span>
                    @elseif($type === 'buy')
                        <span class="text-red-600 dark:text-red-400">-{{ number_format($transaction->quantity * $transaction->price_at_buy, 2, ',', '.') }} €</span>
                    @elseif($type === 'sell')
                        <span class="text-green-600 dark:text-green-400">+{{ number_format($transaction->quantity * $transaction->price_at_buy, 2, ',', '.') }} €</span>
                    @elseif(in_array($type, ['deposit', 'withdraw', 'transfer']))
                        <span class="{{ $type === 'withdraw' || $type === 'transfer' ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                            {{ $type === 'withdraw' || $type === 'transfer' ? '-' : '+' }}{{ number_format($transaction->quantity, 2, ',', '.') }} €
                        </span>
                    @else
                        {{ number_format($transaction->quantity, 2, ',', '.') }} €
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    @php $isOpen = $transaction->status ?? false; @endphp
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                        {{ $type === 'buy' || $type === 'sell' || !$isOpen ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' }}">
                        {{ $type === 'buy' || $type === 'sell' || !$isOpen ? 'Abgeschlossen' : 'Offen' }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                    {{ \Carbon\Carbon::parse($transaction->created_at)->format('d.m.Y H:i') }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

