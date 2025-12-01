@props([
'api' => '',
'placeholder' => 'Suchen...',
'display' => 'name'
])


<div x-data="searchInput('{{ $api }}', '{{ $display }}')" class="relative w-full">

    <!-- Search Box: Cooler Farbverlauf, stark abgerundet und mit Hover-Effekten -->
    <div class="relative flex items-center p-4 pl-6 transition duration-300 transform 
                !bg-gradient-to-r !from-blue-600 !via-indigo-500 !to-purple-600
                !rounded-full !shadow-xl !shadow-indigo-500/20
                hover:!shadow-indigo-500/40 focus-within:!scale-[1.01]
                focus-within:!ring-4 focus-within:!ring-indigo-300/50">

        <!-- Icon in Weiß für Kontrast -->
        <svg class="w-6 h-6 mr-4 flex-shrink-0 !text-white"
            fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>

        <!-- Input mit weißem Text und Placeholder -->
        <input
            x-model="query"
            {{-- Entprellung für API-Aufrufe --}}
            x-on:input.debounce.300ms="search()"
            type="text"
            placeholder="{{ $placeholder }}"
            class="w-full bg-transparent focus:outline-none 
                   !text-white !placeholder-indigo-100 
                   text-lg tracking-wide font-medium">
    </div>

    <!-- Dropdown Results -->
    <template x-if="results.length > 0 && query.length > 1">
        <ul class="absolute left-0 right-0 mt-4 bg-white dark:!bg-gray-800 
                   !shadow-2xl !rounded-3xl z-50 max-h-64 overflow-y-auto py-3 
                   custom-scrollbar border border-gray-100 dark:!border-gray-700
                   transition-opacity duration-300 ease-out"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 transform scale-95"
            x-transition:enter-end="opacity-100 transform scale-100">

            <template x-for="item in results" :key="item.id">
                <li
                    class="px-6 py-3 mx-2 my-1 hover:!bg-indigo-50 dark:hover:!bg-gray-700 
                           cursor-pointer transition duration-150 !rounded-2xl group"
                    @click="select(item)">
                    <div class="flex flex-col">
                        <span class="font-semibold text-gray-900 dark:!text-gray-100 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors"
                            x-text="item[display]"></span>

                        <span class="text-sm text-gray-500 dark:!text-gray-400"
                            x-show="item.email"
                            x-text="item.email"></span>
                    </div>
                </li>
            </template>

            <li x-if="results.length === 0" class="px-6 py-3 text-gray-500 dark:!text-gray-400 text-center">
                Keine Ergebnisse gefunden.
            </li>

        </ul>
    </template>
</div>

<!-- Das Alpine.js Skript (Logik bleibt unverändert) -->
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('searchInput', (api, display) => ({
            query: '',
            results: [],
            display,

            async search() {
                if (this.query.length < 2) {
                    this.results = [];
                    return;
                }

                try {
                    // ECHTER API CALL: Nutzt den übergebenen 'api'-Prop
                    const res = await fetch(api + "?q=" + encodeURIComponent(this.query));
                    this.results = await res.json();
                } catch (e) {
                    console.error("Search API Error:", e);
                }
            },

            select(item) {
                this.query = item[this.display];
                this.results = [];

                // Sendet das 'search-selected' Event
                window.dispatchEvent(new CustomEvent('search-selected', {
                    detail: item
                }));
            }
        }))
    })
</script>