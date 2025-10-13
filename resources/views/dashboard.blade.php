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
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-2">{{ $depotInfo['totalPortfolioValue'] }} €</div>
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
    echo "<li>" . $winner->stock->name . " (+" . number_format($winner->stock->currentPrice, 2) . "€)</li>";
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
                                echo "<li>" . $winner->stock->name . " (+" . number_format($winner->stock->currentPrice, 2) . "€)</li>";
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
                    <x-transaction-list :transaktionens="$transaktionens ?? collect([])" limit="5" />
                </div>
                <!-- Next Dividend Date -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 flex flex-col items-center">
                    <div class="text-gray-500 dark:text-gray-400 text-sm">Next Dividend</div>
                    <div class="text-lg font-bold text-gray-900 dark:text-gray-100 mt-2">--</div>
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
