<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Depot Übersicht') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
<table class="min-w-full text-center text-sm"> <!-- text-center statt text-left -->
    <thead class="border-b border-gray-700 bg-gray-900/20">
        <tr>
            <th class="px-4 py-2">Name</th>
            <th class="px-4 py-2">Durchschn. Kaufpreis</th>
            <th class="px-4 py-2">Aktueller Preis</th>
            <th class="px-4 py-2">Menge</th>
            <th class="px-4 py-2">Letztes Kauf Datum</th>
            <th class="px-4 py-2">Gewinn / Verlust</th>
        </tr>
    </thead>
    <tbody>
        @forelse($stocks as $stock)
            @php
                $gainLoss = null;
                if ($stock->current_price) {
                    $gainLoss = ($stock->current_price - $stock->avg_buy_price) * $stock->quantity;
                }
            @endphp
            <tr class="border-b border-gray-700 hover:bg-gray-700/30" onclick="window.location='{{ route('stock.show', ['id' => $stock->id]) }}'" style="cursor: pointer;">
                <td class="px-4 py-2 font-semibold">{{ $stock->name }}</td>
                <td class="px-4 py-2">{{ number_format($stock->avg_buy_price, 2) }} €</td>
                <td class="px-4 py-2">
                    @if($stock->current_price)
                        {{ number_format($stock->current_price, 2) }} €
                    @else
                        <span class="text-gray-400">n/a</span>
                    @endif
                </td>
                <td class="px-4 py-2">{{ $stock->quantity }}</td>
                <td class="px-4 py-2">{{ $stock->bought_at->format('d.m.Y H:i') }}</td>
                <td class="px-4 py-2 font-semibold">
                    @if(!is_null($gainLoss))
                        @if($gainLoss >= 0)
                            <span class="text-green-400">+{{ number_format($gainLoss, 2) }} €</span>
                        @else
                            <span class="text-red-400">{{ number_format($gainLoss, 2) }} €</span>
                        @endif
                    @else
                        <span class="text-gray-400">n/a</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="px-4 py-4 text-center text-gray-400">
                    Keine Aktien im Depot gefunden.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>


                </div>
            </div>
        </div>
    </div>
</x-app-layout>