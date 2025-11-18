<div class="space-y-6">
    <form method="POST" class="mt-4 flex flex-col gap-2">
        @csrf
        <label class="text-gray-200">To Account</label>
        <input type="string" name="to_account" class="rounded p-2 bg-gray-900 text-white" value="DE61 12345678 2848820727" placeholder="DE61 12345678 2848820727" required>
        @error('to_account')
            <p class="text-red-500 text-sm">{{ $message }}</p>
        @enderror

        <label class="text-gray-200">Amount</label>
        <input type="number" name="amount" step="0.10" min="1" max="4294967295" class="rounded p-2 bg-gray-900 text-white" placeholder="Amount" required>
        <p class="text-gray-400 text-xs">Maximaler Betrag: 4.294.967.295</p>
        @error('amount')
            <p class="text-red-500 text-sm">{{ $message }}</p>
        @enderror
        <button type="submit" class="bg-blue-600 text-white rounded px-4 py-2 self-end">Transferieren</button>
    </form>
</div>
