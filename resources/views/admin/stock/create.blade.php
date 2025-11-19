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
                            <div class="mb-6 p-4 rounded-lg {{ $msg === 'success' ? 'bg-green-50 text-green-800 border border-green-200' : ($msg === 'error' ? 'bg-red-50 text-red-800 border border-red-200' : ($msg === 'warning' ? 'bg-yellow-50 text-yellow-800 border border-yellow-200' : 'bg-blue-50 text-blue-800 border border-blue-200')) }} dark:bg-gray-800 dark:text-gray-100">
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

                        <!-- Config Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Config auswählen') }}
                            </label>
                            <div class="flex space-x-2 mb-4">
                                <div class="relative flex-1">
                                    <select id="config_select" class="block w-full px-3 py-2 pr-10 border border-gray-300 dark:border-gray-700 rounded-l-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-900 dark:text-gray-100 appearance-none">
                                        <option value="">{{ __('Config auswählen...') }}</option>
                                      
                                        @foreach($configs as $config)
                                            <option value="{{ $config['id'] }}" data-description="{{ $config['description'] }}" data-volatility="{{ $config['volatility_range'] }}" data-seasonal="{{ $config['seasonal_effect_strength'] }}" data-crash-prob="{{ $config['crash_probability_monthly'] }}" data-crash-int="{{ $config['crash_interval_months'] }}" data-rally-prob="{{ $config['rally_probability_monthly'] }}" data-rally-int="{{ $config['rally_interval_months'] }}">
                                                {{ $config['name'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>
                                </div>
                                <button type="button" id="new_config_btn" class="px-4 py-2 bg-blue-600 text-white rounded-r-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 whitespace-nowrap">
                                    + Neue Config
                                </button>
                            </div>
                            <div id="selected_config" class="hidden p-4 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-lg border border-blue-200 dark:border-blue-800 shadow-sm">
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0">
                                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-blue-900 dark:text-blue-100 mb-2" id="config_title">Ausgewählte Config</h4>
                                        <div id="config_details" class="text-sm text-blue-800 dark:text-blue-200 space-y-1">
                                            <!-- Config details will be shown here -->
                                        </div>
                                    </div>
                                </div>
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

    <!-- Config Creation Modal -->
    <div id="config_modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Neue Config erstellen</h3>
                    <button id="close_modal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form id="config_form" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="config_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name</label>
                            <input type="text" id="config_name" name="name" required class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-900 dark:text-gray-100">
                        </div>
                        <div>
                            <label for="config_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Beschreibung</label>
                            <input type="text" id="config_description" name="description" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-900 dark:text-gray-100">
                        </div>
                        <div>
                            <label for="config_volatility" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Volatilität</label>
                            <input type="number" step="0.001" id="config_volatility" name="volatility_range" value="0.04" min="0" max="1" required class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-900 dark:text-gray-100">
                        </div>
                        <div>
                            <label for="config_seasonal" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Saisonale Stärke</label>
                            <input type="number" step="0.001" id="config_seasonal" name="seasonal_effect_strength" value="0.026" min="0" max="1" required class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-900 dark:text-gray-100">
                        </div>
                        <div>
                            <label for="config_crash_prob" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Crash Wahrscheinlichkeit</label>
                            <input type="number" step="0.1" id="config_crash_prob" name="crash_probability_monthly" value="1" min="0" required class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-900 dark:text-gray-100">
                        </div>
                        <div>
                            <label for="config_crash_int" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Crash Intervall (Monate)</label>
                            <input type="number" id="config_crash_int" name="crash_interval_months" value="240" min="1" required class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-900 dark:text-gray-100">
                        </div>
                        <div>
                            <label for="config_rally_prob" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Rally Wahrscheinlichkeit</label>
                            <input type="number" step="0.1" id="config_rally_prob" name="rally_probability_monthly" value="1" min="0" required class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-900 dark:text-gray-100">
                        </div>
                        <div>
                            <label for="config_rally_int" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Rally Intervall (Monate)</label>
                            <input type="number" id="config_rally_int" name="rally_interval_months" value="360" min="1" required class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-900 dark:text-gray-100">
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-6 space-x-3">
                        <button type="button" id="cancel_config" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                            Abbrechen
                        </button>
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                            Config erstellen
                        </button>
                    </div>
                </form>
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

        // Config Selection
        const configSelect = document.getElementById('config_select');
        const selectedConfigDiv = document.getElementById('selected_config');
        const configDetailsDiv = document.getElementById('config_details');
        const configTitleDiv = document.getElementById('config_title');

        configSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (!selectedOption.value) {
                selectedConfigDiv.classList.add('hidden');
                return;
            }

            const name = selectedOption.textContent.trim();
            const description = selectedOption.getAttribute('data-description');
            const volatility = selectedOption.getAttribute('data-volatility');
            const seasonal = selectedOption.getAttribute('data-seasonal');
            const crashProb = selectedOption.getAttribute('data-crash-prob');
            const crashInt = selectedOption.getAttribute('data-crash-int');
            const rallyProb = selectedOption.getAttribute('data-rally-prob');
            const rallyInt = selectedOption.getAttribute('data-rally-int');

            configTitleDiv.textContent = name;
            configDetailsDiv.innerHTML = `
                <div class="mb-2"><strong>${description}</strong></div>
                <div class="grid grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="font-medium">Volatilität:</span> ${volatility}
                    </div>
                    <div>
                        <span class="font-medium">Saisonale Stärke:</span> ${seasonal}
                    </div>
                    <div>
                        <span class="font-medium">Crash Wahrscheinlichkeit:</span> ${crashProb}%
                    </div>
                    <div>
                        <span class="font-medium">Crash Intervall:</span> ${crashInt} Monate
                    </div>
                    <div>
                        <span class="font-medium">Rally Wahrscheinlichkeit:</span> ${rallyProb}%
                    </div>
                    <div>
                        <span class="font-medium">Rally Intervall:</span> ${rallyInt} Monate
                    </div>
                </div>
            `;

            selectedConfigDiv.classList.remove('hidden');

            // Update hidden input
            updateHiddenConfigInput(selectedOption.value);
        });

        function updateHiddenConfigInput(configId) {
            // Remove existing hidden input
            const existingInput = document.querySelector('input[name="config_id"]');
            if (existingInput) {
                existingInput.remove();
            }

            // Add new hidden input
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'config_id';
            hiddenInput.value = configId;
            document.querySelector('form').appendChild(hiddenInput);
        }

        // Modal functionality
        const newConfigBtn = document.getElementById('new_config_btn');
        const configModal = document.getElementById('config_modal');
        const closeModalBtn = document.getElementById('close_modal');
        const cancelConfigBtn = document.getElementById('cancel_config');
        const configForm = document.getElementById('config_form');

        newConfigBtn.addEventListener('click', function() {
            configModal.classList.remove('hidden');
        });

        closeModalBtn.addEventListener('click', function() {
            configModal.classList.add('hidden');
        });

        cancelConfigBtn.addEventListener('click', function() {
            configModal.classList.add('hidden');
        });

        configForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // Get form data
            const formData = new FormData(this);
            const configData = {
             //   id: Date.now(), // Simple ID generation
                name: formData.get('name'),
                description: formData.get('description'),
                volatility_range: parseFloat(formData.get('volatility_range')),
                seasonal_effect_strength: parseFloat(formData.get('seasonal_effect_strength')),
                crash_probability_monthly: parseFloat(formData.get('crash_probability_monthly')),
                crash_interval_months: parseInt(formData.get('crash_interval_months')),
                rally_probability_monthly: parseFloat(formData.get('rally_probability_monthly')),
                rally_interval_months: parseInt(formData.get('rally_interval_months'))
            };

            // Add to dropdown
            const option = document.createElement('option');
            option.value = configData.id;
            option.textContent = configData.name;
            option.setAttribute('data-description', configData.description);
            option.setAttribute('data-volatility', configData.volatility_range);
            option.setAttribute('data-seasonal', configData.seasonal_effect_strength);
            option.setAttribute('data-crash-prob', configData.crash_probability_monthly);
            option.setAttribute('data-crash-int', configData.crash_interval_months);
            option.setAttribute('data-rally-prob', configData.rally_probability_monthly);
            option.setAttribute('data-rally-int', configData.rally_interval_months);

            configSelect.appendChild(option);

            // Select the new config
            configSelect.value = configData.id;
            configSelect.dispatchEvent(new Event('change'));

            // Close modal and reset form
            configModal.classList.add('hidden');
            this.reset();
        });
    </script>
</x-app-layout>
