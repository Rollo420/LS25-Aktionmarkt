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
                    this.filterStocks();
                    return;
                }

                try {
                    // ECHTER API CALL: Nutzt den übergebenen 'api'-Prop
                    const res = await fetch(api + "?q=" + encodeURIComponent(this.query));
                    this.results = await res.json();
                    this.filterStocks();
                } catch (e) {
                    console.error("Search API Error:", e);
                    this.results = [];
                    this.filterStocks();
                }
            },

            select(item) {
                this.query = item[this.display];
                this.results = [];

                // Sendet das 'search-selected' Event
                window.dispatchEvent(new CustomEvent('search-selected', {
                    detail: item
                }));
            },

            filterStocks() {
                // Sendet das 'search-filter' Event mit Query und Ergebnissen
                window.dispatchEvent(new CustomEvent('search-filter', {
                    detail: {
                        query: this.query,
                        results: this.results
                    }
                }));
            }
        }))
    })
</script>