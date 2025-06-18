<form method="POST" action="{{route('payment.payout')}}" class="mt-4 flex flex-col gap-2">
    @csrf
    <label class="text-gray-200">Betrag auszahlen</label>
    <input type="number" name="payout" class="rounded p-2 bg-gray-900 text-white" placeholder="Betrag">
    <button type="submit" class="bg-red-600 text-white rounded px-4 py-2 self-end">Auszahlen</button>
</form>
