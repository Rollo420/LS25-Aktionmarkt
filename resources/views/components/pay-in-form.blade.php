<form method="POST" action="{{ route('payment.payin') }}" class="mt-4 flex flex-col gap-2">
    @csrf
    <label class="text-gray-200">{{ __('Betrag einzahlen') }}</label>
    <input type="text" id="payin-input" name="payin" class="rounded p-2 bg-gray-900 text-white" placeholder="{{ __('Betrag') }}" required>
    <p class="text-gray-400 text-xs">{{ __('Maximaler Betrag: 4.294.967.295') }}</p>
    @error('payin')
        <p class="text-red-500 text-sm">{{ $message }}</p>
    @enderror
    <button type="submit" class="bg-blue-600 text-white rounded px-4 py-2 self-end">{{ __('Einzahlen') }}</button>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const payinInput = document.getElementById('payin-input');
    const form = payinInput.closest('form');

    form.addEventListener('submit', function(e) {
        let value = payinInput.value.replace(/[,.]/g, '');
        if (!/^\d+$/.test(value)) {
            e.preventDefault();
            alert('{{ __("Bitte geben Sie eine gültige Zahl ein.") }}');
            return;
        }
        payinInput.value = value;
    });
});
</script>
