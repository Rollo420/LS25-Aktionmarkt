<x-app-layout>
    <x-slot name="header">
        <a class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            href="{{ route('stock.store', $stock->id) }}">{{ $stock->name }}</a>
    </x-slot>

    <div class="py-12">
        @foreach (['success', 'error', 'warning', 'info'] as $msg)
            @if(session($msg))
                <div :class="['p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg']" class="alert alert-{{ $msg }} mb-4">
                    {{ session($msg) }}
                </div>
            @endif
        @endforeach
        <div class="max-w-7xl mx-auto space-y-8">

            <!-- Übersicht Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 flex flex-col items-center">
                    <div class="text-gray-500 dark:text-gray-400 text-sm">Aktueller Preis</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-2">
                        {{ number_format($stockData->current_price, 2, ',', '.') }} €
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 flex flex-col items-center">
                    <div class="text-gray-500 dark:text-gray-400 text-sm">Einkaufpreis</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-2">
                        {{ number_format($stockData->avg_buy_price, 2, ',', '.') }} €
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 flex flex-col items-center">
                    <div class="text-gray-500 dark:text-gray-400 text-sm">Gesamtmenge</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-2">{{ $stockData->quantity }}
                        Stk.</div>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 flex flex-col items-center">
                    <div class="text-gray-500 dark:text-gray-400 text-sm">Aktueller Depotwert</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-2">26.743 €</div>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 flex flex-col items-center">
                    <div class="text-gray-500 dark:text-gray-400 text-sm">Gewinn / Verlust</div>
                    <div
                        class="text-2xl font-bold mt-2 {{ $stockData->profit_loss >= 0 ? 'text-green-500' : 'text-red-500' }}">
                        {{ number_format($stockData->profit_loss, 2, ',', '.') }} €
                        ({{ number_format($stockData->profit_loss_percent, 2, ',','.') }} %)
                    </div>

                </div>

                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 flex flex-col items-center">
                    <div class="text-gray-500 dark:text-gray-400 text-sm">Depot-Anteil</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-2">
                        {{ $stockData->deposit_share_in_percent }} %
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 flex flex-col items-center">
                    <div class="text-gray-500 dark:text-gray-400 text-sm">Erster Kauf</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-2">{{ $stockData->first_buy }}</div>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 flex flex-col items-center">
                    <div class="text-gray-500 dark:text-gray-400 text-sm">Letzter Kauf</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-2">{{ $stockData->last_buy }}</div>
                </div>
            </div>

            <!-- Dividenden Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 text-center">
                    <div class="text-gray-500 dark:text-gray-400 text-sm">Nächste Dividende</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-2">{{ $stockData->dividende->next_date }} : {{ $stockData->dividende->next_amount }} €</div>
                    <div class="text-gray-500 dark:text-gray-400 text-sm mt-1">Frequenz: {{ $stockData->dividende->frequency_per_year }}× pro Jahr</div>
                    <div class="text-gray-500 dark:text-gray-400 text-sm mt-1">Dividendenrendite: {{ $stockData->dividende->yield_percent }} %</div>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 text-center">
                    <div class="text-gray-500 dark:text-gray-400 text-sm">Letzte Dividende</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-2">{{ $stockData->dividende->last_date }}: {{ $stockData->dividende->last_amount }} €</div>
                    <div class="text-gray-500 dark:text-gray-400 text-sm mt-1">Erhalten: {{ number_format($stockData->quantity * $stockData->dividende->last_amount, 2, ',', '.') }} €</div>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 text-center">
                    <div class="text-gray-500 dark:text-gray-400 text-sm">Gesamtdividenden</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-2">{{ number_format($stockData->dividende->total_received, 2, ',', '.') }}€</div>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 text-center">
                    <div class="text-gray-500 dark:text-gray-400 text-sm">Erwartete 12 Monate</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-2">{{ number_format($stockData->dividende->expected_next_12m, 2, ',', '.') }} €</div>
                </div>
            </div>


            <!-- Kaufhistorie -->
            <div x-data="{ visible: 3 }" class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <div class="text-gray-500 dark:text-gray-400 text-sm">Kauf-Historie</div>
                    <button @click="visible += 25" x-show="visible < {{ count($stockTransactionsHistory) }}"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow text-sm">
                        Mehr anzeigen
                    </button>
                </div>

                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead>
                        <tr class="text-left text-sm text-gray-500 dark:text-gray-400">
                            <th class="px-4 py-2">Datum</th>
                            <th class="px-4 py-2">Menge</th>
                            <th class="px-4 py-2">Kaufpreis</th>
                            <th class="px-4 py-2">Gesamtbetrag</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                        @foreach ($stockTransactionsHistory as $index => $stockTransaction)
                            <tr x-show="{{ $index }} < visible">
                                <td class="px-4 py-2">{{ $stockTransaction->gameTime()->latest()->value('name') }}</td>
                                <td class="px-4 py-2">{{ $stockTransaction->quantity }}</td>
                                <td class="px-4 py-2">{{ number_format($stockTransaction->resolvedPriceAtBuy(), 2, ',', '.') }} €</td>
                                <td class="px-4 py-2">
                                    {{ number_format($stockTransaction->quantity * $stockTransaction->resolvedPriceAtBuy(), 2, ',', '.') }}
                                    €
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <x-buy_sell-buttons :stock="$stock" />


        </div>
    </div>
</x-app-layout>