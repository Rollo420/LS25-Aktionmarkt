{{-- Benutzer auswählen (Milisearch Live Search) --}}
<div class="mb-6" x-data="userSearch()">
    <label class="block mb-1 font-medium">Benutzer suchen</label>

    <div class="relative">
        <!-- Suchfeld -->
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>

        <input
            type="text"
            x-model="query"
            @input="searchUsers"
            placeholder="Benutzer suchen..."
            class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md
                   leading-5 bg-white dark:bg-gray-900 placeholder-gray-500 dark:placeholder-gray-400
                   focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
    </div>

    <!-- Ergebnisliste -->
    <div class="relative mt-2" x-show="results.length > 0">
        <div class="absolute z-10 w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-md shadow-lg">
            <template x-for="user in results" :key="user.id">
                <div
                    @click="selectUser(user)"
                    class="px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer flex justify-between items-center">
                    <span x-text="user.name"></span>
                    <span class="text-gray-500 text-sm" x-text="user.email"></span>
                </div>
            </template>
        </div>
    </div>

    <!-- Hidden Input für ausgewählten User -->
    <input type="hidden" name="user_id" x-model="selectedUserId">

    <!-- Auswahl-Tag -->
    <template x-if="selectedUser">
        <div class="mt-3 inline-flex items-center px-3 py-1 rounded-full bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200 text-sm">
            <span class="mr-2" x-text="selectedUser.name"></span>
            <button @click="clearSelection()" class="font-bold">×</button>
        </div>
    </template>
</div>

{{-- Alpine.js Search Script --}}
<script>
    function userSearch() {
        return {
            query: '',
            results: [],
            selectedUser: null,
            selectedUserId: null,

            searchUsers() {
                if (this.query.length < 2) {
                    this.results = [];
                    return;
                }

                // Milisearch Anfrage
                fetch(`/api/search/users?q=${this.query}`)
                    .then(res => res.json())
                    .then(data => {
                        this.results = data;
                    });
            },

            selectUser(user) {
                this.selectedUser = user;
                this.selectedUserId = user.id;
                this.results = [];
                this.query = user.name;
            },

            clearSelection() {
                this.selectedUser = null;
                this.selectedUserId = null;
                this.query = '';
            }
        }
    }
</script>