<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg sm:text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Time Manager') }}
        </h2>
    </x-slot>

    <div class="min-h-screen flex items-center justify-center py-12 month-choose">
        <div class="py-4 sm:py-8 md:py-12 text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg w-full max-w-4xl mx-auto flex flex-col items-center">
            <h2 class="text-base sm:text-lg md:text-xl lg:text-2xl xl:text-3xl 2xl:text-4xl mb-4 sm:mb-8 text-center font-semibold">Please choose the current month in LS25</h2>
            <form method="POST" action="{{ route('time.update') }}" class="w-full flex flex-col items-center">
                @csrf
                <div class="month-content grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-y-4 sm:gap-y-8 gap-x-4 sm:gap-x-8 w-full justify-items-center">
                    @foreach ($monthArray as $month)
                    <div class="flex flex-col items-center justify-center w-full max-w-xs my-1 sm:my-2">
                        <input type="radio" id="{{ $month }}" name="choose" value="{{ $month }}" {{ $selectedMonth == $month ? 'checked' : '' }} class="form-radio h-3 w-3 sm:h-4 sm:w-4 text-blue-600 mb-1 sm:mb-2">
                        <label for="{{ $month }}" class="text-sm sm:text-base md:text-lg text-center px-4 sm:px-8 py-1 sm:py-2 whitespace-nowrap w-full">{{ $month }}</label>
                    </div>
                    @endforeach
                </div>
                <button type="submit" class="mt-4 sm:mt-8 px-4 sm:px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 w-full sm:w-auto font-semibold text-sm sm:text-base">Submit</button>
            </form>
            <div class="selected-month mt-4 sm:mt-8 w-full">
                <h3 class="text-sm sm:text-base md:text-lg font-semibold text-center">Selected Month: <span id="selected-month">{{ session('selectedMonth') ?? 'None'}}</span></h3>

            </div>
        </div>
    </div>
</x-app-layout>
