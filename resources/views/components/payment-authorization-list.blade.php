@props(['payments'])

<div class="max-w-7xl mx-auto space-y-6">
        @foreach (['success', 'error', 'warning', 'info'] as $msg)
        @if(session($msg))
            <div :class="['p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg" class="alert alert-{{ $msg }}">
                {{ session($msg) }}
            </div>
        @endif
    @endforeach
<form method="POST" action="{{route('payment.handlePaymentApproval')}}">
    @csrf
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-xs sm:text-sm">
            <thead>
                <tr>
                    <th class="px-2 sm:px-6 py-3">ID</th>
                    <th class="px-2 sm:px-6 py-3">Username</th>
                    <th class="px-2 sm:px-6 py-3">Typ</th>
                    <th class="px-2 sm:px-6 py-3">Betrag</th>
                    <th class="px-2 sm:px-6 py-3">Status</th>
                    <th class="px-2 sm:px-6 py-3">Datum</th>
                    <th class="px-2 sm:px-6 py-3">Aktion</th>
                </tr>
            </thead>
            <tbody>
                @if(count($payments) > 0)
                    @foreach($payments as $payment)
                        <tr>
                            <td class="px-2 sm:px-6 py-4">{{ $payment->id }}</td>
                            <td class="px-2 sm:px-6 py-4">{{ $payment->user->name }}</td>
                            <td class="px-2 sm:px-6 py-4">{{ $payment->type }}</td>
                            <td class="px-2 sm:px-6 py-4">{{ $payment->quantity }}</td>
                            <td class="px-2 sm:px-6 py-4">{{ $payment->status }}</td>
                            <td class="px-2 sm:px-6 py-4">{{ $payment->created_at->format('Y-m-d')}}
                                <br>
                                {{ $payment->created_at->format('H:i:s') }} Uhr
                            </td>
                            <td class="px-2 sm:px-6 py-4 flex flex-col sm:flex-row gap-1 sm:gap-2">
                                <button type="submit" name="authorize_id" value="{{ $payment->id }}" class="bg-green-600 text-white rounded px-2 sm:px-4 py-1 sm:py-2 text-xs sm:text-sm">Autorisieren</button>
                                <button type="submit" name="decline_id" value="{{ $payment->id }}" class="bg-red-600 text-white rounded px-2 sm:px-4 py-1 sm:py-2 text-xs sm:text-sm">Ablehnen</button>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="7" class="px-2 sm:px-6 py-4 text-center">Keine offenen Zahlungen</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</form>
