<div class="mt-4">
    <p class="text-gray-200 mb-2">Offene Orders:</p>
    <ul class="list-disc ml-6 text-gray-400">
        @foreach($orders as $order)
            <li class="mb-2">
                <span class="font-semibold">{{ ucfirst($order->type) }}</span>:
                {{ $order->quantity }}
                @if($order->stock_id != null)
                    <span> x Aktie </span>
                @else
                    <span>€ </span>
                @endif
                   
                @if($order->stock)
                    {{ $order->stock->name }} 
                @endif
                <span class="text-gray-500">am {{ $order->created_at->format('d.m.Y') }}</span>
            </li>
        @endforeach
    </ul>
</div>
