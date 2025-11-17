<form method="POST" action="{{ route('payment.payin') }}" class="mt-4 flex flex-col gap-2">
    @csrf
    <label class="text-gray-200">Betrag einzahlen</label>
    <input type="number" name="payin" class="rounded p-2 bg-gray-900 text-white" placeholder="Betrag" min="1" max="4294967295" required>
    @error('payin')
        <p class="text-red-500 text-sm">{{ $message }}</p>
    @enderror
    <button type="submit" class="bg-blue-600 text-white rounded px-4 py-2 self-end">Einzahlen</button>
</form>
