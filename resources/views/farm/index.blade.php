<x-app-layout>
    {{-- Der Slot 'header' definiert den Titelbereich --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Farm Übersicht') }}
        </h2>
    </x-slot>

    {{-- Hauptinhalt --}}
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Hauptcontainer-Karte --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">

                {{-- Titel Sektion --}}
                <div class="text-center mb-10 pt-4">
                    <h1 class="text-4xl font-extrabold text-gray-900 dark:text-gray-100 tracking-tight">
                        {{__('Farm Management')}}
                    </h1>
                    <p class="text-gray-500 dark:text-gray-400 mt-3 text-lg">
                        {{__('Wählen Sie eine der verfügbaren Aktionen für Ihre Farmen.')}}
                    </p>
                </div>

                <div class="mt-8">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 2xl:grid-cols-4 gap-8">

                        {{-- Feature Cards Container --}}
                        @if(!Auth::user()->isInFarm())

                        {{-- 1. KARTE: Farm anfragen --}}
                        <a href="#"
                            class="group bg-gray-50 dark:bg-gray-900 rounded-xl p-8 shadow-lg hover:shadow-2xl hover:scale-[1.03] transition duration-300 ease-in-out border border-transparent hover:border-blue-600 dark:hover:border-blue-500 flex flex-col items-center text-center min-h-[280px]">

                            {{-- Icon: Dokument/Anfrage --}}
                            <svg class="w-16 h-16 text-blue-500 group-hover:text-blue-600 transition duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.586a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>

                            <h3 class="text-xl font-bold mt-5 text-gray-900 dark:text-gray-100">Farm anfragen</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-sm mt-2">Stellen Sie eine neue Farm-Anfrage zur Genehmigung.</p>
                        </a>

                        {{-- 5. KARTE: Farm beitreten --}}
                        <a href="#"
                            class="group bg-gray-50 dark:bg-gray-900 rounded-xl p-8 shadow-lg hover:shadow-2xl hover:scale-[1.03] transition duration-300 ease-in-out border border-transparent hover:border-purple-600 dark:hover:border-purple-500 flex flex-col items-center text-center min-h-[280px]">

                            {{-- Icon: Beitreten/Team --}}
                            <svg class="w-16 h-16 text-purple-500 group-hover:text-purple-600 transition duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20v-2c0-.523-.213-1.018-.588-1.393M13 12h5m-2 2l-2-2m2 2l2-2m-2-2V9a2 2 0 00-2-2H7a2 2 0 00-2 2v4a2 2 0 002 2h2m0 0h2"></path>
                            </svg>

                            <h3 class="text-xl font-bold mt-5 text-gray-900 dark:text-gray-100">Farm beitreten</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-sm mt-2">Treten Sie einer bestehenden Farm über einen Einladungscode bei.</p>
                        </a>
                        @endif

                        @if(!Auth::user()->isAdministrator())
                        {{-- 2. KARTE: Farm erstellen (ADMIN) --}}
                        <a href="#"
                            class="group bg-gray-50 dark:bg-gray-900 rounded-xl p-8 shadow-lg hover:shadow-2xl hover:scale-[1.03] transition duration-300 ease-in-out border border-transparent hover:border-green-600 dark:hover:border-green-500 flex flex-col items-center text-center min-h-[280px]">

                            {{-- Icon: Hinzufügen/Erstellen --}}
                            <svg class="w-16 h-16 text-green-500 group-hover:text-green-600 transition duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 3h-3m3 0h3m-6 3a9 9 0 1118 0M3 12a9 9 0 019-9"></path>
                            </svg>

                            <h3 class="text-xl font-bold mt-5 text-gray-900 dark:text-gray-100">Farm erstellen</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-sm mt-2">Neue Farm direkt anlegen und Administratoren zuweisen.</p>
                            <span class="text-xs mt-3 px-3 py-1 font-medium rounded-full bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300">ADMIN FUNKTION</span>
                        </a>
                        @endif

                        <a href="{{ route('farm.userInvite') }}"
                            class="group bg-gray-50 dark:bg-gray-900 rounded-xl p-8 shadow-lg hover:shadow-2xl hover:scale-[1.03] transition duration-300 ease-in-out border border-transparent hover:border-blue-600 dark:hover:border-blue-500 flex flex-col items-center text-center min-h-[280px]">

                            {{-- Icon: User Add --}}
                            <svg class="w-16 h-16 text-blue-500 group-hover:text-blue-600 transition duration-300"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 7a4 4 0 11-8 0 4 4 0 018 0zM6 21v-1a6 6 0 0112 0v1M18 8v4m2-2h-4">
                                </path>
                            </svg>

                            <h3 class="text-xl font-bold mt-5 text-gray-900 dark:text-gray-100">Benutzer einladen</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-sm mt-2">
                                Laden Sie neue Benutzer zur Ihrer Farm ein.
                            </p>
                        </a>


                        {{-- 3. KARTE: Farm verwalten --}}
                        <a href="{{route('farm.management')}}"
                            class="group bg-gray-50 dark:bg-gray-900 rounded-xl p-8 shadow-lg hover:shadow-2xl hover:scale-[1.03] transition duration-300 ease-in-out border border-transparent hover:border-yellow-600 dark:hover:border-yellow-500 flex flex-col items-center text-center min-h-[280px]">

                            {{-- Icon: Einstellungen/Verwaltung --}}
                            <svg class="w-16 h-16 text-yellow-500 group-hover:text-yellow-600 transition duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37a1.724 1.724 0 002.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>

                            <h3 class="text-xl font-bold mt-5 text-gray-900 dark:text-gray-100">Farm verwalten</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-sm mt-2">Bearbeiten Sie Farmdaten, verwalten Sie Nutzer und ändern Sie Einstellungen.</p>
                        </a>

                        {{-- 4. KARTE: Übersicht / Stats --}}
                        <a href="#"
                            class="group bg-gray-50 dark:bg-gray-900 rounded-xl p-8 shadow-lg hover:shadow-2xl hover:scale-[1.03] transition duration-300 ease-in-out border border-transparent hover:border-indigo-600 dark:hover:border-indigo-500 flex flex-col items-center text-center min-h-[280px]">

                            {{-- Icon: Statistiken --}}
                            <svg class="w-16 h-16 text-indigo-500 group-hover:text-indigo-600 transition duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0h6"></path>
                            </svg>

                            <h3 class="text-xl font-bold mt-5 text-gray-900 dark:text-gray-100">Übersicht & Statistiken</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-sm mt-2">Sehen Sie Echtzeit-Statistiken, Status und Leistungsdaten Ihrer Farmen.</p>
                        </a>



                        {{-- 6. KARTE: Farm-Historie / Logs --}}
                        <a href="#"
                            class="group bg-gray-50 dark:bg-gray-900 rounded-xl p-8 shadow-lg hover:shadow-2xl hover:scale-[1.03] transition duration-300 ease-in-out border border-transparent hover:border-gray-600 dark:hover:border-gray-500 flex flex-col items-center text-center min-h-[280px]">

                            {{-- Icon: Historie/Log --}}
                            <svg class="w-16 h-16 text-gray-500 group-hover:text-gray-600 transition duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>

                            <h3 class="text-xl font-bold mt-5 text-gray-900 dark:text-gray-100">Farm-Historie / Logs</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-sm mt-2">Überprüfen Sie alle vergangenen Ereignisse und Protokolle Ihrer Farm.</p>
                        </a>

                        <a href="{{ route('farm.toggleMode') }}"
                            class="group bg-gray-50 dark:bg-gray-900 rounded-xl p-8 shadow-lg hover:shadow-2xl 
                                hover:scale-[1.03] transition duration-300 ease-in-out border border-transparent 
                                hover:border-gray-600 dark:hover:border-gray-500 flex flex-col items-center 
                                text-center min-h-[280px] cursor-pointer">

                            {{-- Icon --}}
                            <svg class="w-16 h-16 text-gray-500 group-hover:text-gray-600 transition duration-300"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>

                            <h3 class="text-xl font-bold mt-5 text-gray-900 dark:text-gray-100">
                                Farm-Mode umschalten
                            </h3>

                            <p class="text-gray-600 dark:text-gray-400 text-sm mt-2">
                                Aktueller Status:
                                <strong class="text-gray-900 dark:text-gray-100">
                                    {{ session('farmMode') ? 'Aktiv' : 'Inaktiv' }}
                                </strong>
                            </p>
                        </a>


                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>