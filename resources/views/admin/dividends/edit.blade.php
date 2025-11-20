<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dividend bearbeiten') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('admin.dividends.update', $dividend) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="stock_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Stock</label>
                                <select name="stock_id" id="stock_id" required class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md">
                                    @foreach ($stocks as $stock)
                                        <option value="{{ $stock->id }}" {{ old('stock_id', $dividend->stock_id) == $stock->id ? 'selected' : '' }}>{{ $stock->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="game_time_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">GameTime</label>
                                <select name="game_time_id" id="game_time_id" required class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md">
                                    @foreach ($gameTimes as $gameTime)
                                        <option value="{{ $gameTime->id }}" {{ old('game_time_id', $dividend->game_time_id) == $gameTime->id ? 'selected' : '' }}>{{ $gameTime->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="amount_per_share" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Betrag pro Aktie</label>
                                <input type="number" step="0.01" name="amount_per_share" id="amount_per_share" value="{{ old('amount_per_share', $dividend->amount_per_share) }}" required
                                       class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md">
                            </div>
                        </div>

                        <div class="flex items-center justify-between mt-6">
                            <form method="POST" action="{{ route('admin.dividends.destroy', $dividend) }}" onsubmit="return confirm('Wirklich löschen?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                                    {{ __('Löschen') }}
                                </button>
                            </form>
                            <div class="flex gap-4">
                                <a href="{{ route('admin.dividends.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                    {{ __('Abbrechen') }}
                                </a>
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                    {{ __('Speichern') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
