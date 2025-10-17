<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <!-- Key Figures Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Portfolio Value -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 flex flex-col items-center">
                    <div class="text-gray-500 dark:text-gray-400 text-sm">Total Portfolio Value</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-2">
                        {{ number_format($depotInfo['totalPortfolioValue'], 3, ',', '.') }} €
                    </div>
                </div>
                <!-- Performance -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 flex flex-col items-center">
                    <div class="text-gray-500 dark:text-gray-400 text-sm">Performance</div>
                    <div class="text-lg font-bold text-gray-900 dark:text-gray-100 mt-2">Today: -- € / -- %</div>
                    <div class="text-sm">Week: -- € / -- %</div>
                    <div class="text-sm">Month: -- € / -- %</div>
                </div>
                <!-- Avg. Dividend Yield -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 flex flex-col items-center">
                    <div class="text-gray-500 dark:text-gray-400 text-sm">Avg. Dividend Yield</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-2">-- %</div>
                </div>
                <!-- Avg. P/E Ratio -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 flex flex-col items-center">
                    <div class="text-gray-500 dark:text-gray-400 text-sm">Avg. P/E Ratio</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-2">--</div>
                </div>
            </div>
            <!-- Top/Flop Stocks -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Top 3 Winners -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                    <div class="text-gray-500 dark:text-gray-400 text-sm mb-2">Top 3 Winners</div>
                    <ul class="space-y-1">
                        @php
                            foreach ($depotInfo['tops']['topThreeUp'] as $winner) {
                                $sign = $winner->profit_loss['amount'] >= 0 ? '+' : '-';

                                echo "<li>"
                                    . $winner->stock->name
                                    . " (" . $sign . number_format(abs($winner->profit_loss['amount']), 2, ',', '.') . "€ / "
                                    . $sign . number_format(abs($winner->profit_loss['percent']), 2, ',', '.') . "%)</li>";
                            }

                        @endphp
                    </ul>
                </div>
                <!-- Top 3 Losers -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                    <div class="text-gray-500 dark:text-gray-400 text-sm mb-2">Top 3 Losers</div>
                    <ul class="space-y-1">
                        @php

                            foreach ($depotInfo['tops']['topThreeDown'] as $winner) {
                                echo "<li>"
                                    . $winner->stock->name
                                    . " (" . number_format($winner->profit_loss['amount'], 2, ',', '.') . "€ / "
                                    . number_format($winner->profit_loss['percent'], 2, ',', '.') . "%)</li>";

                            }
                        @endphp
                    </ul>
                </div>
            </div>
            <!-- Latest Transactions & Dividend -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Last 5 Transactions (with component) -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                    <div class="text-gray-500 dark:text-gray-400 text-sm mb-2">Last 5 Transactions</div>
                    <x-transaction-list :transaktionens="$depotInfo['lastTransactions'] ?? collect([])" limit="5" />
                </div>
                <!-- Next Dividend Date -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-3 flex items-center">
                        📅 Dividendenkalender
                    </h3>

                    <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($depotInfo['nextDividens'] as $dividend)
                            @php
                                $isFuture = \Carbon\Carbon::parse($dividend['next_dividend'])->isFuture();
                                $dateFormatted = \Carbon\Carbon::parse($dividend['next_dividend'])->format('d.m.Y');
                            @endphp

                            <li class="py-3 flex justify-between items-center">
                                <div>
                                    <span class="font-semibold text-gray-800 dark:text-gray-100">
                                        {{ ucfirst($dividend['name']) }}
                                    </span>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        @if ($isFuture)
                                            <span class="text-green-500">Dividende erwartet am:</span>
                                        @else
                                            <span class="text-red-500">Letzte Dividende:</span>
                                        @endif
                                        {{ $dateFormatted }}
                                    </div>
                                </div>

                                @if ($isFuture)
                                    <span class="text-sm font-medium text-green-600 dark:text-green-400">🟢 Zukünftig</span>
                                @else
                                    <span class="text-sm font-medium text-gray-400 dark:text-gray-500">⚪ Vergangen</span>
                                @endif
                            </li>
                        @empty
                            <li class="py-3 text-gray-500 dark:text-gray-400 text-sm">
                                Keine Dividendeninformationen verfügbar.
                            </li>
                        @endforelse
                    </ul>
                </div>

            </div>
            <!-- Portfolio Value Chart (bottom, compact and centered) -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-4 flex flex-col items-center mt-8">
                <div class="text-gray-500 dark:text-gray-400 text-sm mb-2">Portfolio Value Chart</div>
                <div class="w-full flex justify-center">
                    <div class="max-w-md w-full">
                        <x-chart-show type="line" :data="$depotInfo['chartData']" :options="[]" class="h-48" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>