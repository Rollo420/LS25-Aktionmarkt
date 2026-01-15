<x-app-layout>
    <x-slot name="header">
        <h1 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Transfer') }}
        </h1>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto space-y-6">
            @foreach (['success', 'error', 'warning', 'info'] as $msg)
                @if(session($msg))
                    <div class="mb-6 p-4 rounded-lg {{ $msg === 'success' ? 'bg-green-50 text-green-800 border border-green-200' : ($msg === 'error' ? 'bg-red-50 text-red-800 border border-red-200' : ($msg === 'warning' ? 'bg-yellow-50 text-yellow-800 border border-yellow-200' : 'bg-blue-50 text-blue-800 border border-blue-200')) }} dark:bg-gray-800 dark:text-gray-100">
                        {{ session($msg) }}
                    </div>
                @endif
            @endforeach

            <div class="p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                @include('components.transfer-form')
            </div>
        </div>
    </div>
</x-app-layout>
