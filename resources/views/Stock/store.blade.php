<head>
    <link rel="stylesheet" href="scss/app.scss">
</head>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Chart') }}
        </h2>
    </x-slot>

    <!--
        Detailansicht einer Aktie:
        - Zeigt das Kurs-Chart (Daten und Optionen aus Controller)
        - Zeigt Firmen- und Aktien-Kennzahlen (über Komponenten)
        - Nutzt eigene Komponenten für Chart, Timeline und Details
    -->
    <div class="py-12">
        @foreach (['success', 'error', 'warning', 'info'] as $msg)
            @if(session($msg))
                <div class="mb-6 p-4 rounded-lg {{ $msg === 'success' ? 'bg-green-50 text-green-800 border border-green-200' : ($msg === 'error' ? 'bg-red-50 text-red-800 border border-red-200' : ($msg === 'warning' ? 'bg-yellow-50 text-yellow-800 border border-yellow-200' : 'bg-blue-50 text-blue-800 border border-blue-200')) }} dark:bg-gray-800 dark:text-gray-100">
                    {{ session($msg) }}
                </div>
            @endif
        @endforeach
        <div class="max-w-7xl mx-auto">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h2 id="chartName">{{ $chartData['datasets'][0]['label'] }}</h2>

                    <div class="chart-timeline-grid">
                        <!-- Chart-Komponente für Kursverlauf -->
                        <x-chart-show 
                            id="chart"
                            type="line"
                            :data="$chartData"
                            :options="$chartOptions"
                        />
                        <!-- Timeline-Komponente für Zeitsteuerung -->
                        <x-chart-timeline/>
                    </div>

                    <!-- Buy and Sell Buttons-->
                    <x-buy_sell-buttons :stock="$stock" />

                    <!-- Firmen-Details Komponente -->
                    <x-firmen-details :firmenDetails="$stock" />

                    <div class="placeholder"></div>
                    
                    <section>
                        <!-- Aktien-Kennzahlen Komponente -->
                        <x-stock-details :stockDetails="$details" />
                    </section>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>