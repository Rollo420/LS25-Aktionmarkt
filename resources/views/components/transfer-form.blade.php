<form method="POST" action="{{ route('payment.transfer') }}" class="mt-4 space-y-4">
    @csrf
    <div class="space-y-2">
        <label class="block text-sm font-medium text-gray-200">Auf Konto</label>
        <input type="string" name="to_account" class="w-full rounded-lg p-3 bg-gray-900 text-white text-lg placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="DE61 12345678 2848820727" placeholder="DE61 12345678 2848820727" required>
        @error('to_account')
            <p class="text-red-500 text-sm">{{ $message }}</p>
        @enderror
    </div>

    <div class="space-y-2">
        <label class="block text-sm font-medium text-gray-200">Betrag</label>
        <input type="text" id="transfer-amount" name="amount" class="w-full rounded-lg p-3 bg-gray-900 text-white text-lg placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Betrag" required>
        <p class="text-gray-400 text-xs">Maximaler Betrag: 4.294.967.295</p>
        @error('amount')
            <p class="text-red-500 text-sm">{{ $message }}</p>
        @enderror
    </div>
    
    <button type="submit" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg px-6 py-3 text-lg transition-colors duration-200">Transferieren</button>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const transferAmountInput = document.getElementById('transfer-amount');
    const form = transferAmountInput.closest('form');

    form.addEventListener('submit', function(e) {
        let value = transferAmountInput.value.replace(/[,.]/g, '');
        if (!/^\d+$/.test(value)) {
            e.preventDefault();
            alert('Bitte geben Sie eine gültige Zahl ein.');
            return;
        }
        transferAmountInput.value = value;
    });
});
</script>
