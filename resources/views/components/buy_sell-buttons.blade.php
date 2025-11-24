@props(['stock'])

<div x-data="stockCalculator({{ $stock->getCurrentPrice() }})">
    <button @click="open = true"
            class="relative inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold rounded-lg shadow-lg transform transition-all duration-200 hover:scale-105">
        Kaufen/Verkaufen
    </button>

    <div x-show="open" class="fixed inset-0 flex items-center justify-center z-50 transition-opacity"
         x-transition.opacity
         style="background: rgba(30,41,59,0.25); display: none;">
        <div @click.away="open = false"
             class="bg-white dark:bg-gray-800 rounded-lg shadow-2xl w-96 mx-2 p-6 border border-blue-200 dark:border-blue-700"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">

            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Transaktion für {{ $stock->name }}</h3>
                <button @click="open = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                    ×
                </button>
            </div>

            <form method="POST" action="{{ route('payment.SellBuy', ['id' => $stock->id]) }}">
                @csrf

                <!-- Stückzahl -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Stückzahl:
                    </label>
                    <input type="number"
                           min="1"
                           name="quantity"
                           x-model="piecesInput"
                           @input="updateFromPieces()"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white transition-colors"
                           placeholder="Stückzahl eingeben">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1" x-text="infoPieces"></p>
                </div>

                <!-- Preis -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Kaufpreis (€):
                    </label>
                    <input type="number"
                           min="0.01"
                           step="0.01"
                           name="price"
                           x-model="priceInput"
                           @input="updateFromPrice()"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white transition-colors"
                           placeholder="Preis eingeben">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1" x-text="infoPrice"></p>
                </div>

                <div class="flex flex-col gap-3">
                    <button type="submit" name="buy" value="buy" class="w-full px-4 py-2 bg-green-500 text-white rounded-lg">
                        Kaufen
                    </button>
                    <button type="submit" name="sell" value="sell" class="w-full px-4 py-2 bg-red-500 text-white rounded-lg">
                        Verkaufen
                    </button>
                    <button type="button" @click="open = false" class="w-full px-4 py-2 bg-gray-200 rounded-lg">Abbrechen</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function stockCalculator(pricePerStock) {
    return {
        open: false,
        pricePerStock,

        // Zwei getrennte Inputs, um leeren zu erlauben
        piecesInput: 1,
        priceInput: pricePerStock,

        infoPieces: '',
        infoPrice: '',

        // Wenn Stückzahl geändert wird
        updateFromPieces() {
            if (!this.piecesInput || this.piecesInput < 1) this.piecesInput = 1;

            this.priceInput = (this.piecesInput * this.pricePerStock).toFixed(2);
            this.infoPieces = `${this.piecesInput} Stück kosten ${this.priceInput} €`;
            this.infoPrice = '';
        },

        // Wenn Preis geändert wird
        updateFromPrice() {
            if (!this.priceInput || this.priceInput < this.pricePerStock) {
                this.piecesInput = 1;
                this.infoPrice = '';
                return;
            }

            let st = Math.floor(this.priceInput / this.pricePerStock);
            let remainder = this.priceInput % this.pricePerStock;
            if (remainder === 0) this.piecesInput = st;

            let next = st + 1;
            let nextPrice = (next * this.pricePerStock).toFixed(2);

            this.infoPrice = `Nächste volle Aktie: ${next} Stück → benötigt: ${nextPrice} €`;
            this.infoPieces = '';
        }
    }
}
</script>
