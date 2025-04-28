<x-app-layout>
    <x-slot name="header">
        <h1 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Payment') }}
        </h1>
    </x-slot>


    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <div class="list-timeline">
                        <h3 class="headerMonth">Payment</h3>
                        <form method="POST" action="{{ route('update.payment') }}">
                            @csrf
                            <select name="months" onchange="this.form.submit()" class="transaction-dropdown">
                                <option value="Transaction" >Transaction</option>
                                <option value="PayIn" {{ session('selectedPayment') == 'PayIn' ? 'selected' : '' }}>Pay in</option>
                                <option value="PayOut" {{ session('selectedPayment') == 'PayOut' ? 'selected' : '' }}>Pay out</option>
                            </select>
                        </form>
                    </div>
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl" onclick="document.getElementById('payInForm').submit()" style="cursor:pointer;">
                    <h1>Pay in</h1>
                    <form id="payInForm" method="POST" action="{{ route('payment.updateMethod') }}" style="display:none;">
                        @csrf
                        <input type="hidden" name="months" value="PayIn">
                    </form>
                    <div class="pay-in">
                        <!-- Pay in Formular oder Inhalt hier -->
                    </div>
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl" onclick="document.getElementById('payOutForm').submit()" style="cursor:pointer;">
                    <h1>Pay out</h1>
                    <form id="payOutForm" method="POST" action="{{ route('payment.updateMethod') }}" style="display:none;">
                        @csrf
                        <input type="hidden" name="months" value="PayOut">
                    </form>
                    <!-- Pay out Formular oder Inhalt hier -->
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl" onclick="document.getElementById('transactionForm').submit()" style="cursor:pointer;">
                    <h1>Transaction</h1>
                    <form id="transactionForm" method="POST" action="{{ route('payment.updateMethod') }}" style="display:none;">
                        @csrf
                        <input type="hidden" name="months" value="Transaction">
                    </form>
                    <div class="payment-header">
                        <div 
                        @class([
                            'transaction',
                            'active' => session('selectedPayment') == 'Transaction',
                            'inactive' => session('selectedPayment') != 'Transaction',
                        ])>
                            <!-- Transaction Formular oder Inhalt hier -->
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl" onclick="document.getElementById('ordersForm').submit()" style="cursor:pointer;">
                    <h1>Orders</h1>
                    <form id="ordersForm" method="POST" action="{{ route('payment.updateMethod') }}" style="display:none;">
                        @csrf
                        <input type="hidden" name="months" value="Orders">
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>