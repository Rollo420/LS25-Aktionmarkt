@props(['stock'])

<div x-data="{ open: false }">
    <!-- Buy/Sell Button -->
    <button @click="open = true" class="relative inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold rounded-lg shadow-lg transform transition-all duration-200 hover:scale-105 focus:outline-none focus:ring-4 focus:ring-blue-300">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
        </svg>
        Kaufen/Verkaufen
    </button>

    <!-- Enhanced Modal (wie Delete Modal Design) -->
    <div x-show="open" class="fixed inset-0 flex items-center justify-center z-50 transition-opacity" x-transition.opacity style="background: rgba(30,41,59,0.25); display: none;">
        <div @click.away="open = false" class="bg-white dark:bg-gray-800 rounded-lg shadow-2xl w-96 mx-2 transform transition-all p-6 border border-blue-200 dark:border-blue-700" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Transaktion für {{ $stock->name }}</h3>
                <button @click="open = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form method="post" action="{{ route('payment.SellBuy', ['id' => $stock->id]) }}">
                @csrf
                <!-- Stückzahl Input -->
                <div class="mb-4">
                    <label for="quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Stückzahl:</label>
                    <input type="number" min="1" name="quantity" id="quantity" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white text-base transition-colors" required placeholder="Anzahl eingeben">
                </div>

                <!-- Preis Input -->
                <div class="mb-6">
                    <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Preis pro Stück:</label>
                    <input type="number" min="0.01" step="0.01" name="price" id="price" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white text-base transition-colors" required placeholder="Preis eingeben">
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col gap-3">
                    <button type="submit" name="buy" value="buy" class="w-full inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-semibold rounded-lg shadow-lg transform transition-all duration-200 hover:scale-105 focus:outline-none focus:ring-4 focus:ring-green-300">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Kaufen
                    </button>
                    <button type="submit" name="sell" value="sell" class="w-full inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-semibold rounded-lg shadow-lg transform transition-all duration-200 hover:scale-105 focus:outline-none focus:ring-4 focus:ring-red-300">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                        </svg>
                        Verkaufen
                    </button>
                    <button type="button" @click="open = false" class="w-full px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-white font-semibold rounded-lg shadow-lg transform transition-all duration-200 hover:scale-105 focus:outline-none focus:ring-4 focus:ring-gray-300">
                        Abbrechen
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
