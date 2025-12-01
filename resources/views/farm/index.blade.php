<x-app-layout>
    <x-slot name="header">
        <h1 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Zahlungen & Transaktionen') }}
        </h1>
    </x-slot>
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900 py-10 px-6">

        <h1 class="text-4xl font-bold text-gray-800 dark:text-white mb-10 text-center">
            Hof einem Benutzer zuweisen
        </h1>

        <div class="max-w-3xl mx-auto bg-gray-100 dark:bg-gray-800 rounded-3xl p-10 
                shadow-[8px_8px_20px_#d1d1d1,-8px_-8px_20px_#ffffff0d] 
                dark:shadow-[8px_8px_20px_#0b0b0b,-8px_-8px_20px_#1a1a1a]">

            <!-- USER SEARCH -->
            <div class="mb-10">
                <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-3">
                    Benutzer auswählen
                </h2>

                {{-- WICHTIG: richtige Komponente --}}
                <x-search-input
                    api="{{ route('api.search.users') }}"
                    placeholder="Benutzer suchen..."
                    display="name" />
            </div>

            <!-- FARM NAME -->
            <div class="mb-10">
                <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-3">
                    Hof-Name
                </h2>

                <input
                    type="text"
                    name="farm_name"
                    placeholder="z. B. Sonnenhof"
                    class="w-full px-5 py-4 rounded-3xl bg-gray-100 dark:bg-gray-900 
                       shadow-inner dark:shadow-inner text-gray-700 dark:text-gray-200 
                       focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <!-- SUBMIT -->
            <button
                class="w-full py-4 rounded-3xl bg-indigo-500 hover:bg-indigo-600 text-white text-lg font-semibold 
                   shadow-[4px_4px_12px_#b1b1b1,-4px_-4px_12px_#ffffff70] 
                   dark:shadow-[4px_4px_12px_#0c0c0c,-4px_-4px_12px_#1a1a1a]
                   transition">
                Hof erstellen
            </button>
        </div>
    </div>
</x-app-layout>