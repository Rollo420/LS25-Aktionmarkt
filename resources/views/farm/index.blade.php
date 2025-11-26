<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Freunde einladen') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <!-- Einladung senden -->
                    <h3 class="text-lg font-semibold mb-4">Benutzer einladen</h3>

                    <form method="POST" action="{{ route('farm.send') }}" class="mb-10">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                            <!-- Email -->
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    E-Mail des Nutzers:
                                </label>
                                <input
                                    type="email"
                                    name="email"
                                    required
                                    class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md
                                           bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100
                                           focus:ring-indigo-500 focus:border-indigo-500">
                            </div>

                            <!-- Nachricht -->
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Nachricht (optional):
                                </label>
                                <input
                                    type="text"
                                    name="message"
                                    class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md
                                           bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100
                                           focus:ring-indigo-500 focus:border-indigo-500">
                            </div>

                        </div>

                        <button type="submit"
                            class="mt-5 inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700
                                   text-white font-semibold rounded-md shadow">
                            Einladung senden
                        </button>
                    </form>

                    <!-- Offene Einladungen -->
                    <h3 class="text-lg font-semibold mb-4">Offene Einladungen</h3>

                    <div class="overflow-x-auto mb-10">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                        Eingeladener Nutzer
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                        Eingeladen am
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                        Aktionen
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">

                                <!-- Beispiel -->
                                <tr>
                                    <td class="px-6 py-4">user@example.com</td>
                                    <td class="px-6 py-4">12.03.2025</td>
                                    <td class="px-6 py-4">

                                        <div class="flex gap-3">

                                            <form method="POST" action="{{ route('farm.accept', 1) }}">
                                                @csrf
                                                <button
                                                    class="px-4 py-2 bg-green-600 hover:bg-green-700
                                                           text-white rounded-md shadow">
                                                    Annehmen
                                                </button>
                                            </form>

                                            <form method="POST" action="{{ route('farm.decline', 1) }}">
                                                @csrf
                                                <button
                                                    class="px-4 py-2 bg-red-600 hover:bg-red-700
                                                           text-white rounded-md shadow">
                                                    Ablehnen
                                                </button>
                                            </form>

                                        </div>

                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>

                    <!-- Angenommene Einladungen -->
                    <h3 class="text-lg font-semibold mb-4">Angenommene Einladungen</h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                        Nutzer
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                        Angenommen am
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">

                                <tr>
                                    <td class="px-6 py-4">friend@example.com</td>
                                    <td class="px-6 py-4">15.03.2025</td>
                                </tr>

                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>