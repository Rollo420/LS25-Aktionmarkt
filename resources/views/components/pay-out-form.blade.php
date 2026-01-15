<form method="POST" action="{{route('payment.payout')}}" class="mt-4 space-y-4">
    @csrf
    <div class="space-y-2">
        <label class="block text-sm font-medium text-gray-200">Betrag auszahlen</label>
        <input type="text" id="payout-input" name="payout" class="w-full rounded-lg p-3 bg-gray-900 text-white text-lg placeholder-gray-400 focus:ring-2 focus:ring-red-500 focus:border-transparent" placeholder="Betrag" required>
        <p class="text-gray-400 text-xs">Maximaler Betrag: 4.294.967.295</p>
    </div>
    @error('payout')
        <p class="text-red-500 text-sm">{{ $message }}</p>
    @enderror
    <button type="submit" class="w-full sm:w-auto bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg px-6 py-3 text-lg transition-colors duration-200">Auszahlen</button>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const payoutInput = document.getElementById('payout-input');
    const form = payoutInput.closest('form');

    form.addEventListener('submit', function(e) {
        let value = payoutInput.value.replace(/[,.]/g, '');
        if (!/^\d+$/.test(value)) {
            e.preventDefault();
            alert('Bitte geben Sie eine gültige Zahl ein.');
            return;
        }
        payoutInput.value = value;
    });
});
</script>
