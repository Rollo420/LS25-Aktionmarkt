<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Neue Aktie erstellen') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto space-y-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-xl">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @foreach (['success', 'error', 'warning', 'info'] as $msg)
                        @if(session($msg))
                            <div :class="['p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg']" class="alert alert-{{ $msg }} mb-4">
                                {{ session($msg) }}
                            </div>
                        @endif
                    @endforeach

                    <form method="POST" action="{{ route('admin.stock.store') }}" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Product Type -->
                            <div>
                                <label for="product_type_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('Produkttyp') }}
                                </label>
                                <div class="flex">
                                    <select name="product_type_id" id="product_type_id" required
                                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-l-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-900 dark:text-gray-100">
                                        <option value="">{{ __('Bitte wählen') }}</option>
                                        @foreach($productTypes as $productType)
                                            <option value="{{ $productType->id }}" {{ old('product_type_id') == $productType->id ? 'selected' : '' }}>
                                                {{ $productType->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="button" onclick="generateField('product_type_id')"
                                            class="px-3 py-2 bg-blue-600 text-white rounded-r-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        🎲
                                    </button>
                                </div>
                                @error('product_type_id')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('Aktienname') }}
                                </label>
                                <div class="flex">
                                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                           class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-l-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-900 dark:text-gray-100">
                                    <button type="button" onclick="generateField('name')"
                                            class="px-3 py-2 bg-blue-600 text-white rounded-r-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        🎲
                                    </button>
                                </div>
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Firma -->
                            <div>
                                <label for="firma" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('Firma') }}
                                </label>
                                <div class="flex">
                                    <input type="text" name="firma" id="firma" value="{{ old('firma') }}" required
                                           class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-l-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-900 dark:text-gray-100">
                                    <button type="button" onclick="generateField('firma')"
                                            class="px-3 py-2 bg-blue-600 text-white rounded-r-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        🎲
                                    </button>
                                </div>
                                @error('firma')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Sektor -->
                            <div>
                                <label for="sektor" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('Sektor') }}
                                </label>
                                <div class="flex">
                                    <select name="sektor" id="sektor" required
                                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-l-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-900 dark:text-gray-100">
                                        <option value="">{{ __('Bitte wählen') }}</option>
                                        @foreach($sectors as $sector)
                                            <option value="{{ $sector }}" {{ old('sektor') == $sector ? 'selected' : '' }}>
                                                {{ $sector }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="button" onclick="generateField('sektor')"
                                            class="px-3 py-2 bg-blue-600 text-white rounded-r-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        🎲
                                    </button>
                                </div>
                                @error('sektor')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Land -->
                            <div>
                                <label for="land" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('Land') }}
                                </label>
                                <div class="flex">
                                    <select name="land" id="land" required
                                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-l-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-900 dark:text-gray-100">
                                        <option value="">{{ __('Bitte wählen') }}</option>
                                        @foreach($countries as $country)
                                            <option value="{{ $country }}" {{ old('land') == $country ? 'selected' : '' }}>
                                                {{ $country }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="button" onclick="generateField('land')"
                                            class="px-3 py-2 bg-blue-600 text-white rounded-r-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        🎲
                                    </button>
                                </div>
                                @error('land')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Net Income -->
                            <div>
                                <label for="net_income" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('Jahresüberschuss (€)') }}
                                </label>
                                <div class="flex">
                                    <input type="number" step="0.01" name="net_income" id="net_income" value="{{ old('net_income') }}" required
                                           class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-l-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-900 dark:text-gray-100">
                                    <button type="button" onclick="generateField('net_income')"
                                            class="px-3 py-2 bg-blue-600 text-white rounded-r-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        🎲
                                    </button>
                                </div>
                                @error('net_income')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Dividend Frequency -->
                            <div>
                                <label for="dividend_frequency" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('Dividendenausschüttungen pro Jahr') }}
                                </label>
                                <div class="flex">
                                    <select name="dividend_frequency" id="dividend_frequency" required
                                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-l-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-900 dark:text-gray-100">
                                        @for($i = 0; $i <= 12; $i++)
                                            <option value="{{ $i }}" {{ old('dividend_frequency', 4) == $i ? 'selected' : '' }}>
                                                {{ $i }}
                                            </option>
                                        @endfor
                                    </select>
                                    <button type="button" onclick="generateField('dividend_frequency')"
                                            class="px-3 py-2 bg-blue-600 text-white rounded-r-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        🎲
                                    </button>
                                </div>
                                @error('dividend_frequency')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Start Price -->
                            <div>
                                <label for="start_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('Startpreis (€)') }}
                                </label>
                                <div class="flex">
                                    <input type="number" step="0.01" name="start_price" id="start_price" value="{{ old('start_price') }}" required min="0"
                                           class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-l-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-900 dark:text-gray-100">
                                    <button type="button" onclick="generateField('start_price')"
                                            class="px-3 py-2 bg-blue-600 text-white rounded-r-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        🎲
                                    </button>
                                </div>
                                @error('start_price')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Dividend per Share -->
                            <div>
                                <label for="dividend_amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('Dividend per Share (€)') }}
                                </label>
                                <div class="flex">
                                    <input type="number" step="0.01" name="dividend_amount" id="dividend_amount" value="{{ old('dividend_amount') }}" min="0"
                                           class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-l-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-900 dark:text-gray-100">
                                    <button type="button" onclick="generateField('dividend_amount')"
                                            class="px-3 py-2 bg-blue-600 text-white rounded-r-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        🎲
                                    </button>
                                </div>
                                @error('dividend_amount')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Next Dividend Date -->
                            <div>
                                <label for="next_dividend_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('Nächstes Dividendendatum') }}
                                </label>
                                <div class="flex">
                                    <input type="date" name="next_dividend_date" id="next_dividend_date" value="{{ old('next_dividend_date') }}" min="{{ now()->addDay()->format('Y-m-d') }}"
                                           class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-l-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-900 dark:text-gray-100">
                                    <button type="button" onclick="generateField('next_dividend_date')"
                                            class="px-3 py-2 bg-blue-600 text-white rounded-r-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        🎲
                                    </button>
                                </div>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Das Datum muss in der Zukunft liegen (mindestens morgen).</p>
                                @error('next_dividend_date')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Beschreibung') }}
                            </label>
                            <div class="flex">
                                <textarea name="description" id="description" rows="3" required
                                          class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-l-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-900 dark:text-gray-100">{{ old('description') }}</textarea>
                                <button type="button" onclick="generateField('description')"
                                        class="px-3 py-2 bg-blue-600 text-white rounded-r-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    🎲
                                </button>
                            </div>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex items-center justify-end pt-4 space-x-4">
                            <button type="submit" name="generate_missing" value="0"
                                    class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                {{ __('Aktie erstellen') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function generateField(field) {
            fetch('{{ route("admin.generate-field") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ field: field })
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert('Fehler: ' + data.error);
                    return;
                }

                const element = document.getElementById(field);
                if (element.tagName === 'SELECT') {
                    // For select elements, set the value and trigger change
                    element.value = data.value;
                    // If display is provided (for product_type), we might need to handle it differently
                    if (data.display) {
                        // Find the option with the matching value and select it
                        const option = Array.from(element.options).find(opt => opt.value == data.value);
                        if (option) {
                            option.selected = true;
                        }
                    }
                } else {
                    // For input/textarea elements
                    element.value = data.value;
                }

                // Trigger change event to ensure any validation or styling updates
                element.dispatchEvent(new Event('change'));
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Ein Fehler ist aufgetreten beim Generieren der Daten.');
            });
        }
    </script>
</x-app-layout>
