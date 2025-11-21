<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight flex flex-col gap-4">
            <span class="flex items-center gap-2">
                <svg class="w-8 h-7 text-indigo-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.657 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                {{ __('User bearbeiten') }} <span class="ml-1 text-indigo-600">{{ $user->name }}</span>
            </span>
            <!-- Aktuelle Rollen als kleine Badges -->
            <div class="flex flex-wrap gap-1 mt-1">
                @forelse($user->roles as $role)
                <span class="inline-flex items-center px-3 py-1 text-base font-medium bg-indigo-100 text-indigo-700 rounded shadow border border-indigo-200">
                    {{ $role->name }}
                </span>
                @empty
                <span class="inline-flex items-center px-3 py-1 text-base font-medium bg-gray-100 text-gray-500 rounded border border-gray-200">
                    Keine Rolle
                </span>
                @endforelse
            </div>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <!-- Größerer Schatten und gerundet für modernen Look -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-2xl sm:rounded-xl">
                <div class="p-6 lg:p-8 text-gray-900 dark:text-gray-100">

                    @if ($errors->any())
                    <!-- Fehlerbox mit mehr Abstand und Schatten -->
                    <div class="mb-8 p-4 bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-300 rounded-xl shadow-xl animate-pulse">
                        <strong class="font-extrabold text-lg">{{ __('Fehler:') }}</strong>
                        <ul class="mt-2 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <!-- Erhöhte Abstände im Formular (space-y-8) -->
                    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-8">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Name -->
                            <div class="space-y-1">
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ __('Name') }}
                                </label>
                                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                                    class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100 text-base transition duration-150 @error('name') border-red-500 @enderror">
                                @error('name')
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="space-y-1">
                                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ __('E-Mail') }}
                                </label>
                                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                                    class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100 text-base transition duration-150 @error('email') border-red-500 @enderror">
                                @error('email')
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Role Selection (Visuelle Liste) -->
                        <div class="col-span-full pt-4">
                            <label class="block text-lg font-semibold text-indigo-700 dark:text-indigo-300 mb-3">
                                {{ __('Rollen zuweisen') }}
                            </label>
                            <div class="border border-indigo-100 dark:border-indigo-700 rounded-lg bg-gray-50 dark:bg-gray-900 grid grid-cols-2 sm:grid-cols-3" style="gap: 7.56px; padding: 8px;">
                                @php
                                    $selectedRoleIds = collect(old('role_ids', $user->roles->pluck('id')->toArray()))->toArray();
                                    if (empty($selectedRoleIds)) {
                                        $defaultRole = \App\Models\Role::where('name', 'default user')->first();
                                        if ($defaultRole) {
                                            $selectedRoleIds = [$defaultRole->id];
                                        }
                                    }
                                @endphp
                                @foreach ($roles as $role)
                                <label class="cursor-pointer block relative transition-all duration-200">
                                    <input type="checkbox" name="role_ids[]" value="{{ $role->id }}"
                                        {{ in_array($role->id, $selectedRoleIds) ? 'checked' : '' }}
                                        class="sr-only peer">
                                    <div class="px-3 py-2 rounded text-center border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 text-sm font-medium shadow-sm hover:shadow-lg hover:scale-101 hover:bg-indigo-100 dark:hover:bg-indigo-600 transition-all duration-150 peer-checked:bg-indigo-500 peer-checked:text-white peer-checked:border-indigo-700">
                                        {{ $role->name }}
                                    </div>
                                </label>
                                @endforeach
                            </div>
                            @error('role_ids')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Action Buttons and Delete Modal -->
                        <div class="flex items-center justify-between pt-6 gap-2 border-t border-gray-100 dark:border-gray-700">
                            <!-- Delete Button & Alpine Modal -->
                            <div x-data="{ deleteOpen: false }">
                                <button type="button" @click="deleteOpen = true" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-lg text-sm font-semibold text-white shadow hover:bg-red-700 transition-colors duration-150 flex-shrink-0">
                                    {{ __('User löschen') }}
                                </button>
                                <!-- Delete Modal (vollständig) -->
                                <div x-show="deleteOpen" class="fixed inset-0 flex items-center justify-center z-50 transition-opacity" x-transition.opacity style="background: rgba(30,41,59,0.25); display: none;">
                                    <div @click.away="deleteOpen = false" class="bg-white dark:bg-gray-800 rounded-lg shadow-2xl w-96 mx-2 transform transition-all p-6 border border-red-200 dark:border-red-700" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
                                        <h3 class="text-lg font-bold text-red-600 dark:text-red-400 mb-3 text-center">
                                            {{ __('User wirklich löschen?') }}
                                        </h3>
                                        <p class="text-base text-gray-600 dark:text-gray-400 mb-6 text-center">
                                            {{ __('Diese Aktion kann nicht rückgängig gemacht werden.') }}
                                        </p>
                                        <div class="flex gap-4 justify-center">
                                            <button type="button" @click="deleteOpen = false" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg font-semibold hover:bg-gray-300 dark:hover:bg-gray-600 transition text-base">
                                                {{ __('Abbrechen') }}
                                            </button>
                                            <button type="button" @click="document.getElementById('delete-user-{{ $user->id }}').submit()" class="px-4 py-2 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 shadow text-base">
                                                {{ __('Ja, löschen') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Save and Cancel Buttons -->
                            <div class="flex gap-2">
                                <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-lg text-sm font-semibold text-gray-700 shadow hover:bg-gray-300 transition-colors duration-150">
                                    {{ __('Abbrechen') }}
                                </a>
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg text-sm font-semibold text-white shadow hover:bg-indigo-500 transition-colors duration-150">
                                    {{ __('Speichern') }}
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- External delete form for user (avoids nested forms) -->
                    <form id="delete-user-{{ $user->id }}" method="POST" action="{{ route('admin.users.destroy', $user) }}" style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>