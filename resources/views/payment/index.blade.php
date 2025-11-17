<x-app-layout>
    <x-slot name="header">
        <h1 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Zahlungen & Transaktionen') }}
        </h1>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto space-y-6">
        @foreach (['success', 'error', 'warning', 'info'] as $msg)
        @if(session($msg))
            <div class="mb-6 p-4 rounded-lg {{ $msg === 'success' ? 'bg-green-50 text-green-800 border border-green-200' : ($msg === 'error' ? 'bg-red-50 text-red-800 border border-red-200' : ($msg === 'warning' ? 'bg-yellow-50 text-yellow-800 border border-yellow-200' : 'bg-blue-50 text-blue-800 border border-blue-200')) }} dark:bg-gray-800 dark:text-gray-100">
                {{ session($msg) }}
            </div>
        @endif
    @endforeach

            <!-- Übersicht Karten -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex items-center">
                            <div class="p-2 bg-blue-500 rounded-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Kontostand</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ number_format(auth()->user()->bank->balance ?? 0, 2, ',', '.') }} €</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex items-center">
                            <div class="p-2 bg-green-500 rounded-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Abgeschlossene Transaktionen</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $transaktionens->where('status', false)->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex items-center">
                            <div class="p-2 bg-yellow-500 rounded-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Offene Orders</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $orders->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div x-data="{ open: '' }" class="space-y-6">
                <!-- Pay In -->
                <div :class="['p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg', open === 'payin' ? 'active' : '']">
                    <div class="max-w-xl" @click="open === 'payin' ? open = '' : open = 'payin'" style="cursor:pointer;">
                        <h1>Pay in</h1>
                    </div>
                    <template x-if="open === 'payin'">
                        <x-pay-in-form />
                    </template>
                </div>
                <!-- Pay Out -->
                <div :class="['p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg', open === 'payout' ? 'active' : '']">
                    <div class="max-w-xl" @click="open === 'payout' ? open = '' : open = 'payout'" style="cursor:pointer;">
                        <h1>Pay out</h1>
                    </div>
                    <template x-if="open === 'payout'">
                        <x-pay-out-form />
                    </template>
                </div>

                <!-- Transfer -->
                <div :class="['p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg', open === 'transfer' ? 'active' : '']">
                    <div class="max-w-xl" @click="open === 'transfer' ? open = '' : open = 'transfer'" style="cursor:pointer;">
                        <h1>Transfer</h1>
                    </div>
                    <template x-if="open === 'transfer'">
                        @include('components.transfer-form')
                    </template>
                </div>
              
                <!-- Alle Transaktionen -->
                <div :class="['p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg', open === 'transaction' ? 'active' : '']">
                    <div class="max-w-xl" @click="open === 'transaction' ? open = '' : open = 'transaction'" style="cursor:pointer;">
                        <h1>Alle Transaktionen</h1>
                    </div>
                    <template x-if="open === 'transaction'">
                        <x-transaction-list :transaktionens="$transaktionens ?? collect([])" />
                    </template>
                </div>
                <!-- Offene Orders -->
                <div :class="['p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg', open === 'orders' ? 'active' : '']">
                    <div class="max-w-xl" @click="open === 'orders' ? open = '' : open = 'orders'" style="cursor:pointer;">
                        <h1>Offene Orders</h1>
                    </div>
                    <template x-if="open === 'orders'">
                        <x-orders-list :orders="$orders ?? collect([])" />
                    </template>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>