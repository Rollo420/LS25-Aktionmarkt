<x-app-layout>
    {{-- Der Slot 'header' definiert den Titelbereich --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Hofverwaltung') }}
        </h2>
    </x-slot>

    <head>
      
        <!-- Tailwind CSS CDN -->
        <script src="https://cdn.tailwindcss.com"></script>
        <!-- Custom config for Indigo colors -->
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            'indigo': {
                                50: '#eef2ff',
                                100: '#e0e7ff',
                                200: '#c7d2fe',
                                300: '#a5b4fc',
                                400: '#818cf8',
                                500: '#6366f1',
                                600: '#4f46e5',
                                700: '#4338ca',
                                800: '#3730a3',
                                900: '#312e81',
                                950: '#1e1b4b',
                            },
                        }
                    }
                },
                darkMode: 'media', // Use 'media' for system preference or 'class'
            }
        </script>
        <style>
            /* Emulate x-app-layout's main background */
            body {
                background-color: #f3f4f6;
                color: #1f2937;
                font-family: 'Inter', sans-serif;
            }

            @media (prefers-color-scheme: dark) {
                body {
                    background-color: #111827;
                    color: #f9fafb;
                }
            }
        </style>
    </head>

    <body>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

                <!-- 1. Finanz-Dashboard: Gesamtwert und Liquide Mittel -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                    <!-- Block 1: Gesamtwert des Hofes (Total Farm Value) -->
                    <div class="bg-indigo-600 dark:bg-indigo-900 overflow-hidden shadow-xl rounded-lg p-6 md:col-span-1 flex flex-col justify-center">
                        <p class="text-sm font-medium text-indigo-200 uppercase tracking-wider">Gesamtwert des Hofes</p>
                        <!-- Mock Data Calculation: 845k (Cash) + 125k (Aktien) + 650k (Maschinen) + 1.5M (Felder) = 3.120.320,50 € -->
                        <p class="text-5xl font-extrabold text-white mt-2">
                            3.120.320,50 €
                        </p>
                    </div>

                    <!-- Block 2: Hof-Guthaben (Cash) -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-lg p-6 flex flex-col justify-center border-l-4 border-green-500">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Flüssiges Hof-Guthaben</p>
                        <p class="text-3xl font-bold text-green-600 dark:text-green-400 mt-1">
                            845.320,50 €
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                            <span class="text-green-600 dark:text-green-400">↑ +1.2%</span> seit letzter Saison
                        </p>
                    </div>

                    <!-- Block 3: Aktien-Portfolio (Stocks/Shares) -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-lg p-6 flex flex-col justify-center border-l-4 border-indigo-500">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aktien-Portfolio Wert</p>
                        <p class="text-3xl font-bold text-indigo-600 dark:text-indigo-400 mt-1">
                            125.000,00 €
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                            <span class="font-semibold text-indigo-600 dark:text-indigo-400">500</span> Anteile @ 250 €/Stk.
                        </p>
                    </div>
                </div>

                <!-- 2. Detaillierte Finanz-Aufschlüsselung (Detailed Finance Breakdown) -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-lg">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4 border-b border-gray-200 dark:border-gray-700 pb-3">
                            Hof-Vermögenswerte (Gesamtrechnung)
                        </h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">

                                    <!-- Category: Felder -->
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                            Felder & Immobilien (Grundstücke)
                                        </td>
                                        <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300 text-right">
                                            1.500.000,00 €
                                        </td>
                                    </tr>

                                    <!-- Category: Maschinen -->
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                            Maschinenpark (Traktoren, Geräte, etc.)
                                        </td>
                                        <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300 text-right">
                                            650.000,00 €
                                        </td>
                                    </tr>

                                    <!-- Category: Hof-Guthaben -->
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                            Hof-Guthaben (Liquidität)
                                        </td>
                                        <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300 text-right">
                                            845.320,50 €
                                        </td>
                                    </tr>

                                    <!-- Category: Aktien-Portfolio -->
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                            Aktien-Portfolio (Marktwert)
                                        </td>
                                        <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300 text-right">
                                            125.000,00 €
                                        </td>
                                    </tr>

                                    <!-- Gesamt (Total) -->
                                    <tr class="bg-indigo-50 dark:bg-gray-700/50">
                                        <td class="px-6 py-4 whitespace-nowrap text-lg font-extrabold text-indigo-700 dark:text-indigo-300">
                                            GESAMTWERT DES HOFES
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-lg font-extrabold text-indigo-700 dark:text-indigo-300 text-right">
                                            3.120.320,50 €
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- 3. Hof-Team Übersicht (Detailed Table) -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-lg">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-6 border-b border-gray-200 dark:border-gray-700 pb-3">
                            Hof-Team Mitglieder
                        </h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Name
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Rolle
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Beigetreten
                                        </th>
                                        <th scope="col" class="relative px-6 py-3">
                                            <span class="sr-only">Bearbeiten</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    <!-- Mock data loop -->
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">Benutzer 1 (Hofbesitzer)</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                Eigentümer
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            01.01.2025
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                            <a href="#" onclick="event.preventDefault(); alert('Bearbeitungsmodus für Benutzer 1');"
                                                class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200 p-1 rounded-md border border-indigo-200 dark:border-indigo-700 bg-indigo-50 dark:bg-gray-700/50 text-xs">
                                                Bearbeiten
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">Lisa M.</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                Landwirt (Vollzeit)
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            15.03.2025
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                            <a href="#" onclick="event.preventDefault(); alert('Bearbeitungsmodus für Lisa M.');"
                                                class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200 p-1 rounded-md border border-indigo-200 dark:border-indigo-700 bg-indigo-50 dark:bg-gray-700/50 text-xs">
                                                Bearbeiten
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">Max S.</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                                Lohnunternehmer
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            20.06.2025
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                            <a href="#" onclick="event.preventDefault(); alert('Bearbeitungsmodus für Max S.');"
                                                class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200 p-1 rounded-md border border-indigo-200 dark:border-indigo-700 bg-indigo-50 dark:bg-gray-700/50 text-xs">
                                                Bearbeiten
                                            </a>
                                        </td>
                                    </tr>
                                    <!-- Ende Mock data loop -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>


                <!-- 4. Schnellzugriffe / Management-Kacheln (Tiles) -->
                <!-- Nur noch Maschinen und Felder bleiben als Kernmanagement-Kacheln -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Kachel 1: Maschinenpark -->
                    <a href="#" onclick="event.preventDefault()" class="block p-5 bg-white dark:bg-gray-800 rounded-lg shadow-xl hover:shadow-2xl transition duration-300 group">
                        <div class="flex items-center justify-between">
                            <!-- Icon: Tractor / Equipment -->
                            <svg class="h-8 w-8 text-yellow-500 dark:text-yellow-400 group-hover:scale-105 transition duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684L10 9l1.248-3.084a1 1 0 011.664 0L14 9l1.772-4.316A1 1 0 0116.72 3H20a2 2 0 012 2v10a2 2 0 01-2 2h-3.28a1 1 0 01-.948-.684L14 15l-1.248 3.084a1 1 0 01-1.664 0L10 15l-1.772 4.316A1 1 0 017.28 21H4a2 2 0 01-2-2V5z"></path>
                            </svg>
                            <span class="text-2xl font-bold text-gray-900 dark:text-gray-100 group-hover:text-indigo-600 transition duration-300">
                                Maschinen
                            </span>
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Maschinenpark und Wartung verwalten.</p>
                    </a>

                    <!-- Kachel 2: Felder & Erträge -->
                    <a href="#" onclick="event.preventDefault()" class="block p-5 bg-white dark:bg-gray-800 rounded-lg shadow-xl hover:shadow-2xl transition duration-300 group">
                        <div class="flex items-center justify-between">
                            <!-- Icon: Field / Crop -->
                            <svg class="h-8 w-8 text-green-500 dark:text-green-400 group-hover:scale-105 transition duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.264a2 2 0 01-1.789-2.894l3.5-7z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 9H3v5l6 6v-3m13-6L14 7h-4.764"></path>
                            </svg>
                            <span class="text-2xl font-bold text-gray-900 dark:text-gray-100 group-hover:text-indigo-600 transition duration-300">
                                Felder
                            </span>
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Aussaat, Ernte und Fruchtfolgen planen.</p>
                    </a>

                    <!-- Kachel 3: Lagerbestand (ENTFERNT) -->
                    <!-- Kachel 4: Finanzen (LS25) (ENTFERNT) -->
                </div>

            </div>
        </div>
    </body>

</x-app-layout>
