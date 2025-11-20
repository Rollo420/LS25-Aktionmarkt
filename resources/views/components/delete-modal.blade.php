@props(['formAction', 'title' => 'Wirklich löschen?', 'description' => 'Diese Aktion kann nicht rückgängig gemacht werden.'])

<div x-data="{ open: false }">
    <button @click="open = true" type="button" {{ $attributes->merge(['class' => 'inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500']) }}>
        {{ $slot }}
    </button>

    <!-- Modal Backdrop -->
    <div x-show="open" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50" @click.away="open = false" style="display: none;">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-4">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">
                    {{ $title }}
                </h3>
                <p class="text-gray-500 dark:text-gray-400 text-sm mb-6">
                    {{ $description }}
                </p>
                
                <div class="flex gap-3 justify-end">
                    <button @click="open = false" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 dark:bg-gray-600 dark:text-gray-100 dark:hover:bg-gray-500">
                        {{ __('Abbrechen') }}
                    </button>
                    <form method="POST" action="{{ $formAction }}" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                            {{ __('Ja, löschen') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
