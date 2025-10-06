@props(['stock'])

<div x-data="{ open: false, action: '' }">
    <button @click="open = true; action = 'buy'" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Buy</button>
    <button @click="open = true; action = 'sell'" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Sell</button>

    <!-- Modal -->
    <div x-show="open" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
        <div class="bg-white dark:bg-gray-800 p-6 rounded shadow-lg w-80">
            <h3 class="text-lg font-bold mb-4" x-text="action === 'buy' ? 'Buy Stock' : 'Sell Stock'"></h3>
            <form method="post" :action="action === 'buy' ? '{{ route('payment.SellBuy', ['id' => $stock->id]) }}' : '{{ route('payment.SellBuy', ['id' => $stock->id]) }}'">
                @csrf
                <label for="quantity" class="block mb-2">Stückzahl:</label>
                <input type="number" min="1" name="quantity" id="quantity" class="w-full mb-4 p-2 border rounded" required>
                <div class="flex justify-between">
                    <button type="submit" name="buy" value="buy" x-show="action === 'buy'" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Buy</button>
                    <button type="submit" name="sell" value="sell" x-show="action === 'sell'" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Sell</button>
                    <button type="button" @click="open = false" class="ml-2 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">Abbrechen</button>
                </div>
            </form>
        </div>
    </div>
</div>