<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Maschinenverwaltung') }}
        </h2>
    </x-slot>

    <head>
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
                            // Added custom colors for tiles
                            'yellow': {
                                50: '#fefce8',
                                100: '#fef9c3',
                                700: '#a16207',
                                300: '#fde68a',
                            },
                            'red': {
                                50: '#fef2f2',
                                100: '#fee2e2',
                                700: '#b91c1c',
                                300: '#fca5a5',
                            }
                        }
                    }
                },
                darkMode: 'media', // Use 'media' for system preference or 'class'
            }
        </script>
        <style>
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

        <!-- Header: Maschinenverwaltung -->
        <header class="bg-white shadow dark:bg-gray-800">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Maschinenverwaltung
                </h2>
                <!-- Button: Maschine hinzufügen -->
                <button onclick="showModal('addMachineModal')" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                    <!-- Plus Icon -->
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Maschine hinzufügen
                </button>
            </div>
        </header>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <!-- Maschinenliste / Haupttabelle -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4 border-b border-gray-200 dark:border-gray-700 pb-3">
                            Aktueller Maschinenpark ({{ machineData.length }} Geräte)
                        </h3>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Bezeichnung / Modell
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Typ
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            PS-Anzahl
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Kennzeichen
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Kaufpreis
                                        </th>
                                        <th scope="col" class="relative px-6 py-3">
                                            <span class="sr-only">Aktionen</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="machineList" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    <!-- Machine rows will be injected here by JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Status-Zusammenfassung -->
                <!-- Changed to grid-cols-2 on small screens and grid-cols-4 on medium screens and up -->
                <div class="mt-8 grid grid-cols-2 md:grid-cols-4 gap-6">
                    <!-- 1. Total Machines -->
                    <div class="bg-indigo-50 dark:bg-gray-700 overflow-hidden rounded-lg p-5 shadow">
                        <p class="text-sm font-medium text-indigo-700 dark:text-indigo-300">Total Geräte</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-1" id="totalMachines">0</p>
                    </div>
                    <!-- 2. Total HP -->
                    <div class="bg-green-50 dark:bg-gray-700 overflow-hidden rounded-lg p-5 shadow">
                        <p class="text-sm font-medium text-green-700 dark:text-green-300">Gesamt-PS des Parks</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-1" id="totalHP">0</p>
                    </div>
                    <!-- 3. Total Value -->
                    <div class="bg-yellow-50 dark:bg-gray-700 overflow-hidden rounded-lg p-5 shadow">
                        <p class="text-sm font-medium text-yellow-700 dark:text-yellow-300">Gesamtwert des Parks</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-1" id="totalValue">0 €</p>
                    </div>
                    <!-- 4. NEW: Total Tax -->
                    <div class="bg-red-50 dark:bg-gray-700 overflow-hidden rounded-lg p-5 shadow">
                        <p class="text-sm font-medium text-red-700 dark:text-red-300">PS-Steuer (Jährlich)</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-1" id="totalTax">0 €</p>
                    </div>
                </div>

            </div>
        </div>

        <!-- Modals (Custom UI instead of alert()) -->

        <!-- Add Machine Modal -->
        <div id="addMachineModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 hidden flex items-center justify-center p-4 z-50">
            <div class="bg-white dark:bg-gray-900 rounded-lg shadow-xl w-full max-w-lg">
                <div class="p-6">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Neue Maschine hinzufügen</h3>
                    <p class="text-gray-600 dark:text-gray-400">Hier können Sie das Formular zur Eingabe neuer Maschinen-Details implementieren.</p>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button onclick="hideModal('addMachineModal')" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                            Schließen
                        </button>
                        <!-- Example for a primary action -->
                        <button onclick="hideModal('addMachineModal'); /* Add logic here */" class="px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                            Speichern
                        </button>
                    </div>
                </div>
            </div>
        </div>


        <script>
            // Mock Data for Machines, updated with all requested fields
            const machineData = [{
                    name: "Fastrac 4220",
                    model: "JCB",
                    type: "Traktor",
                    hp: 220,
                    licensePlate: "HU-FA 422",
                    cost: "180.000 €"
                },
                {
                    name: "Tucano 580",
                    model: "Claas",
                    type: "Mähdrescher",
                    hp: 350,
                    licensePlate: "HU-CL 580",
                    cost: "350.000 €"
                },
                {
                    name: "Solitair 9",
                    model: "Lemken",
                    type: "Sämaschine",
                    hp: 0,
                    licensePlate: "N/A",
                    cost: "75.000 €"
                }, // Keine PS für Anbaugeräte
                {
                    name: "Axial-Flow 9250",
                    model: "Case IH",
                    type: "Mähdrescher",
                    hp: 600,
                    licensePlate: "HU-IH 925",
                    cost: "420.000 €"
                },
                {
                    name: "Puma 240 CVX",
                    model: "Case IH",
                    type: "Traktor",
                    hp: 240,
                    licensePlate: "HU-CV 240",
                    cost: "155.000 €"
                },
            ];

            // Utility function to show/hide modals
            function showModal(id) {
                document.getElementById(id).classList.remove('hidden');
            }

            function hideModal(id) {
                document.getElementById(id).classList.add('hidden');
            }

            // Function to render the machine table
            function renderMachineTable() {
                const tableBody = document.getElementById('machineList');
                tableBody.innerHTML = ''; // Clear previous content

                let totalHP = 0;
                let totalCostValue = 0;

                machineData.forEach(machine => {
                    // Calculate total HP (only count if hp > 0)
                    totalHP += machine.hp;

                    // Calculate Total Cost: Remove formatting and convert to number
                    const numericCostString = machine.cost.replace(/\./g, '').replace(' €', '').trim();
                    const cost = parseInt(numericCostString, 10);
                    if (!isNaN(cost)) {
                        totalCostValue += cost;
                    }

                    // Create the table row
                    const row = document.createElement('tr');
                    row.classList.add('hover:bg-gray-50', 'dark:hover:bg-gray-700');
                    row.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">${machine.name}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">${machine.model}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                    ${machine.type}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300 font-semibold">
                    ${machine.hp > 0 ? machine.hp.toLocaleString('de-DE') + ' PS' : 'N/A'}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                    ${machine.licensePlate}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300 font-medium">
                    ${machine.cost}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                    <button onclick="showModal('editMachineModal')" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200 p-1 rounded-md text-xs">
                        Bearbeiten
                    </button>
                    <button onclick="showModal('retireMachineModal')" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-200 p-1 rounded-md text-xs">
                        Ausmustern
                    </button>
                </td>
            `;
                    tableBody.appendChild(row);
                });

                // --- Berechnungen für Status-Kacheln ---

                // 1. Gesamtwert formatieren
                const formattedTotalValue = totalCostValue.toLocaleString('de-DE', {
                    style: 'currency',
                    currency: 'EUR',
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                });

                // 2. PS-Steuer berechnen: 10€ pro 100 PS
                // Nur Traktoren haben eine PS-Steuer. In diesem Mock-Beispiel zählen wir einfach die gesamte PS-Zahl aller Motorgeräte.
                const totalTaxValue = (totalHP / 100) * 10;

                // PS-Steuer formatieren
                const formattedTotalTax = totalTaxValue.toLocaleString('de-DE', {
                    style: 'currency',
                    currency: 'EUR',
                    minimumFractionDigits: 2, // 2 Dezimalstellen für Beträge
                    maximumFractionDigits: 2
                });


                // --- Update Status-Kacheln ---
                document.getElementById('totalMachines').textContent = machineData.length;
                document.getElementById('totalHP').textContent = totalHP.toLocaleString('de-DE') + ' PS';
                document.getElementById('totalValue').textContent = formattedTotalValue;
                document.getElementById('totalTax').textContent = formattedTotalTax; // Update the new tax tile
            }

            // Render the table on load
            window.onload = renderMachineTable;
        </script>


        <!-- Edit Machine Modal (Placeholder) -->
        <div id="editMachineModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 hidden flex items-center justify-center p-4 z-50">
            <div class="bg-white dark:bg-gray-900 rounded-lg shadow-xl w-full max-w-lg">
                <div class="p-6">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Maschine bearbeiten</h3>
                    <p class="text-gray-600 dark:text-gray-400">Details für die ausgewählte Maschine bearbeiten.</p>
                    <div class="mt-6 flex justify-end">
                        <button onclick="hideModal('editMachineModal')" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                            Schließen
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Retire Machine Modal (Placeholder) -->
        <div id="retireMachineModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 hidden flex items-center justify-center p-4 z-50">
            <div class="bg-white dark:bg-gray-900 rounded-lg shadow-xl w-full max-w-lg">
                <div class="p-6">
                    <h3 class="text-xl font-semibold text-red-600 dark:text-red-400 mb-4">Maschine ausmustern</h3>
                    <p class="text-gray-600 dark:text-gray-400">Sind Sie sicher, dass Sie diese Maschine aus dem aktiven Bestand entfernen möchten?</p>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button onclick="hideModal('retireMachineModal')" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                            Abbrechen
                        </button>
                        <button onclick="hideModal('retireMachineModal'); /* Remove logic here */" class="px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-red-600 hover:bg-red-700">
                            Ausmustern bestätigen
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </body>


</x-app-layout>