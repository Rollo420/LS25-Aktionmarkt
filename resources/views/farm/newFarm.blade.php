<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Farm erstellen') }}
        </h2>
    </x-slot>

    <div class="max-w-3xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-6">

            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">{{ __('Erstelle eine neue Farm') }}</h1>

            @if ($errors->any())
            <div class="mb-6 p-4 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 rounded-lg">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{route('farm.sendNewFarm')}}" method="POST">
                @csrf

                {{-- Farm Name --}}
                <div class="mb-4">
                    <label for="name" class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">{{ __('Farm-Name') }}</label>
                    <input type="text" name="name" id="name" placeholder="{{ __('Meine tolle Farm') }}" value="{{ old('name') }}"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-green-500 dark:focus:ring-green-400 focus:outline-none bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                </div>

                {{-- Beschreibung --}}
                <div class="mb-4">
                    <label for="description" class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">{{ __('Beschreibung') }}</label>
                    <textarea name="description" id="description" rows="4" placeholder="{{ __('Beschreibe deine Farm...') }}"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-green-500 dark:focus:ring-green-400 focus:outline-none bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">{{ old('description') }}</textarea>
                </div>


                {{-- Submit Button --}}
                <div class="mt-6">
                    <button type="submit"
                        class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg shadow-md transition duration-300">
                        {{ __('Farm erstellen') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>