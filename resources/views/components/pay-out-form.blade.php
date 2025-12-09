<form method="POST" action="{{route('payment.payout')}}" class="mt-4 flex flex-col gap-2">
    @csrf
    <label class="text-gray-200">Betrag auszahlen</label>
    <input type="text" id="payout-input" name="payout" class="rounded p-2 bg-gray-900 text-white" placeholder="Betrag" required>
    <p class="text-gray-400 text-xs">Maximaler Betrag: 4.294.967.295</p>
    @error('payout')
        <p class="text-red-500 text-sm">{{ $message }}</p>
    @enderror
    <button type="submit" class="bg-red-600 text-white rounded px-4 py-2 self-end">Auszahlen</button>
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
