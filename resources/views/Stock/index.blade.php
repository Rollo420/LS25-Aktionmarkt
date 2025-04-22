<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Stocks') }}
        </h2>
    </x-slot>

    <!--
        Übersichtstabelle aller Aktien.
        Jeder Eintrag ist klickbar und führt zur Detailansicht der Aktie.
        Die Daten werden aus dem Controller übergeben (id, Name, aktueller Preis).
    -->
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-center">
                        <table>
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stocks as $stock)
                                <tr onclick="window.location='{{ route('stock.store', $stock[0]) }}'">
                                    <th>
                                        <p>{{ $stock[1] }}</p>
                                    </th>
                                    <th>
                                        <p>{{ $stock[2] }}</p>
                                    </th>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>