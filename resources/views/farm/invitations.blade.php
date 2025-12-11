<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Farm Einladungen
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-6">Offene Farm-Einladungen</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            @foreach($farms as $farm)
            {{-- Karte 1 --}}
            <div class="group bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900 dark:to-green-800 rounded-xl p-6 shadow-md hover:shadow-xl transition duration-300 ease-in-out border border-transparent hover:border-green-300 dark:hover:border-green-600">

                {{-- Farm Name --}}
                <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">
                    {{$farm['name']}}
                </h3>

                {{-- Beschreibung --}}
                <p class="text-gray-700 dark:text-gray-300 text-sm mb-6">
                    Du wurdest eingeladen, dieser Farm beizutreten. Akzeptiere die Einladung, um Mitglied zu werden.
                </p>

                {{-- Buttons --}}
                <form method="POST" action="{{ route('farm.accept') }}">
                    @csrf

                    <input type="hidden" name="farmID" value="{{$farm['id']}}">
                    <div class="flex space-x-3">
                        <button type="submit" name="acceptBTN" class="flex-1 bg-white dark:bg-gray-700 text-green-700 dark:text-green-300 font-semibold py-2 px-4 rounded-lg shadow hover:shadow-md hover:bg-green-50 dark:hover:bg-green-900 transition duration-300">
                            Annehmen
                        </button>
                        <button type="submit" name="declineBTN" class="flex-1 bg-white dark:bg-gray-700 text-red-600 dark:text-red-400 font-semibold py-2 px-4 rounded-lg shadow hover:shadow-md hover:bg-red-50 dark:hover:bg-red-900 transition duration-300">
                            Ablehnen
                        </button>
                    </div>
                </form>
            </div>
            @endforeach
        </div>
    </div>
</x-app-layout>