<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Configs verwalten') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if(session('success'))
                        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded !important">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="mb-6">
                        <a href="{{ route('admin.configs.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 active:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Neue Config erstellen
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Beschreibung</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Volatilität</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Saisonale Stärke</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Crash Wahrscheinlichkeit</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Rally Wahrscheinlichkeit</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aktionen</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @php
                                    $configs = [
                                        [
                                            'id' => 1,
                                            'name' => 'Standard Config',
                                            'description' => 'Basis-Konfiguration für Aktien',
                                            'volatility_range' => 0.04,
                                            'seasonal_effect_strength' => 0.026,
                                            'crash_probability_monthly' => 1,
                                            'crash_interval_months' => 240,
                                            'rally_probability_monthly' => 1,
                                            'rally_interval_months' => 360,
                                        ],
                                        [
                                            'id' => 2,
                                            'name' => 'Volatile Config',
                                            'description' => 'Hohe Volatilität für dynamische Märkte',
                                            'volatility_range' => 0.08,
                                            'seasonal_effect_strength' => 0.052,
                                            'crash_probability_monthly' => 0.8,
                                            'crash_interval_months' => 120,
                                            'rally_probability_monthly' => 0.9,
                                            'rally_interval_months' => 180,
                                        ],
                                        [
                                            'id' => 3,
                                            'name' => 'Stable Config',
                                            'description' => 'Niedrige Volatilität für stabile Aktien',
                                            'volatility_range' => 0.02,
                                            'seasonal_effect_strength' => 0.013,
                                            'crash_probability_monthly' => 1.2,
                                            'crash_interval_months' => 480,
                                            'rally_probability_monthly' => 1.1,
                                            'rally_interval_months' => 720,
                                        ],
                                        [
                                            'id' => 4,
                                            'name' => 'Growth Config',
                                            'description' => 'Fokus auf Wachstum und Rallys',
                                            'volatility_range' => 0.06,
                                            'seasonal_effect_strength' => 0.039,
                                            'crash_probability_monthly' => 1.5,
                                            'crash_interval_months' => 360,
                                            'rally_probability_monthly' => 0.7,
                                            'rally_interval_months' => 240,
                                        ],
                                    ];
                                @endphp
                                @forelse($configs as $config)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $config['name'] }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300">
                                            {{ $config['description'] ?: 'Keine Beschreibung' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            {{ $config['volatility_range'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            {{ $config['seasonal_effect_strength'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            {{ $config['crash_probability_monthly'] * 100 }}%
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            {{ $config['rally_probability_monthly'] * 100 }}%
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('admin.configs.edit', $config['id']) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3">Bearbeiten</a>
                                            <form method="POST" action="#" class="inline" onsubmit="return confirm('Sind Sie sicher, dass Sie diese Config löschen möchten?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">Löschen</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                            Keine Configs gefunden.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
