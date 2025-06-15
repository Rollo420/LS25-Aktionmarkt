<x-app-layout>
    <x-slot name="header">
        <h1 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Payment') }}
        </h1>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
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
                <!-- Transaction -->
                <div :class="['p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg', open === 'transaction' ? 'active' : '']">
                    <div class="max-w-xl" @click="open === 'transaction' ? open = '' : open = 'transaction'" style="cursor:pointer;">
                        <h1>Transaction</h1>
                    </div>
                    <template x-if="open === 'transaction'">
                        <x-transaction-list />
                    </template>
                </div>
                <!-- Orders -->
                <div :class="['p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg', open === 'orders' ? 'active' : '']">
                    <div class="max-w-xl" @click="open === 'orders' ? open = '' : open = 'orders'" style="cursor:pointer;">
                        <h1>Orders</h1>
                    </div>
                    <template x-if="open === 'orders'">
                        <x-orders-list />
                    </template>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>