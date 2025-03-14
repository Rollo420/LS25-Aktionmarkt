<head>
    <link rel="stylesheet" href="scss/app.scss">
</head>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Time Manager') }}
        </h2>
    </x-slot>

    <div class="py-12 month-choose">
        <div class="lg:px-20
        p-12 text-gray-900 dark:text-gray-100
        bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <h2>Please choose the current month in LS25</h2> <br>
            <form method="POST" action="{{ route('time.update') }}">
                @csrf
                <div class="month-content grid grid-flow-col grid-rows-6 gap-2">
                    @foreach ($monthArray as $month)
                    <div class="{{ $month }} ">
                        <input type="radio" id="{{ $month }}" name="choose" value="{{ $month }}" {{ $selectedMonth == $month ? 'checked' : '' }}>
                        <label for="{{ $month }}">{{ $month }}</label><br>
                    </div>
                    @endforeach
                </div>
                <button type="submit">Submit</button>
            </form>
            <div class="selected-month mt-4">
                <h3>Selected Month: <span id="selected-month">{{ $selectedMonth ?? 'None' }}</span></h3>
                <div>
                    @foreach ($insertData as $key => $value)
                    <p>{{ $key }}: {{ $value }}</p>
                    @endforeach
                </div>

            </div>


        </div>
    </div>
</x-app-layout>