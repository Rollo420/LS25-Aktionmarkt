<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Stock bearbeiten') }} - {{ $stock->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto space-y-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-xl">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('admin.stocks.update', $stock) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Name</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $stock->name) }}" required
                                       class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-900 dark:text-gray-100">
                            </div>
                            <div>
                                <label for="firma" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Firma</label>
                                <input type="text" name="firma" id="firma" value="{{ old('firma', $stock->firma) }}" required
                                       class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-900 dark:text-gray-100">
                            </div>
                            <div>
                                <label for="sektor" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Sektor</label>
                                <select name="sektor" id="sektor" required class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-900 dark:text-gray-100">
                                    @foreach ($sectors as $sector)
                                        <option value="{{ $sector }}" {{ old('sektor', $stock->sektor) == $sector ? 'selected' : '' }}>{{ $sector }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="land" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Land</label>
                                <select name="land" id="land" required class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-900 dark:text-gray-100">
                                    @foreach ($countries as $country)
                                        <option value="{{ $country }}" {{ old('land', $stock->land) == $country ? 'selected' : '' }}>{{ $country }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="product_type_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Produkttyp</label>
                                <select name="product_type_id" id="product_type_id" required class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-900 dark:text-gray-100">
                                    @foreach ($productTypes as $type)
                                        <option value="{{ $type->id }}" {{ old('product_type_id', $stock->product_type_id) == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="config_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Config auswählen</label>
                                <select name="config_id" id="config_id" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-900 dark:text-gray-100">
                                    <option value="">{{ __('Config auswählen...') }}</option>
                                    @foreach ($configs as $config)
                                        @php $selectedConfig = old('config_id', $stock->config_id ?? null); @endphp
                                        <option value="{{ $config->id }}" {{ $selectedConfig == $config->id ? 'selected' : '' }}>{{ $config->name ?? 'Config ' . $config->id }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mt-6">
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Beschreibung</label>
                            <textarea name="description" id="description" rows="4" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-900 dark:text-gray-100">{{ old('description', $stock->description) }}</textarea>
                        </div>

                        <div class="flex items-center justify-between mt-6">
                            <div class="flex items-center gap-3" x-data="{ deleteOpen: false }">
                                <a href="{{ route('admin.time.index', ['stock' => $stock->id]) }}" class="inline-flex items-center px-3 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600">
                                    {{ __('Manage Time') }}
                                </a>
                                <button type="button" @click="deleteOpen = true" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                                    {{ __('Löschen') }}
                                </button>

                                <!-- Delete Modal -->
                                <div x-show="deleteOpen" class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 flex items-center justify-center z-50" style="display: none;">
                                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-4">
                                        <div class="p-6">
                                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">
                                                {{ __('Stock wirklich löschen?') }}
                                            </h3>
                                            <p class="text-gray-500 dark:text-gray-400 text-sm mb-6">
                                                {{ __('Diese Aktion kann nicht rückgängig gemacht werden.') }}
                                            </p>

                                            <div class="flex gap-3 justify-end">
                                                <button type="button" @click="deleteOpen = false" class="px-4 py-2 bg-gray-300 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-400 dark:hover:bg-gray-600">
                                                    {{ __('Abbrechen') }}
                                                </button>
                                                <button type="button" @click="document.getElementById('delete-stock-{{ $stock->id }}').submit()" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                                                    {{ __('Ja, löschen') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex gap-4">
                                <a href="{{ route('admin.stocks.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                    {{ __('Abbrechen') }}
                                </a>
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                    {{ __('Speichern') }}
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- External delete form (keeps markup valid, avoids nested forms) -->
                    <form id="delete-stock-{{ $stock->id }}" method="POST" action="{{ route('admin.stocks.destroy', $stock) }}" style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
