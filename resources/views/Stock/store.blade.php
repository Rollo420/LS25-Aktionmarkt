<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Chart') }}
        </h2>
    </x-slot>

    <x-chart-show
        type="line"
        :data="$chartData"
        :options="$chartOptions" />

</x-app-layout>