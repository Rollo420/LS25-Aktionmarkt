<form method="POST" action="{{route('payment.payout')}}" class="mt-4 flex flex-col gap-2">
    @csrf
    <label class="text-gray-200">Betrag auszahlen</label>
    <input type="number" name="payout" class="rounded p-2 bg-gray-900 text-white" placeholder="Betrag" min="1" max="4294967295" required>
    <p class="text-gray-400 text-xs">Maximaler Betrag: 4.294.967.295</p>
    @error('payout')
        <p class="text-red-500 text-sm">{{ $message }}</p>
    @enderror
    <button type="submit" class="bg-red-600 text-white rounded px-4 py-2 self-end">Auszahlen</button>
</form>
