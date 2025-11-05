<x-app-layout>
    <x-slot name="header">
        <a class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight" href="{{ route('stock.store', $stock->id) }}">{{ $stock->name }}</a>
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
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-2">{{ $stockData->current_price }} €</div>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 flex flex-col items-center">
                    <div class="text-gray-500 dark:text-gray-400 text-sm">Durchschn. Einkaufpreis</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-2">{{ number_format($stockData->avg_buy_price, 2, ) }} €</div>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 flex flex-col items-center">
                    <div class="text-gray-500 dark:text-gray-400 text-sm">Gesamtmenge</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-2">{{ $stockData->quantity }} Stk.</div>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 flex flex-col items-center">
                    <div class="text-gray-500 dark:text-gray-400 text-sm">Aktueller Depotwert</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-2">26.743 €</div>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 flex flex-col items-center">
                    <div class="text-gray-500 dark:text-gray-400 text-sm">Gewinn / Verlust</div>
                    <div class="text-2xl font-bold mt-2 {{ $stockData->profit_loss['amount'] >= 0 ? 'text-green-500' : 'text-red-500' }}">
                        {{ number_format($stockData->profit_loss['amount'], 2, ',', '.') }} €
                        ({{ $stockData->profit_loss['percent'] }} %)
                    </div>

                </div>

                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 flex flex-col items-center">
                    <div class="text-gray-500 dark:text-gray-400 text-sm">Depot-Anteil</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-2">{{ $stockData->deposit_share_in_percent }} %</div>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 flex flex-col items-center">
                    <div class="text-gray-500 dark:text-gray-400 text-sm">Erster Kauf</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-2">15.03.2022</div>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 flex flex-col items-center">
                    <div class="text-gray-500 dark:text-gray-400 text-sm">Letzter Kauf</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-2">07.10.2025</div>
                </div>
            </div>

            <!-- Dividenden Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 text-center">
                    <div class="text-gray-500 dark:text-gray-400 text-sm">Nächste Dividende</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-2">15.12.2025: 0,85 €</div>
                    <div class="text-gray-500 dark:text-gray-400 text-sm mt-1">Frequenz: 4× pro Jahr</div>
                    <div class="text-gray-500 dark:text-gray-400 text-sm mt-1">Dividendenrendite: 1,8 %</div>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 text-center">
                    <div class="text-gray-500 dark:text-gray-400 text-sm">Letzte Dividende</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-2">15.09.2025: 0,85 €</div>
                    <div class="text-gray-500 dark:text-gray-400 text-sm mt-1">Erhalten: 123,25 €</div>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 text-center">
                    <div class="text-gray-500 dark:text-gray-400 text-sm">Gesamtdividenden</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-2">3.456 €</div>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 text-center">
                    <div class="text-gray-500 dark:text-gray-400 text-sm">Erwartete 12 Monate</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-2">3.680 €</div>
                </div>
            </div>


            <!-- Kaufhistorie -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <div class="text-gray-500 dark:text-gray-400 text-sm">Kauf-Historie</div>
                    <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow text-sm">
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
                        @foreach ($stockTransactionsHistory as $stockTransaction)

                            <tr>
                                <td class="px-4 py-2">{{ $stockTransaction->gameTime()->latest()->value('name')  }}</td>
                                <td class="px-4 py-2">{{ $stockTransaction->quantity }}</td>
                                <td class="px-4 py-2">{{$stockTransaction->resolvedPriceAtBuy()}} €</td>
                                <td class="px-4 py-2">{{ $stockTransaction->quantity * $stockTransaction->resolvedPriceAtBuy()}} €</td>
                            </tr>
                           
                        @endforeach
                            
                        </tr>
                    </tbody>
                </table>
            </div>

            <x-buy_sell-buttons :stock="$stock" />

        </div>
    </div>
</x-app-layout>