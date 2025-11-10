<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto space-y-8">

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

                <div
                    class="bg-white dark:bg-gray-800 shadow rounded-xl p-6 flex flex-col justify-center items-center transform hover:scale-[1.02] transition duration-150">
                    <div class="text-gray-500 dark:text-gray-400 text-sm font-medium uppercase tracking-wider">
                        Gesamtwert Portfolio</div>
                    <div class="text-4xl font-extrabold text-indigo-600 dark:text-indigo-400 mt-2">
                        {{ number_format($depotInfo['totalPortfolioValue'] ?? 0, 2, ',', '.') }} €
                    </div>
                </div>

                @php
                    $perf3M = $depotInfo['monthly_performance']['3_month']['percent'] ?? 0.0;
                    $isPositive3M = $perf3M >= 0;
                    $color3M = $isPositive3M ? 'text-green-500 ' : 'text-red-500';
                    $sign3M = $isPositive3M ? '+' : '';
                @endphp
                <div
                    class="bg-white dark:bg-gray-800 shadow rounded-xl p-6 flex flex-col justify-center items-center transform hover:scale-[1.02] transition duration-150">
                    <div class="text-gray-500 dark:text-gray-400 text-sm font-medium uppercase tracking-wider">
                        Performance (3 Monate)</div>
                    <div class="text-4xl font-extrabold {{ $color3M }} mt-2">
                        {{ $sign3M }}{{ number_format($perf3M, 2, ',', '.') }} %
                    </div>
                </div>

                @php
                    $perf6M = $depotInfo['monthly_performance']['6_month']['percent'] ?? 0.0;
                    $isPositive6M = $perf6M >= 0;
                    $color6M = $isPositive6M ? 'text-green-500' : 'text-red-500';
                    $sign6M = $isPositive6M ? '+' : '';
                @endphp
                <div
                    class="bg-white dark:bg-gray-800 shadow rounded-xl p-6 flex flex-col justify-center items-center transform hover:scale-[1.02] transition duration-150">
                    <div class="text-gray-500 dark:text-gray-400 text-sm font-medium uppercase tracking-wider">
                        Performance (6 Monate)</div>
                    <div class="text-4xl font-extrabold {{ $color6M }} mt-2">
                        {{ $sign6M }}{{ number_format($perf6M, 2, ',', '.') }} %
                    </div>
                </div>

                <div
                    class="bg-white dark:bg-gray-800 shadow rounded-xl p-6 flex flex-col justify-center items-center transform hover:scale-[1.02] transition duration-150">
                    <div class="text-gray-500 dark:text-gray-400 text-sm font-medium uppercase tracking-wider">Avg.
                        Dividendenrendite</div>
                    <div class="text-4xl font-extrabold text-yellow-600 dark:text-yellow-400 mt-2">
                        {{ number_format($depotInfo['averages']['avg_dividend_percent_total'] ?? 0, 2, ',', '.') }} %
                    </div>
                </div>
            </div>

            ---

            <div class="bg-white dark:bg-gray-800 shadow rounded-xl p-6">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4 border-b pb-2">Historischer
                    Depotwert</h3>
                <div class="w-full">
                    <x-chart-show type="line" :data="$depotInfo['chartData'] ?? []" :options="['aspectRatio' => 3]"
                        class="h-80" />
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                    Hinweis: Der dargestellte historische Depotwert basiert ausschließlich auf den gehaltenen Aktien (Kurswert pro Monat) und berücksichtigt kein Cash/Guthaben oder sonstige Kontostände.
                </div>
            </div>

            ---

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <div class="bg-white dark:bg-gray-800 shadow rounded-xl p-6 lg:col-span-1">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-3 flex items-center">
                        <span class="text-indigo-500 mr-2">🥇</span> Performance vs. Benchmark
                    </h3>
                    @php
                        $portfPerf = $depotInfo['monthly_performance']['6_month']['percent'] ?? 0.0; // Nehmen wir 6 Monate als Beispiel
                        $benchPerf = $depotInfo['monthly_performance']['benchmark_ytd_percent'] ?? 0.0;
                        $benchName = $depotInfo['monthly_performance']['benchmark_name'] ?? 'Index';

                        $outperformance = $portfPerf - $benchPerf;
                        $outperfColor = $outperformance >= 0 ? 'text-green-500' : 'text-red-500';
                        $outperfSign = $outperformance >= 0 ? '+' : '';
                    @endphp
                    <div class="space-y-3">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-500 dark:text-gray-400">Portfolio (6M)</span>
                            <span
                                class="font-bold {{ $color6M }}">{{ $sign6M }}{{ number_format($portfPerf, 2, ',', '.') }}
                                %</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-500 dark:text-gray-400">{{ $benchName }} (YTD)</span>
                            <span
                                class="font-bold text-gray-900 dark:text-gray-100">{{ number_format($benchPerf, 2, ',', '.') }}
                                %</span>
                        </div>
                        <div class="border-t pt-3 flex justify-between items-center">
                            <span class="font-semibold text-gray-900 dark:text-gray-100">Outperformance</span>
                            <span
                                class="font-extrabold {{ $outperfColor }}">{{ $outperfSign }}{{ number_format($outperformance, 2, ',', '.') }}
                                %</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow rounded-xl p-6 lg:col-span-2">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-3 flex items-center">
                        <span class="text-red-500 mr-2">⚠️</span> Risiko-Kennzahlen
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">

                        @php
                            $cash = $depotInfo['risk_metrics']['cash_balance'] ?? 0;
                            $capital = $depotInfo['risk_metrics']['total_capital'] ?? $depotInfo['totalPortfolioValue'] ?? 0;
                            $cashPercent = $capital > 0 ? ($cash / $capital) * 100 : 0;
                            $investmentPercent = 100 - $cashPercent;
                        @endphp
                        <div class="flex flex-col border-l-4 border-yellow-500 pl-4">
                            <span class="text-gray-500 dark:text-gray-400 font-medium">Investitionsquote / Cash</span>
                            <span
                                class="font-bold text-2xl text-indigo-500 dark:text-indigo-400">{{ number_format($investmentPercent, 1, ',', '.') }}
                                %</span>
                            <span class="text-sm text-gray-500 dark:text-gray-400 mt-1">Cash-Anteil:
                                {{ number_format($cashPercent, 1, ',', '.') }} %
                                ({{ number_format($cash, 0, ',', '.') }} €)</span>
                        </div>

                        @php
                            $beta = $depotInfo['risk_metrics']['portfolio_beta'] ?? 1.0;
                            $betaColor = $beta >= 1.2 ? 'text-red-500' : ($beta >= 1.0 ? 'text-yellow-500' : 'text-green-500');
                            $betaText = $beta > 1 ? 'Volatiler' : ($beta < 1 ? 'Weniger Volatil' : 'Marktkonform');
                        @endphp
                        <div class="flex flex-col border-l-4 border-red-500 pl-4">
                            <span class="text-gray-500 dark:text-gray-400 font-medium">Portfolio Beta-Wert</span>
                            <span
                                class="font-bold text-2xl {{ $betaColor }} dark:text-red-400">{{ number_format($beta, 2, ',', '.') }}</span>
                            <span class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $betaText }} (vs.
                                {{ $depotInfo['monthly_performance']['benchmark_name'] ?? 'Index' }})</span>
                        </div>
                    </div>
                </div>
            </div>

            ---

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <div class="bg-white dark:bg-gray-800 shadow rounded-xl p-6 lg:col-span-2">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4 border-b pb-2">Monatlicher
                        Dividenden-Ertragsplan</h3>
                    <div class="w-full">
                        @php
                            $chartDataDividends = [
                                'labels' => $depotInfo['dividend_chart']['labels'] ?? [],
                                'datasets' => [
                                    [
                                        'label' => 'Erwartete Dividende (€)',
                                        'data' => $depotInfo['dividend_chart']['data'] ?? [],
                                        'backgroundColor' => '#f59e0b', // Yellow-500
                                    ],
                                ],
                            ];
                        @endphp
                        <x-chart-show type="bar" :data="$chartDataDividends" :options="['aspectRatio' => 3]"
                            class="h-80" />
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow rounded-xl p-6 lg:col-span-1">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-3 flex items-center">
                        <span class="text-green-500 mr-2">🛒</span> Dividenden-Kaufkraft
                    </h3>
                    @php
                        $annualDiv = $depotInfo['purchasing_power']['annual_gross_dividend'] ?? 0;
                        $buyStock = $depotInfo['purchasing_power']['stock_name'] ?? 'N/A';
                        $buyQty = $depotInfo['purchasing_power']['can_buy_quantity'] ?? 0;
                    @endphp
                    <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            Ihre erwarteten Bruttodividenden pro Jahr belaufen sich auf
                            <span
                                class="font-extrabold text-yellow-600 dark:text-yellow-400">{{ number_format($annualDiv, 2, ',', '.') }}
                                €</span>.
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">
                            Damit könnten Sie aktuell **{{ number_format($buyQty, 2, ',', '.') }}** Stück
                            der Aktie **{{ $buyStock }}** nachkaufen.
                        </p>
                    </div>
                    <div class="mt-4 text-xs text-gray-400">
                        *Basierend auf Bruttodividenden und aktuellem Preis der {{ $buyStock }}-Aktie.
                    </div>
                </div>
            </div>

            ---

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="bg-white dark:bg-gray-800 shadow rounded-xl p-6 lg:col-span-1">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-3 flex items-center">
                        <span class="text-green-500 mr-2">🚀</span> Top 3 Gewinner (Gesamt P/L)
                    </h3>
                    <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($depotInfo['tops']['topThreeUp'] ?? [] as $winner)
                            @php
                                $amount = $winner->profit_loss ?? 0;
                                $percent = $winner->profit_loss_percent ?? 0;
                                $sign = $amount >= 0 ? '+' : '';
                                $color = $amount >= 0 ? 'text-green-500' : 'text-red-500';
                            @endphp
                            <li class="py-3 group">
                                <div class="flex justify-between items-center">
                                    <div class="font-semibold text-gray-900 dark:text-gray-100 group-hover:text-indigo-500">
                                        {{ $winner->stock->name ?? 'N/A' }}</div>
                                    <div class="font-bold {{ $color }} text-sm">
                                        {{ $sign }}{{ number_format($percent, 2, ',', '.') }} %
                                    </div>
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 flex justify-between">
                                    <span>P/L: <span
                                            class="{{ $color }}">{{ $sign }}{{ number_format($amount, 2, ',', '.') }}
                                            €</span></span>
                                    <span>Kauf: {{ number_format($winner->avg_buy_price ?? 0, 2, ',', '.') }} €</span>
                                    <span>Menge: {{ $winner->quantity ?? 0 }}</span>
                                </div>
                            </li>
                        @empty
                            <li class="py-3 text-gray-400">Keine Top-Gewinner verfügbar.</li>
                        @endforelse
                    </ul>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow rounded-xl p-6 lg:col-span-1">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-3 flex items-center">
                        <span class="text-red-500 mr-2">📉</span> Top 3 Verlierer (Gesamt P/L)
                    </h3>
                    <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($depotInfo['tops']['topThreeDown'] ?? [] as $loser)
                            @php
                                $amount = $loser->profit_loss ?? 0;
                                $percent = $loser->profit_loss_percent ?? 0;
                                $sign = $amount >= 0 ? '+' : '';
                                $color = $amount < 0 ? 'text-red-500' : 'text-gray-500';
                            @endphp
                            <li class="py-3 group">
                                <div class="flex justify-between items-center">
                                    <div class="font-semibold text-gray-900 dark:text-gray-100 group-hover:text-indigo-500">
                                        {{ $loser->stock->name ?? 'N/A' }}</div>
                                    <div class="font-bold {{ $color }} text-sm">
                                        {{ $sign }}{{ number_format($percent, 2, ',', '.') }} %
                                    </div>
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 flex justify-between">
                                    <span>P/L: <span
                                            class="{{ $color }}">{{ $sign }}{{ number_format($amount, 2, ',', '.') }}
                                            €</span></span>
                                    <span>Kauf: {{ number_format($loser->avg_buy_price ?? 0, 2, ',', '.') }} €</span>
                                    <span>Menge: {{ $loser->quantity ?? 0 }}</span>
                                </div>
                            </li>
                        @empty
                            <li class="py-3 text-gray-400">Keine Top-Verlierer verfügbar.</li>
                        @endforelse
                    </ul>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow rounded-xl p-6 lg:col-span-1">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-3 flex items-center">
                        <span class="text-yellow-500 mr-2">📅</span> Nächste Dividenden
                    </h3>
                    <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse(collect($depotInfo['nextDividends'] ?? [])->sortBy('next_dividend') as $dividend)
                            @php
                                $nextDividend = $dividend['next_dividend'] ?? now();
                                $isFuture = \Carbon\Carbon::parse($nextDividend)->isFuture();
                                $dateFormatted = \Carbon\Carbon::parse($nextDividend)->format('d.m.Y');
                                $statusIcon = $isFuture ? '🟢' : '⚪';
                                $statusColor = $isFuture ? 'text-green-600 dark:text-green-400' : 'text-gray-400 dark:text-gray-500';
                            @endphp
                            <li class="py-3 flex justify-between items-center group">
                                <div class="flex-grow">
                                    <span
                                        class="font-semibold text-gray-800 dark:text-gray-100 group-hover:text-indigo-500">{{ ucfirst($dividend['name'] ?? 'N/A') }}</span>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        <span
                                            class="font-bold text-yellow-500">{{ number_format($dividend['dividend'] ?? 0, 2, ',', '.') }}
                                            €</span>
                                        | Rendite: {{ number_format($dividend['percent'] ?? 0, 2, ',', '.') }} %
                                    </div>
                                </div>
                                <div class="flex flex-col items-end">
                                    <span class="text-xs font-medium {{ $statusColor }} flex items-center">
                                        {{ $statusIcon }} {{ $dateFormatted }}
                                    </span>
                                </div>
                            </li>
                        @empty
                            <li class="py-3 text-gray-500 dark:text-gray-400 text-sm">Keine Dividendeninformationen
                                verfügbar.</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <div class="bg-white dark:bg-gray-800 shadow rounded-xl p-6 lg:col-span-2">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4 border-b pb-2">Detaillierte
                        Portfolio-Statistiken</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-6 text-base">

                        @php
                            $allPositions = collect($depotInfo['tops']['topThreeUp'] ?? [])->merge(collect($depotInfo['tops']['topThreeDown'] ?? []));
                            $allDividends = collect($depotInfo['nextDividends'] ?? []);

                            $totalUniqueStocks = $allPositions->unique(fn($item) => $item->stock->id ?? null)->count();
                            $totalQuantity = $allPositions->sum('quantity');
                            $totalCurrentValue = $depotInfo['totalPortfolioValue'] ?? 0;
                            $totalDividendAmount = $allDividends->sum('dividend');
                        @endphp

                        <div class="flex flex-col border-l-4 border-indigo-500 pl-4">
                            <span class="text-gray-500 dark:text-gray-400 font-medium">Unique Aktienarten</span>
                            <span
                                class="font-bold text-2xl text-gray-900 dark:text-gray-100">{{ $totalUniqueStocks }}</span>
                        </div>

                        <div class="flex flex-col border-l-4 border-indigo-500 pl-4">
                            <span class="text-gray-500 dark:text-gray-400 font-medium">Gesamt gehaltene Menge</span>
                            <span
                                class="font-bold text-2xl text-gray-900 dark:text-gray-100">{{ $totalQuantity }}</span>
                        </div>

                        <div class="flex flex-col border-l-4 border-yellow-500 pl-4">
                            <span class="text-gray-500 dark:text-gray-400 font-medium">Gesamt-Dividenden (p.P.)</span>
                            <span
                                class="font-bold text-2xl text-yellow-600 dark:text-yellow-400">{{ number_format($totalDividendAmount, 2, ',', '.') }}
                                €</span>
                        </div>

                        <div class="flex flex-col border-l-4 border-indigo-500 pl-4">
                            <span class="text-gray-500 dark:text-gray-400 font-medium">Aktueller Wert (Bestand)</span>
                            <span
                                class="font-bold text-2xl text-gray-900 dark:text-gray-100">{{ number_format($totalCurrentValue, 2, ',', '.') }}
                                €</span>
                        </div>

                        <div class="flex flex-col border-l-4 border-gray-500 pl-4">
                            <span class="text-gray-500 dark:text-gray-400 font-medium">Avg. Kaufpreis/Aktie</span>
                            <span
                                class="font-bold text-2xl text-gray-900 dark:text-gray-100">{{ number_format($depotInfo['averages']['avg_stock_price_eur'] ?? 0, 2, ',', '.') }}
                                €</span>
                        </div>

                        <div class="flex flex-col border-l-4 border-gray-500 pl-4">
                            <span class="text-gray-500 dark:text-gray-400 font-medium">Avg. Dividende/Aktie</span>
                            <span
                                class="font-bold text-2xl text-gray-900 dark:text-gray-100">{{ number_format($depotInfo['averages']['avg_dividend_amount_eur'] ?? 0, 2, ',', '.') }}
                                €</span>
                        </div>

                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow rounded-xl p-6 lg:col-span-1">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4 border-b pb-2 flex items-center">
                        <span class="text-gray-500 mr-2">🧾</span> Letzte 5 Transaktionen
                    </h3>

                    @php
                        $lastTransactions = collect($depotInfo['lastTransactions'] ?? [])->take(5);
                    @endphp

                    @if($lastTransactions->count() > 0)
                        <x-transaction-list :transaktionens="$lastTransactions" />
                    @else
                        <p class="text-gray-500 dark:text-gray-400 text-sm">Keine aktuellen Transaktionen vorhanden.</p>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>