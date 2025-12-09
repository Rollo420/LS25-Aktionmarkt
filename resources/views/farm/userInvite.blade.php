<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            {{ __('Benutzer einladen') }}
        </h2>
    </x-slot>

    <div class="min-h-screen px-6 py-16 flex flex-col items-center">

        <div class="w-full max-w-2xl space-y-8">

            {{-- Titel --}}
            <div class="text-center space-y-2">
                <h1 class="text-3xl font-extrabold text-gray-200 tracking-wide">
                    Benutzer einladen
                </h1>
                <p class="text-gray-400">
                    Fügen Sie neue Nutzer für Ihre Farm oder Verwaltung hinzu.
                </p>
            </div>

            {{-- Form --}}
            <form method="POST" action="{{ route('farm.sendUserInvite') }}" x-data="{ invites: [], searchResults: [], query: '' }"
                x-on:search-filter.window="query = $event.detail.query; searchResults = $event.detail.results;"
                x-on:search-selected.window="invites.push($event.detail); searchResults = []; query = ''"
                class="space-y-4 w-full">
                @csrf

                {{-- Search Component --}}
                <div class="w-full">
                    <x-search-input
                        api="{{ route('api.search.users') }}"
                        placeholder="User suchen..."
                        display="name" />
                </div>

                {{-- Dropdown Ergebnisse --}}
                <template x-if="searchResults.length > 0">
                    <div class="bg-gray-800/90 text-gray-100 shadow-lg rounded-xl p-4 space-y-2 max-h-64 overflow-y-auto">
                        <template x-for="item in searchResults" :key="item.id">
                            <div
                                class="cursor-pointer p-2 rounded hover:bg-indigo-600/50 transition flex justify-between items-center"
                                @click="$dispatch('search-selected', item)">
                                <span x-text="item.name"></span>
                                <span class="text-gray-400 text-sm" x-text="item.email"></span>
                            </div>
                        </template>
                    </div>
                </template>

                {{-- Eingeladene User --}}
                <div class="space-y-2">
                    <h3 class="font-bold text-lg text-gray-200">Eingeladene User</h3>

                    <template x-if="invites.length === 0">
                        <p class="text-gray-500">Noch keine User eingeladen.</p>
                    </template>

                    <template x-for="user in invites" :key="user.id">
                        <div class="p-3 bg-gray-700/50 rounded-lg flex justify-between items-center transition hover:bg-gray-700/70">
                            <span x-text="user.name" class="text-gray-100"></span>
                            <button type="button"
                                class="text-red-500 text-sm hover:text-red-400"
                                @click="invites = invites.filter(i => i.id !== user.id)">
                                Entfernen
                            </button>
                            {{-- Hidden Input für Form --}}
                            <input type="hidden" :name="'invites[]'" :value="user.id">
                        </div>
                    </template>
                </div>

                {{-- Submit Button --}}
                <div class="mt-6">
                    <button type="submit"
                        class="w-full py-4 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-lg 
                               shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 transition-all duration-300">
                        Einladungen senden
                    </button>
                </div>

                {{-- Info --}}
                <p class="text-center text-gray-400 text-sm mt-4">
                    Der eingeladene Benutzer erhält einen Aktivierungslink per E-Mail.
                </p>

            </form>
        </div>

    </div>
</x-app-layout>