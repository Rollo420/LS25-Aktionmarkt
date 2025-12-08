<div class="py-12">
    <div class="max-w-7xl mx-auto grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

        <!-- 1. Farm anfragen -->
        <a href="#"
           class="bg-gray-800 rounded-xl p-6 shadow-lg hover:scale-105 transition transform duration-200 flex flex-col items-center w-[200px] h-[320px] mx-auto justify-center">
            <x-icon name="pencil" class="w-16 h-16 text-blue-400" />
            <h3 class="text-xl mt-4 text-gray-200">Farm anfragen</h3>
            <p class="text-gray-400 text-center text-sm mt-2">Stelle eine Anfrage mit Namensvorschlag & Beschreibung.</p>
        </a>

        <!-- 2. Farm erstellen (Admin) -->
        @if(Auth::user()->is_admin ?? false)
            <a href="#"
               class="bg-gray-800 rounded-xl p-6 shadow-lg hover:scale-105 transition transform duration-200 flex flex-col items-center w-[200px] h-[320px] mx-auto justify-center">
                <x-icon name="plus-circle" class="w-16 h-16 text-green-400" />
                <h3 class="text-xl mt-4 text-gray-200">Farm erstellen</h3>
                <p class="text-gray-400 text-center text-sm mt-2">Neue Farm anlegen und Benutzer zuordnen.</p>
            </a>
        @endif

        <!-- 3. Farm verwalten -->
        @if(Auth::user()->is_admin ?? false || Auth::user()->farm_id)
            <a href="#"
               class="bg-gray-800 rounded-xl p-6 shadow-lg hover:scale-105 transition transform duration-200 flex flex-col items-center w-[200px] h-[320px] mx-auto justify-center">
                <x-icon name="cog" class="w-16 h-16 text-yellow-400" />
                <h3 class="text-xl mt-4 text-gray-200">Farm verwalten</h3>
                <p class="text-gray-400 text-center text-sm mt-2">Farmdaten bearbeiten, Nutzer verwalten.</p>
            </a>
        @endif

        <!-- 4. Farm beitreten -->
        <a href="#"
           class="bg-gray-800 rounded-xl p-6 shadow-lg hover:scale-105 transition transform duration-200 flex flex-col items-center w-[200px] h-[320px] mx-auto justify-center">
            <x-icon name="user-group" class="w-16 h-16 text-purple-400" />
            <h3 class="text-xl mt-4 text-gray-200">Farm beitreten</h3>
            <p class="text-gray-400 text-center text-sm mt-2">Tritt einer bestehenden Farm bei.</p>
        </a>

        <!-- 13. Übersicht / Stats -->
        <a href="#"
           class="bg-gray-800 rounded-xl p-6 shadow-lg hover:scale-105 transition transform duration-200 flex flex-col items-center w-[200px] h-[320px] mx-auto justify-center">
            <x-icon name="chart-bar" class="w-16 h-16 text-indigo-400" />
            <h3 class="text-xl mt-4 text-gray-200">Übersicht</h3>
            <p class="text-gray-400 text-center text-sm mt-2">Statistiken & Status deiner Farmen anzeigen.</p>
        </a>

        <!-- Platz für weitere Cards -->
        <!-- <a href="#" class="...">...</a> -->

    </div>
</div>