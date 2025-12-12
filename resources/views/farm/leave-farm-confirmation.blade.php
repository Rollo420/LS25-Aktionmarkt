<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Farm verlassen') }}
        </h2>
    </x-slot>

    <div class="max-w-2xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-6">
            
            <div class="text-center mb-6">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900 mb-4">
                    <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>
                

                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                    {{ __('Farm verlassen') }}
                </h1>
                
                <p class="text-gray-600 dark:text-gray-400">
                    {{ __('Möchten Sie wirklich die Farm verlassen?') }}
                </p>
            </div>

            <!-- Farm Information -->
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">

                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
                    {{ __('Aktuelle Farm:') }}
                </h3>
                <div class="text-gray-700 dark:text-gray-300">
                    <p class="font-medium">{{ $currentFarm->name }}</p>
                    @if($currentFarm->farm_description)
                        <p class="text-sm mt-1">{{ $currentFarm->farm_description }}</p>
                    @endif
                </div>
            </div>

            <!-- Warning Message -->
            <div class="bg-yellow-50 dark:bg-yellow-900 border border-yellow-200 dark:border-yellow-700 rounded-lg p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400 dark:text-yellow-300" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">

                        <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                            {{ __('Wichtiger Hinweis') }}
                        </h3>
                        <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                            <p>{{ __('Nach dem Verlassen der Farm:') }}</p>
                            <ul class="list-disc list-inside mt-1 space-y-1">
                                <li>{{ __('Sie verlieren alle Farm-spezifischen Daten') }}</li>
                                <li>{{ __('Der Farm-Modus wird deaktiviert') }}</li>
                                <li>{{ __('Sie können nur durch eine neue Einladung wieder beitreten') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Confirmation Form -->
            <form action="{{ route('farm.leave') }}" method="POST" class="space-y-4">
                @csrf
                
                <div class="flex flex-col sm:flex-row gap-4">
                    <!-- Cancel Button -->

                    <a href="{{ route('farm.index') }}" 
                       class="flex-1 inline-flex justify-center items-center px-4 py-3 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 dark:focus:ring-green-400 transition duration-300">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        {{ __('Abbrechen') }}
                    </a>

                    <!-- Leave Button -->
                    <button type="submit" 
                            class="flex-1 inline-flex justify-center items-center px-4 py-3 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:focus:ring-red-400 transition duration-300"
                            onclick="return confirm('{{ __('Sind Sie absolut sicher, dass Sie diese Farm verlassen möchten? Diese Aktion kann nicht rückgängig gemacht werden.') }}')">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        {{ __('Farm verlassen') }}
                    </button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>
