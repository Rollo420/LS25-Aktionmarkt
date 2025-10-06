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
                <div :class="['p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg']" class="alert alert-{{ $msg }} mb-4">
                    {{ session($msg) }}
                </div>
            @endif
        @endforeach
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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