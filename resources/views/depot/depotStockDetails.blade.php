<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight hover:text-blue-600 transition-colors"
                    href="{{ route('stock.store', $stock->id) }}">{{ $stock->name }}</a>
                <span class="text-sm text-gray-500 dark:text-gray-400">{{ $stock->firma }}</span>
            </div>
            <div class="flex items-center space-x-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                    {{ $stock->sektor }}
                </span>
                <span class="text-sm text-gray-500 dark:text-gray-400">{{ $stock->land }}</span>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        @foreach (['success', 'error', 'warning', 'info'] as $msg)
            @if(session($msg))
                <div class="mb-6 p-4 rounded-lg {{ $msg === 'success' ? 'bg-green-50 text-green-800 border border-green-200' : ($msg === 'error' ? 'bg-red-50 text-red-800 border border-red-200' : ($msg === 'warning' ? 'bg-yellow-50 text-yellow-800 border border-yellow-200' : 'bg-blue-50 text-blue-800 border border-blue-200')) }} dark:bg-gray-800 dark:text-gray-100">
                    {{ session($msg) }}
                </div>
            @endif
        @endforeach

        <div class="max-w-7xl mx-auto space-y-8">
            <!-- Key Metrics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                Aktueller Preis
                            </dt>
                            <dd class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                {{ number_format($stockData->current_price, 2, ',', '.') }} €
                            </dd>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                Durchschnittlicher Einkaufspreis
                            </dt>
                            <dd class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                {{ number_format($stockData->avg_buy_price, 2, ',', '.') }} €
                            </dd>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                Gesamtmenge
                            </dt>
                            <dd class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                {{ number_format($stockData->quantity, 0, ',', '.') }} Stk.
                            </dd>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-indigo-500 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                Aktueller Depotwert
                            </dt>
                            <dd class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                {{ number_format($stockData->quantity * $stockData->current_price, 2, ',', '.') }} €
                            </dd>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 {{ $stockData->profit_loss >= 0 ? 'bg-green-500' : 'bg-red-500' }} rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $stockData->profit_loss >= 0 ? 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6' : 'M13 17h8m0 0V9m0 8l-8-8-4 4-6-6' }}"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                Gewinn / Verlust
                            </dt>
                            <dd class="text-2xl font-bold {{ $stockData->profit_loss >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $stockData->profit_loss >= 0 ? '+' : '' }}{{ number_format($stockData->profit_loss, 2, ',', '.') }} €
                            </dd>
                            <dd class="text-sm {{ $stockData->profit_loss >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                ({{ number_format($stockData->profit_loss_percent, 2, ',', '.') }} %)
                            </dd>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-yellow-500 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                Depot-Anteil
                            </dt>
                            <dd class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                {{ number_format($stockData->deposit_share_in_percent, 1, ',', '.') }} %
                            </dd>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-cyan-500 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                Erster Kauf
                            </dt>
                            <dd class="text-lg font-bold text-gray-900 dark:text-gray-100">
                                {{ $stockData->first_buy }}
                            </dd>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                Letzter Kauf
                            </dt>
                            <dd class="text-lg font-bold text-gray-900 dark:text-gray-100">
                                {{ $stockData->last_buy }}
                            </dd>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dividend Information -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-xl border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                        Dividenden Übersicht
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="text-center">
                            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Nächste Dividende</div>
                            <div class="text-xl font-bold text-gray-900 dark:text-gray-100 mt-1">{{ $stockData->dividende->next_date }}</div>
                            <div class="text-lg font-semibold text-green-600">{{ number_format($stockData->dividende->next_amount, 2, ',', '.') }} €</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $stockData->dividende->frequency_per_year }}× pro Jahr</div>
                        </div>

                        <div class="text-center">
                            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Dividendenrendite</div>
                            <div class="text-xl font-bold text-gray-900 dark:text-gray-100 mt-1">{{ number_format($stockData->dividende->yield_percent, 2, ',', '.') }} %</div>
                        </div>

                        <div class="text-center">
                            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Letzte Dividende</div>
                            <div class="text-xl font-bold text-gray-900 dark:text-gray-100 mt-1">{{ $stockData->dividende->last_date }}</div>
                            <div class="text-lg font-semibold text-green-600">{{ number_format($stockData->dividende->last_amount, 2, ',', '.') }} €</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Erhalten: {{ number_format($stockData->quantity * $stockData->dividende->last_amount, 2, ',', '.') }} €</div>
                        </div>

                        <div class="text-center">
                            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Erwartete 12 Monate</div>
                            <div class="text-xl font-bold text-gray-900 dark:text-gray-100 mt-1">{{ number_format($stockData->dividende->expected_next_12m, 2, ',', '.') }} €</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transaction History -->
            <div x-data="{ visible: 5 }" class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-xl border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            Kaufhistorie
                        </h3>
                        <button @click="visible += 25" x-show="visible < {{ count($stockTransactionsHistory) }}"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            Mehr anzeigen
                        </button>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Datum
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Typ
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Menge
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Preis
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Gesamtbetrag
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($stockTransactionsHistory as $index => $stockTransaction)
                                <tr x-show="{{ $index }} < visible" class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ $stockTransaction->gameTime()->latest()->value('name') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $stockTransaction->type === 'buy' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                            {{ $stockTransaction->type === 'buy' ? 'Kauf' : 'Verkauf' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ number_format($stockTransaction->quantity, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ number_format($stockTransaction->resolvedPriceAtBuy(), 2, ',', '.') }} €
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ number_format($stockTransaction->quantity * $stockTransaction->resolvedPriceAtBuy(), 2, ',', '.') }} €
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Buy/Sell Buttons -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <x-buy_sell-buttons :stock="$stock" />
            </div>
        </div>
    </div>
</x-app-layout>
