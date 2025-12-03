<div 
    x-data="{ open: true, tab: 'request' }"
    x-show="open"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-6"
>
    <div class="bg-white dark:bg-gray-900 w-full max-w-4xl rounded-2xl shadow-2xl overflow-hidden animate-fadeIn">
        
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                Hofverwaltung
            </h2>
            <button 
                @click="open = false"
                class="text-gray-500 hover:text-gray-800 dark:hover:text-gray-300"
            >
                ✕
            </button>
        </div>

        <!-- Tabs -->
        <div class="flex border-b border-gray-200 dark:border-gray-700">
            <button 
                class="py-3 px-5 text-sm font-medium"
                :class="tab === 'request' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500'"
                @click="tab = 'request'"
            >
                Hof anfragen
            </button>
            <button 
                class="py-3 px-5 text-sm font-medium"
                :class="tab === 'manage' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500'"
                @click="tab = 'manage'"
            >
                Hof verwalten
            </button>
            <button 
                class="py-3 px-5 text-sm font-medium"
                :class="tab === 'admin' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500'"
                @click="tab = 'admin'"
            >
                Admin Aktionen
            </button>
        </div>

        <!-- Content -->
        <div class="p-6 space-y-6">

            <!-- TAB: Hof anfragen -->
            <div x-show="tab === 'request'" x-transition>
                <h3 class="text-lg font-semibold mb-3 text-gray-800 dark:text-gray-200">
                    Hof-Anfrage senden
                </h3>

                <form>
                    <div class="mb-4">
                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-400">Hofname</label>
                        <input type="text" class="w-full px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-700">
                    </div>

                    <button class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Anfrage senden
                    </button>
                </form>
            </div>

            <!-- TAB: Hof verwalten -->
            <div x-show="tab === 'manage'" x-transition>
                <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-200">
                    Hof verwalten
                </h3>

                <!-- Userliste -->
                <div class="space-y-3">

                    <div class="flex items-center justify-between p-4 bg-gray-100 dark:bg-gray-800 rounded-xl">
                        <div>
                            <p class="font-medium">Max Mustermann</p>
                            <p class="text-sm text-gray-500">Mitglied</p>
                        </div>
                        <button class="px-3 py-1 text-sm bg-red-600 text-white rounded-md">
                            Entfernen
                        </button>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-gray-100 dark:bg-gray-800 rounded-xl">
                        <div>
                            <p class="font-medium">Lisa Muster</p>
                            <p class="text-sm text-gray-500">Mitglied</p>
                        </div>
                        <button class="px-3 py-1 text-sm bg-red-600 text-white rounded-md">
                            Entfernen
                        </button>
                    </div>

                </div>

                <!-- User hinzufügen -->
                <div class="mt-6">
                    <h4 class="text-md font-semibold mb-2 text-gray-800 dark:text-gray-200">User hinzufügen</h4>
                    <input type="text" placeholder="User suchen…" class="w-full px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-700">
                </div>
            </div>

            <!-- TAB: Admin -->
            <div x-show="tab === 'admin'" x-transition>
                <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-200">
                    Admin Aktionen
                </h3>

                <!-- Hof erstellen -->
                <div class="bg-gray-100 dark:bg-gray-800 p-4 rounded-xl mb-6">
                    <h4 class="font-semibold mb-2">Neuen Hof erstellen</h4>
                    <form class="space-y-3">
                        <input type="text" placeholder="Hofname" class="w-full px-4 py-2 rounded-lg bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700">

                        <input type="text" placeholder="Owner User E-Mail" class="w-full px-4 py-2 rounded-lg bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700">

                        <button class="px-5 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                            Hof erstellen
                        </button>
                    </form>
                </div>

                <!-- Genehmigen -->
                <div class="bg-gray-100 dark:bg-gray-800 p-4 rounded-xl">
                    <h4 class="font-semibold mb-3">Offene Anfragen</h4>

                    <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-900 rounded-lg mb-2">
                        <div>
                            <p class="font-medium">Peter wünscht Hof "Sunny Farm"</p>
                            <p class="text-sm text-gray-500">User-ID: 23</p>
                        </div>
                        <div class="flex gap-2">
                            <button class="px-3 py-1 bg-green-600 text-white rounded-md">✔</button>
                            <button class="px-3 py-1 bg-red-600 text-white rounded-md">✕</button>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<style>
    .animate-fadeIn {
        animation: fadeIn .25s ease-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: scale(.98); }
        to { opacity: 1; transform: scale(1); }
    }
</style>
