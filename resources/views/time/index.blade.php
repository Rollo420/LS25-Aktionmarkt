<head>
    <link rel="stylesheet" href="scss/app.scss">
</head>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Time Manager') }}
        </h2>
    </x-slot>

    <div class="min-h-screen flex items-center justify-center py-12 month-choose">
        <div class="p-4 sm:p-8 md:p-12 lg:px-20 text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg w-full max-w-2xl mx-auto flex flex-col items-center">
            <h2 class="text-lg sm:text-2xl mb-8 text-center font-semibold">Please choose the current month in LS25</h2>
            <form method="POST" action="{{ route('time.update') }}" class="w-full flex flex-col items-center">
                @csrf
                <div class="month-content grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-y-8 gap-x-8 w-full justify-items-center">
                    @foreach ($monthArray as $month)
                    <div class="flex flex-col items-center justify-center w-full max-w-xs my-2">
                        <input type="radio" id="{{ $month }}" name="choose" value="{{ $month }}" {{ $selectedMonth == $month ? 'checked' : '' }} class="form-radio h-4 w-4 text-blue-600 mb-2">
                        <label for="{{ $month }}" class="text-base text-center px-8 py-2 whitespace-nowrap w-full">{{ $month }}</label>
                    </div>
                    @endforeach
                </div>
                <button type="submit" class="mt-8 px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 w-full sm:w-auto font-semibold">Submit</button>
            </form>
            <div class="selected-month mt-8 w-full">
                <h3 class="text-base font-semibold text-center">Selected Month: <span id="selected-month">{{ $selectedMonth ?? 'None' }}</span></h3>
                <div class="text-center">
                    @foreach ($insertData as $key => $value)
                    <p>{{ $key }}: {{ $value }}</p>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>