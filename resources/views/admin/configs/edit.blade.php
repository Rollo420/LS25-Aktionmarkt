<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Config bearbeiten') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @php
                        $config = [
                            'id' => 1,
                            'name' => 'Standard Config',
                            'description' => 'Basis-Konfiguration für Aktien',
                            'volatility_range' => 0.04,
                            'seasonal_effect_strength' => 0.026,
                            'crash_probability_monthly' => 1,
                            'crash_interval_months' => 240,
                            'rally_probability_monthly' => 1,
                            'rally_interval_months' => 360,
                        ];
                    @endphp

                    <form method="POST" action="#">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('Name') }}
                                </label>
                                <input type="text" name="name" id="name" value="{{ old('name', $config['name']) }}" required
                                       class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-900 dark:text-gray-100">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('Beschreibung') }}
                                </label>
                                <textarea name="description" id="description" rows="3"
                                          class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-900 dark:text-gray-100">{{ old('description', $config['description']) }}</textarea>
                                @error('description')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Volatility Range -->
                            <div>
                                <label for="volatility_range" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('Volatilitätsbereich') }}
                                </label>
                                <input type="number" step="0.001" name="volatility_range" id="volatility_range" value="{{ old('volatility_range', $config['volatility_range']) }}" required min="0" max="1"
                                       class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-900 dark:text-gray-100">
                                @error('volatility_range')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Seasonal Effect Strength -->
                            <div>
                                <label for="seasonal_effect_strength" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('Saisonale Effektstärke') }}
                                </label>
                                <input type="number" step="0.001" name="seasonal_effect_strength" id="seasonal_effect_strength" value="{{ old('seasonal_effect_strength', $config['seasonal_effect_strength']) }}" required min="0" max="1"
                                       class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-900 dark:text-gray-100">
                                @error('seasonal_effect_strength')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Crash Probability Monthly -->
                            <div>
                                <label for="crash_probability_monthly" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('Crash Wahrscheinlichkeit (monatlich)') }}
                                </label>
                                <input type="number" step="0.1" name="crash_probability_monthly" id="crash_probability_monthly" value="{{ old('crash_probability_monthly', $config['crash_probability_monthly']) }}" required min="0"
                                       class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-900 dark:text-gray-100">
                                @error('crash_probability_monthly')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Crash Interval Months -->
                            <div>
                                <label for="crash_interval_months" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('Crash Intervall (Monate)') }}
                                </label>
                                <input type="number" name="crash_interval_months" id="crash_interval_months" value="{{ old('crash_interval_months', $config['crash_interval_months']) }}" required min="1"
                                       class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-900 dark:text-gray-100">
                                @error('crash_interval_months')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Rally Probability Monthly -->
                            <div>
                                <label for="rally_probability_monthly" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('Rally Wahrscheinlichkeit (monatlich)') }}
                                </label>
                                <input type="number" step="0.1" name="rally_probability_monthly" id="rally_probability_monthly" value="{{ old('rally_probability_monthly', $config['rally_probability_monthly']) }}" required min="0"
                                       class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-900 dark:text-gray-100">
                                @error('rally_probability_monthly')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Rally Interval Months -->
                            <div>
                                <label for="rally_interval_months" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('Rally Intervall (Monate)') }}
                                </label>
                                <input type="number" name="rally_interval_months" id="rally_interval_months" value="{{ old('rally_interval_months', $config['rally_interval_months']) }}" required min="1"
                                       class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-900 dark:text-gray-100">
                                @error('rally_interval_months')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('admin.configs.index') }}" class="mr-4 inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Abbrechen
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Config aktualisieren
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
