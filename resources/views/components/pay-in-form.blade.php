<form method="POST" action="{{ route('payment.payin') }}" class="mt-4 space-y-4">
    @csrf
    <div class="space-y-2">
        <label class="block text-sm font-medium text-gray-200">{{ __('Betrag einzahlen') }}</label>
        <input type="text" id="payin-input" name="payin" class="w-full rounded-lg p-3 bg-gray-900 text-white text-lg placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="{{ __('Betrag') }}" required>
        <p class="text-gray-400 text-xs">{{ __('Maximaler Betrag: 4.294.967.295') }}</p>
    </div>
    @error('payin')
        <p class="text-red-500 text-sm">{{ $message }}</p>
    @enderror
    <button type="submit" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg px-6 py-3 text-lg transition-colors duration-200">{{ __('Einzahlen') }}</button>
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
