@props(['type' => 'success', 'dismissable' => true])

@php
    $bgColorClass = match($type) {
        'success' => 'bg-green-100 border-green-400 text-green-700',
        'error' => 'bg-red-100 border-red-400 text-red-700',
        'warning' => 'bg-yellow-100 border-yellow-400 text-yellow-700',
        'info' => 'bg-blue-100 border-blue-400 text-blue-700',
        default => 'bg-gray-100 border-gray-400 text-gray-700'
    };
@endphp

<div x-data="{ open: true }" x-show="open" class="mb-4 p-4 border rounded {{ $bgColorClass }}" role="alert">
    <div class="flex justify-between items-center">
        <div>{{ $slot }}</div>
        @if ($dismissable)
            <button @click="open = false" type="button" class="ml-4 font-bold">
                ×
            </button>
        @endif
    </div>
</div>
