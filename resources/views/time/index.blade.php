<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Time Manager') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto space-y-8">

            <!-- Aktuelle Zeit-Info -->
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 shadow-xl rounded-xl p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-2xl font-bold mb-2">Aktuelle Spielzeit</h3>
                        <p class="text-blue-100 text-lg">
                            <span class="font-semibold">{{ $currentGameTime }}</span>
                            <span class="text-sm ml-2">(LS25 Simulation)</span>
                        </p>
                        <p class="text-blue-200 text-sm mt-1">
                            Ausgewählter Monat: <span class="font-medium">{{ $selectedMonth }}</span>
                        </p>
                    </div>
                    <div class="text-right">
                        <div class="text-4xl mb-1">⏰</div>
                        <div class="text-sm text-blue-100">Zeitsteuerung aktiv</div>
                    </div>
                </div>
            </div>

            <!-- Monatsauswahl -->
            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-xl p-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-6 border-b pb-3 flex items-center">
                    <span class="text-indigo-500 mr-3">📅</span> Monat auswählen
                </h3>

                <form method="POST" action="{{ route('time.update') }}" class="space-y-6">
                    @csrf

                    <!-- Monats-Grid -->
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
                        @foreach ($monthArray as $index => $month)
                        <div class="relative">
                            <input type="radio" id="{{ $month }}" name="choose" value="{{ $month }}"
                                   {{ $selectedMonth == $month ? 'checked' : '' }}
                                   class="sr-only peer">

                            <label for="{{ $month }}"
                                   class="block p-4 bg-gray-50 dark:bg-gray-700 hover:bg-indigo-50 dark:hover:bg-indigo-900/20
                                          border-2 border-gray-200 dark:border-gray-600 hover:border-indigo-300 dark:hover:border-indigo-500
                                          peer-checked:bg-indigo-500 peer-checked:border-indigo-500 peer-checked:text-white
                                          rounded-lg cursor-pointer transition-all duration-200 text-center
                                          transform hover:scale-105 peer-checked:scale-105 peer-checked:shadow-lg">

                                <div class="text-sm font-medium mb-1">{{ $month }}</div>
                                <div class="text-xs opacity-70">{{ $index + 1 }}</div>
                            </label>
                        </div>
                        @endforeach
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-center pt-4">
                        <button type="submit"
                                class="px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700
                                       text-white font-semibold rounded-lg shadow-lg hover:shadow-xl
                                       transform hover:scale-105 transition-all duration-200 flex items-center space-x-2">
                            <span>⏭️</span>
                            <span>Zeit springen</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Info-Karten -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <!-- Zeit-Simulation Info -->
                <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl p-6">
                    <div class="flex items-center mb-4">
                        <div class="text-2xl mr-3">📊</div>
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Zeit-Simulation</h4>
                    </div>
                    <div class="space-y-3 text-sm text-gray-600 dark:text-gray-400">
                        <p>• Monatliche Preisgenerierung</p>
                        <p>• Saisonale Marktschwankungen</p>
                        <p>• Zufällige Marktbewegungen</p>
                        <p>• Crash/Rallye-Events</p>
                    </div>
                </div>

                <!-- Markt-Effekte -->
                <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl p-6">
                    <div class="flex items-center mb-4">
                        <div class="text-2xl mr-3">📈</div>
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Markt-Effekte</h4>
                    </div>
                    <div class="space-y-3 text-sm text-gray-600 dark:text-gray-400">
                        <p>• ±4% monatliche Volatilität</p>
                        <p>• Saisonale Trends</p>
                        <p>• Crash-Wahrscheinlichkeit: ~0.24%</p>
                        <p>• Rallye-Wahrscheinlichkeit: ~0.036%</p>
                    </div>
                </div>

                <!-- Hinweise -->
                <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl p-6">
                    <div class="flex items-center mb-4">
                        <div class="text-2xl mr-3">💡</div>
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Wichtige Hinweise</h4>
                    </div>
                    <div class="space-y-3 text-sm text-gray-600 dark:text-gray-400">
                        <p>• Zeit springt immer vorwärts</p>
                        <p>• Preise werden automatisch generiert</p>
                        <p>• Dividenden werden ausgeschüttet</p>
                        <p>• Transaktionen bleiben erhalten</p>
                    </div>
                </div>
            </div>

            <!-- Warnung -->
            <div class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 p-4 rounded-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <span class="text-yellow-400 text-xl">⚠️</span>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700 dark:text-yellow-300">
                            <strong>Wichtig:</strong> Das Springen in der Zeit ist irreversibel. Alle Marktpreise und Dividenden werden für die vergangenen Monate generiert.
                            Stellen Sie sicher, dass Sie den gewünschten Monat korrekt ausgewählt haben.
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
