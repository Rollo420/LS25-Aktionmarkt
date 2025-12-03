<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Depot-Übersicht | Dashboard</title>
    <!-- Load Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Load Chart.js for graphs -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
    <style>
        /* Custom scrollbar for better look in dark mode */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6; /* Light gray background */
        }
        @media (prefers-color-scheme: dark) {
            body {
                background-color: #111827; /* Dark background */
            }
        }
        /* Ensure charts are responsive and container holds aspect ratio */
        .chart-container {
            position: relative;
            height: 320px;
            width: 100%;
        }
    </style>
</head>
<body class="bg-gray-100 dark:bg-gray-900 transition-colors duration-300">

    <div class="min-h-screen">
        <!-- Header (Simulated x-slot name="header") -->
        <header class="bg-white dark:bg-gray-800 shadow sticky top-0 z-10">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Depot-Dashboard
                </h2>
            </div>
        </header>

        <!-- Main Content (Simulated py-12) -->
        <main class="py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

                <!-- 1. Key Performance Indicators -->
                <div id="kpi-section" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

                    <!-- Gesamtwert Portfolio -->
                    <div id="total-value-card"
                        class="bg-white dark:bg-gray-800 shadow-xl rounded-xl p-6 flex flex-col justify-center items-center transform hover:scale-[1.02] transition duration-150">
                        <div class="text-gray-500 dark:text-gray-400 text-sm font-medium uppercase tracking-wider">
                            Gesamtwert Portfolio</div>
                        <div class="text-4xl font-extrabold text-indigo-600 dark:text-indigo-400 mt-2">
                            Lade...
                        </div>
                    </div>

                    <!-- Performance (3 Monate) -->
                    <div id="perf-3m-card"
                        class="bg-white dark:bg-gray-800 shadow-xl rounded-xl p-6 flex flex-col justify-center items-center transform hover:scale-[1.02] transition duration-150">
                        <div class="text-gray-500 dark:text-gray-400 text-sm font-medium uppercase tracking-wider">
                            Performance (3 Monate)</div>
                        <div class="text-4xl font-extrabold mt-2">
                            Lade...
                        </div>
                    </div>

                    <!-- Performance (6 Monate) -->
                    <div id="perf-6m-card"
                        class="bg-white dark:bg-gray-800 shadow-xl rounded-xl p-6 flex flex-col justify-center items-center transform hover:scale-[1.02] transition duration-150">
                        <div class="text-gray-500 dark:text-gray-400 text-sm font-medium uppercase tracking-wider">
                            Performance (6 Monate)</div>
                        <div class="text-4xl font-extrabold mt-2">
                            Lade...
                        </div>
                    </div>

                    <!-- Avg. Dividendenrendite -->
                    <div id="avg-dividend-card"
                        class="bg-white dark:bg-gray-800 shadow-xl rounded-xl p-6 flex flex-col justify-center items-center transform hover:scale-[1.02] transition duration-150">
                        <div class="text-gray-500 dark:text-gray-400 text-sm font-medium uppercase tracking-wider">Avg.
                            Dividendenrendite</div>
                        <div class="text-4xl font-extrabold text-yellow-600 dark:text-yellow-400 mt-2">
                            Lade...
                        </div>
                    </div>
                </div>

                <!-- 2. Historical Value Chart -->
                <div class="bg-white dark:bg-gray-800 shadow-xl rounded-xl p-6">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4 border-b pb-2">Historischer
                        Depotwert</h3>
                    <div class="chart-container">
                        <canvas id="historicalChart"></canvas>
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                        Hinweis: Der dargestellte historische Depotwert basiert ausschließlich auf den gehaltenen Aktien (Kurswert pro Monat) und berücksichtigt kein Cash/Guthaben oder sonstige Kontostände.
                    </div>
                </div>

                <!-- 3. Benchmark & Risk Metrics -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    <!-- Performance vs. Benchmark -->
                    <div class="bg-white dark:bg-gray-800 shadow-xl rounded-xl p-6 lg:col-span-1">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-3 flex items-center">
                            <span class="text-indigo-500 mr-2">🥇</span> Performance vs. Benchmark
                        </h3>
                        <div id="benchmark-data" class="space-y-3">
                            <!-- Data will be injected here -->
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-500 dark:text-gray-400">Portfolio (6M)</span>
                                <span class="font-bold text-gray-500">Lade...</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-500 dark:text-gray-400" id="benchmark-name-ytd">Index (YTD)</span>
                                <span class="font-bold text-gray-900 dark:text-gray-100" id="benchmark-perf">Lade...</span>
                            </div>
                            <div class="border-t pt-3 flex justify-between items-center">
                                <span class="font-semibold text-gray-900 dark:text-gray-100">Outperformance</span>
                                <span class="font-extrabold" id="outperformance-value">Lade...</span>
                            </div>
                        </div>
                    </div>

                    <!-- Risiko-Kennzahlen -->
                    <div class="bg-white dark:bg-gray-800 shadow-xl rounded-xl p-6 lg:col-span-2">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-3 flex items-center">
                            <span class="text-red-500 mr-2">⚠️</span> Risiko-Kennzahlen
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">

                            <!-- Investitionsquote / Cash -->
                            <div id="investment-ratio" class="flex flex-col border-l-4 border-yellow-500 pl-4">
                                <span class="text-gray-500 dark:text-gray-400 font-medium">Investitionsquote / Cash</span>
                                <span class="font-bold text-2xl text-indigo-500 dark:text-indigo-400">Lade...</span>
                                <span class="text-sm text-gray-500 dark:text-gray-400 mt-1">Cash-Anteil: Lade...</span>
                            </div>

                            <!-- Portfolio Beta-Wert -->
                            <div id="portfolio-beta" class="flex flex-col border-l-4 border-red-500 pl-4">
                                <span class="text-gray-500 dark:text-gray-400 font-medium">Portfolio Beta-Wert</span>
                                <span class="font-bold text-2xl text-red-500 dark:text-red-400">Lade...</span>
                                <span class="text-sm text-gray-500 dark:text-gray-400 mt-1">Lade... (vs. Index)</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. Dividends -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    <!-- Monatlicher Dividenden-Ertragsplan -->
                    <div class="bg-white dark:bg-gray-800 shadow-xl rounded-xl p-6 lg:col-span-2">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4 border-b pb-2">Monatlicher
                            Dividenden-Ertragsplan</h3>
                        <div class="chart-container">
                            <canvas id="dividendChart"></canvas>
                        </div>
                    </div>

                    <!-- Dividenden-Kaufkraft -->
                    <div class="bg-white dark:bg-gray-800 shadow-xl rounded-xl p-6 lg:col-span-1">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-3 flex items-center">
                            <span class="text-green-500 mr-2">🛒</span> Dividenden-Kaufkraft
                        </h3>
                        <div id="purchasing-power" class="mt-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <!-- Data will be injected here -->
                            <p class="text-sm text-gray-600 dark:text-gray-300">Lade...</p>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">Lade...</p>
                        </div>
                        <div id="purchasing-power-note" class="mt-4 text-xs text-gray-400">
                            *Basierend auf Bruttodividenden und aktuellem Preis der Aktie.
                        </div>
                    </div>
                </div>

                <!-- 5. Top Movers & Next Dividends -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    <!-- Top 3 Gewinner -->
                    <div class="bg-white dark:bg-gray-800 shadow-xl rounded-xl p-6 lg:col-span-1">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-3 flex items-center">
                            <span class="text-green-500 mr-2">🚀</span> Top 3 Gewinner (Gesamt P/L)
                        </h3>
                        <ul id="top-winners-list" class="divide-y divide-gray-200 dark:divide-gray-700">
                            <li class="py-3 text-gray-400">Lade...</li>
                        </ul>
                    </div>

                    <!-- Top 3 Verlierer -->
                    <div class="bg-white dark:bg-gray-800 shadow-xl rounded-xl p-6 lg:col-span-1">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-3 flex items-center">
                            <span class="text-red-500 mr-2">📉</span> Top 3 Verlierer (Gesamt P/L)
                        </h3>
                        <ul id="top-losers-list" class="divide-y divide-gray-200 dark:divide-gray-700">
                            <li class="py-3 text-gray-400">Lade...</li>
                        </ul>
                    </div>

                    <!-- Nächste Dividenden -->
                    <div class="bg-white dark:bg-gray-800 shadow-xl rounded-xl p-6 lg:col-span-1">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-3 flex items-center">
                            <span class="text-yellow-500 mr-2">📅</span> Nächste Dividenden
                        </h3>
                        <ul id="next-dividends-list" class="divide-y divide-gray-200 dark:divide-gray-700">
                            <li class="py-3 text-gray-400">Lade...</li>
                        </ul>
                    </div>
                </div>

                <!-- 6. Detailed Statistics & Transactions -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    <!-- Detaillierte Portfolio-Statistiken -->
                    <div class="bg-white dark:bg-gray-800 shadow-xl rounded-xl p-6 lg:col-span-2">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4 border-b pb-2">Detaillierte
                            Portfolio-Statistiken</h3>
                        <div id="detailed-stats" class="grid grid-cols-2 md:grid-cols-3 gap-6 text-base">
                            <!-- Data will be injected here -->
                        </div>
                    </div>

                    <!-- Letzte 5 Transaktionen -->
                    <div class="bg-white dark:bg-gray-800 shadow-xl rounded-xl p-6 lg:col-span-1">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4 border-b pb-2 flex items-center">
                            <span class="text-gray-500 mr-2">🧾</span> Letzte 5 Transaktionen
                        </h3>
                        <ul id="transaction-list" class="divide-y divide-gray-200 dark:divide-gray-700">
                            <li class="py-3 text-gray-400">Lade...</li>
                        </ul>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <script>
        const MOCK_DEPOT_INFO = {
            totalPortfolioValue: 154567.89,
            monthly_performance: {
                '3_month': { amount: 16400, percent: 12.56 },
                '6_month': { amount: -7000, percent: -4.33 },
                benchmark_ytd_percent: 8.75,
                benchmark_name: 'MSCI World Index'
            },
            averages: {
                avg_dividend_percent_total: 3.15,
                avg_stock_price_eur: 55.40,
                avg_dividend_amount_eur: 1.25
            },
            chartData: {
                labels: ["Jan '24", "Feb '24", "Mar '24", "Apr '24", "May '24", "Jun '24"],
                datasets: [{
                    label: "Depotwert (€)",
                    data: [120000, 125000, 130000, 140000, 145000, 154567.89],
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    tension: 0.3,
                    fill: true
                }]
            },
            risk_metrics: {
                cash_balance: 14567.89,
                total_capital: 154567.89,
                portfolio_beta: 1.15
            },
            dividend_chart: {
                labels: ["Jan", "Feb", "Mär", "Apr", "Mai", "Jun", "Jul", "Aug", "Sep", "Okt", "Nov", "Dez"],
                data: [150, 220, 0, 310, 180, 0, 450, 110, 0, 290, 160, 0]
            },
            purchasing_power: {
                annual_gross_dividend: 5000.00,
                stock_name: 'Apple Inc. (AAPL)',
                can_buy_quantity: 26.5
            },
            tops: {
                topThreeUp: [
                    { stock: { name: 'Microsoft Corp' }, profit_loss: 4500.20, profit_loss_percent: 35.10, avg_buy_price: 180.50, quantity: 15 },
                    { stock: { name: 'Johnson & Johnson' }, profit_loss: 1100.00, profit_loss_percent: 15.50, avg_buy_price: 130.00, quantity: 50 },
                    { stock: { name: 'Coca-Cola Co' }, profit_loss: 650.75, profit_loss_percent: 12.11, avg_buy_price: 45.00, quantity: 100 },
                ],
                topThreeDown: [
                    { stock: { name: 'Intel Corp' }, profit_loss: -550.00, profit_loss_percent: -18.25, avg_buy_price: 40.00, quantity: 30 },
                    { stock: { name: 'Bayer AG' }, profit_loss: -300.90, profit_loss_percent: -8.90, avg_buy_price: 60.00, quantity: 20 },
                    { stock: { name: 'AT&T Inc.' }, profit_loss: -150.00, profit_loss_percent: -5.00, avg_buy_price: 25.00, quantity: 60 },
                ]
            },
            nextDividends: [
                { name: 'Coca-Cola Co', next_dividend: '2025-01-15', dividend: 50.00, percent: 1.10 },
                { name: 'Apple Inc.', next_dividend: '2025-02-10', dividend: 75.00, percent: 0.85 },
                { name: 'Microsoft Corp', next_dividend: '2025-02-18', dividend: 90.00, percent: 0.70 },
                { name: 'Bayer AG', next_dividend: '2025-03-20', dividend: 30.00, percent: 1.50 },
                { name: 'AT&T Inc.', next_dividend: '2025-04-05', dividend: 45.00, percent: 1.25 },
            ],
            portfolioStats: {
                totalUniqueStocks: 15,
                totalQuantity: 500,
                totalCurrentValue: 140000.00,
                totalDividendAmount: 416.67
            },
            lastTransactions: [
                { type: 'KAUF', stock: 'MSFT', amount: 900.00, date: '2024-06-01' },
                { type: 'VERKAUF', stock: 'INTC', amount: 550.00, date: '2024-05-28' },
                { type: 'DIVIDENDE', stock: 'JNJ', amount: 130.50, date: '2024-05-15' },
                { type: 'KAUF', stock: 'AAPL', amount: 1200.00, date: '2024-05-01' },
                { type: 'EINZAHLUNG', stock: 'CASH', amount: 5000.00, date: '2024-04-20' },
            ]
        };

        // Utility function to format numbers like PHP's number_format
        const numberFormat = (number, decimals = 2, decPoint = ',', thousandsSep = '.') => {
            const num = (number * 1).toFixed(decimals);
            const parts = num.split('.');
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousandsSep);
            return parts.join(decPoint);
        };

        // Utility function to determine color classes
        const getPerformanceColor = (value) => {
            return value >= 0 ? 'text-green-500' : 'text-red-500';
        };

        const initDashboard = () => {
            const info = MOCK_DEPOT_INFO;

            // --- 1. KPI Cards ---
            document.querySelector('#total-value-card div:last-child').innerHTML =
                `${numberFormat(info.totalPortfolioValue)} €`;

            // Performance 3M
            const perf3M = info.monthly_performance['3_month'].percent;
            const color3M = getPerformanceColor(perf3M);
            const sign3M = perf3M >= 0 ? '+' : '';
            document.querySelector('#perf-3m-card div:last-child').className = `text-4xl font-extrabold ${color3M} mt-2`;
            document.querySelector('#perf-3m-card div:last-child').innerHTML =
                `${sign3M}${numberFormat(perf3M)} %`;

            // Performance 6M
            const perf6M = info.monthly_performance['6_month'].percent;
            const color6M = getPerformanceColor(perf6M);
            const sign6M = perf6M >= 0 ? '+' : '';
            document.querySelector('#perf-6m-card div:last-child').className = `text-4xl font-extrabold ${color6M} mt-2`;
            document.querySelector('#perf-6m-card div:last-child').innerHTML =
                `${sign6M}${numberFormat(perf6M)} %`;

            // Avg. Dividend Yield
            document.querySelector('#avg-dividend-card div:last-child').innerHTML =
                `${numberFormat(info.averages.avg_dividend_percent_total)} %`;

            // --- 2. Historical Chart ---
            new Chart(
                document.getElementById('historicalChart'), {
                    type: 'line',
                    data: info.chartData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        aspectRatio: 3,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: { beginAtZero: false }
                        }
                    }
                }
            );

            // --- 3. Benchmark & Risk Metrics ---

            // Benchmark
            const portfPerf = info.monthly_performance['6_month'].percent;
            const benchPerf = info.monthly_performance.benchmark_ytd_percent;
            const benchName = info.monthly_performance.benchmark_name;
            const outperformance = portfPerf - benchPerf;
            const outperfColor = getPerformanceColor(outperformance);
            const outperfSign = outperformance >= 0 ? '+' : '';
            const portfPerfColor = getPerformanceColor(portfPerf);
            const portfPerfSign = portfPerf >= 0 ? '+' : '';

            document.querySelector('#benchmark-data > div:nth-child(1) span:last-child').className = `font-bold ${portfPerfColor}`;
            document.querySelector('#benchmark-data > div:nth-child(1) span:last-child').textContent =
                `${portfPerfSign}${numberFormat(portfPerf)} %`;

            document.getElementById('benchmark-name-ytd').textContent = `${benchName} (YTD)`;
            document.getElementById('benchmark-perf').textContent = `${numberFormat(benchPerf)} %`;

            document.getElementById('outperformance-value').className = `font-extrabold ${outperfColor}`;
            document.getElementById('outperformance-value').textContent =
                `${outperfSign}${numberFormat(outperformance)} %`;

            // Risk Metrics: Investment Ratio
            const cash = info.risk_metrics.cash_balance;
            const capital = info.risk_metrics.total_capital;
            const cashPercent = capital > 0 ? (cash / capital) * 100 : 0;
            const investmentPercent = 100 - cashPercent;

            const investmentRatioEl = document.getElementById('investment-ratio').children;
            investmentRatioEl[1].textContent = `${numberFormat(investmentPercent, 1)} %`;
            investmentRatioEl[2].textContent = `Cash-Anteil: ${numberFormat(cashPercent, 1)} % (${numberFormat(cash, 0)} €)`;

            // Risk Metrics: Beta
            const beta = info.risk_metrics.portfolio_beta;
            const betaColor = beta >= 1.2 ? 'text-red-500' : (beta >= 1.0 ? 'text-yellow-500' : 'text-green-500');
            const betaText = beta > 1.05 ? 'Volatiler' : (beta < 0.95 ? 'Weniger Volatil' : 'Marktkonform');

            const betaEl = document.getElementById('portfolio-beta').children;
            betaEl[1].className = `font-bold text-2xl ${betaColor} `;
            betaEl[1].textContent = numberFormat(beta, 2);
            betaEl[2].textContent = `${betaText} (vs. ${benchName})`;

            // --- 4. Dividends ---

            // Dividend Chart
            const chartDataDividends = {
                labels: info.dividend_chart.labels,
                datasets: [{
                    label: 'Erwartete Dividende (€)',
                    data: info.dividend_chart.data,
                    backgroundColor: '#f59e0b', // Yellow-500
                }],
            };
            new Chart(
                document.getElementById('dividendChart'), {
                    type: 'bar',
                    data: chartDataDividends,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        aspectRatio: 3,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: { beginAtZero: true }
                        }
                    }
                }
            );

            // Purchasing Power
            const annualDiv = info.purchasing_power.annual_gross_dividend;
            const buyStock = info.purchasing_power.stock_name;
            const buyQty = info.purchasing_power.can_buy_quantity;

            const ppEl = document.getElementById('purchasing-power').children;
            ppEl[0].innerHTML = `Ihre erwarteten Bruttodividenden pro Jahr belaufen sich auf <span class="font-extrabold text-yellow-600 dark:text-yellow-400">${numberFormat(annualDiv)} €</span>.`;
            ppEl[1].innerHTML = `Damit könnten Sie aktuell **${numberFormat(buyQty, 2)}** Stück der Aktie **${buyStock}** nachkaufen.`;
            document.getElementById('purchasing-power-note').textContent = `*Basierend auf Bruttodividenden und aktuellem Preis der ${buyStock}-Aktie.`;


            // --- 5. Top Movers & Next Dividends ---

            // Render Top Movers
            const renderTopMovers = (listId, movers) => {
                const listEl = document.getElementById(listId);
                listEl.innerHTML = '';
                if (movers.length === 0) {
                    listEl.innerHTML = '<li class="py-3 text-gray-400">Keine Daten verfügbar.</li>';
                    return;
                }
                movers.forEach(item => {
                    const amount = item.profit_loss;
                    const percent = item.profit_loss_percent;
                    const sign = amount >= 0 ? '+' : '';
                    const color = getPerformanceColor(amount);

                    const html = `
                        <li class="py-3 group">
                            <div class="flex justify-between items-center">
                                <div class="font-semibold text-gray-900 dark:text-gray-100 group-hover:text-indigo-500">
                                    ${item.stock.name}</div>
                                <div class="font-bold ${color} text-sm">
                                    ${sign}${numberFormat(percent)} %
                                </div>
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 flex justify-between">
                                <span>P/L: <span class="${color}">${sign}${numberFormat(amount)} €</span></span>
                                <span>Kauf: ${numberFormat(item.avg_buy_price)} €</span>
                                <span>Menge: ${item.quantity}</span>
                            </div>
                        </li>`;
                    listEl.insertAdjacentHTML('beforeend', html);
                });
            };

            renderTopMovers('top-winners-list', info.tops.topThreeUp);
            renderTopMovers('top-losers-list', info.tops.topThreeDown);


            // Render Next Dividends
            const nextDivListEl = document.getElementById('next-dividends-list');
            nextDivListEl.innerHTML = '';
            info.nextDividends.sort((a, b) => new Date(a.next_dividend) - new Date(b.next_dividend)).forEach(dividend => {
                const nextDate = new Date(dividend.next_dividend);
                const isFuture = nextDate.getTime() > new Date().getTime();
                const dateFormatted = nextDate.toLocaleDateString('de-DE');
                const statusIcon = isFuture ? '🟢' : '⚪';
                const statusColor = isFuture ? 'text-green-600 dark:text-green-400' : 'text-gray-400 dark:text-gray-500';

                const html = `
                    <li class="py-3 flex justify-between items-center group">
                        <div class="flex-grow">
                            <span class="font-semibold text-gray-800 dark:text-gray-100 group-hover:text-indigo-500">${dividend.name}</span>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                <span class="font-bold text-yellow-500">${numberFormat(dividend.dividend)} €</span>
                                | Rendite: ${numberFormat(dividend.percent)} %
                            </div>
                        </div>
                        <div class="flex flex-col items-end">
                            <span class="text-xs font-medium ${statusColor} flex items-center">
                                ${statusIcon} ${dateFormatted}
                            </span>
                        </div>
                    </li>`;
                nextDivListEl.insertAdjacentHTML('beforeend', html);
            });


            // --- 6. Detailed Statistics & Transactions ---

            // Detailed Stats
            const stats = info.portfolioStats;
            const detailedStatsEl = document.getElementById('detailed-stats');
            detailedStatsEl.innerHTML = `
                <div class="flex flex-col pl-4" style="border-left: 4px solid #6366f1;">
                    <span class="text-gray-500 dark:text-gray-400 font-medium">Unique Aktienarten</span>
                    <span class="font-bold text-2xl text-gray-900 dark:text-gray-100">${stats.totalUniqueStocks}</span>
                </div>
                <div class="flex flex-col pl-4" style="border-left: 4px solid #6366f1;">
                    <span class="text-gray-500 dark:text-gray-400 font-medium">Gesamt gehaltene Menge</span>
                    <span class="font-bold text-2xl text-gray-900 dark:text-gray-100">${stats.totalQuantity}</span>
                </div>
                <div class="flex flex-col pl-4" style="border-left: 4px solid #eab308;">
                    <span class="text-gray-500 dark:text-gray-400 font-medium" title="Jährliche Dividenden-Einnahmen">Gesamt-Dividenden (p.M.)</span>
                    <span class="font-bold text-2xl text-yellow-700 dark:text-yellow-400">${numberFormat(stats.totalDividendAmount)} €</span>
                </div>
                <div class="flex flex-col pl-4" style="border-left: 4px solid #6366f1;">
                    <span class="text-gray-500 dark:text-gray-400 font-medium">Aktueller Wert (Bestand)</span>
                    <span class="font-bold text-2xl text-gray-900 dark:text-gray-100">${numberFormat(stats.totalCurrentValue)} €</span>
                </div>
                <div class="flex flex-col pl-4" style="border-left: 4px solid #6b7280;">
                    <span class="text-gray-500 dark:text-gray-400 font-medium">Avg. Kaufpreis/Aktie</span>
                    <span class="font-bold text-2xl text-gray-900 dark:text-gray-100">${numberFormat(info.averages.avg_stock_price_eur)} €</span>
                </div>
                <div class="flex flex-col pl-4" style="border-left: 4px solid #6b7280;">
                    <span class="text-gray-500 dark:text-gray-400 font-medium">Avg. Dividende/Aktie</span>
                    <span class="font-bold text-2xl text-gray-900 dark:text-gray-100">${numberFormat(info.averages.avg_dividend_amount_eur)} €</span>
                </div>
            `;

            // Last 5 Transactions
            const transactionListEl = document.getElementById('transaction-list');
            transactionListEl.innerHTML = '';

            const getTransactionTypeClass = (type) => {
                switch (type) {
                    case 'KAUF': return 'text-red-500 bg-red-100 dark:bg-red-900';
                    case 'VERKAUF': return 'text-green-500 bg-green-100 dark:bg-green-900';
                    case 'DIVIDENDE': return 'text-yellow-500 bg-yellow-100 dark:bg-yellow-900';
                    case 'EINZAHLUNG': return 'text-indigo-500 bg-indigo-100 dark:bg-indigo-900';
                    default: return 'text-gray-500 bg-gray-100 dark:bg-gray-700';
                }
            };

            info.lastTransactions.forEach(t => {
                const date = new Date(t.date).toLocaleDateString('de-DE');
                const typeClass = getTransactionTypeClass(t.type);
                const sign = t.type === 'VERKAUF' || t.type === 'DIVIDENDE' || t.type === 'EINZAHLUNG' ? '+' : '-';
                const displayAmount = t.type === 'KAUF' ? `-${numberFormat(t.amount)}` : `${sign}${numberFormat(t.amount)}`;
                const stockName = t.stock === 'CASH' ? 'Konto' : t.stock;

                const html = `
                    <li class="py-3 flex justify-between items-center group">
                        <div>
                            <span class="font-semibold text-gray-900 dark:text-gray-100 group-hover:text-indigo-500">${stockName}</span>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium ${typeClass}">${t.type}</span>
                            </div>
                        </div>
                        <div class="flex flex-col items-end">
                            <span class="font-bold text-sm ${t.type === 'KAUF' || t.type === 'VERKAUF' ? getPerformanceColor(t.type === 'VERKAUF' ? 1 : -1) : 'text-gray-900 dark:text-gray-100'}">${displayAmount} €</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400 mt-1">${date}</span>
                        </div>
                    </li>
                `;
                transactionListEl.insertAdjacentHTML('beforeend', html);
            });
        };

        window.onload = initDashboard;
    </script>
</body>
</html>