<div class="flex h-full w-full flex-1 flex-col gap-6 p-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-neutral-900 dark:text-white">
                🔑 Zarządzanie Uprawnieniami
            </h1>
            <p class="text-neutral-600 dark:text-neutral-400 mt-1">
                Przeglądaj i zarządzaj uprawnieniami systemowymi
            </p>
        </div>
        @can('permissions.create')
        <button wire:click="openModal" class="px-4 py-2 bg-neutral-900 dark:bg-white text-white dark:text-neutral-900 rounded-lg font-medium hover:opacity-90 transition">
            + Dodaj Uprawnienie
        </button>
        @endcan
    </div>

    <!-- Pogrupowane uprawnienia -->
    @foreach($permissions as $group => $groupPermissions)
    <div class="bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700 overflow-hidden">
        <div class="px-4 py-3 bg-neutral-50 dark:bg-neutral-800 border-b border-neutral-200 dark:border-neutral-700">
            <h3 class="text-lg font-semibold text-neutral-900 dark:text-white capitalize">{{ $group }}</h3>
            <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ $groupPermissions->count() }} uprawnień</p>
        </div>
        
        <div class="divide-y divide-neutral-200 dark:divide-neutral-700">
            @foreach($groupPermissions as $permission)
            <div class="px-4 py-3 flex items-center justify-between hover:bg-neutral-50 dark:hover:bg-neutral-800 transition">
                <div class="flex-1">
                    <p class="font-medium text-neutral-900 dark:text-white">{{ $permission->name }}</p>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-xs text-neutral-500 dark:text-neutral-400">
                            Używane przez:
                        </span>
                        @php($rolesWithPermission = $roles->filter(fn($role) => $role->permissions->contains('id', $permission->id)))
                        @forelse($rolesWithPermission as $role)
                        <span class="inline-block px-2 py-0.5 bg-neutral-100 dark:bg-neutral-700 text-neutral-700 dark:text-neutral-300 text-xs rounded">
                            {{ $role->name }}
                        </span>
                        @empty
                        <span class="text-xs text-neutral-400 dark:text-neutral-500 italic">brak ról</span>
                        @endforelse
                    </div>
                </div>
                <div class="flex gap-2">
                    @can('permissions.edit')
                    <button wire:click="editPermission({{ $permission->id }})" class="text-neutral-600 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </button>
                    @endcan
                    @can('permissions.delete')
                    <button wire:click="deletePermission({{ $permission->id }})" wire:confirm="Czy na pewno chcesz usunąć to uprawnienie?" class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                    @endcan
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach

    <!-- Modal -->
    @if($showModal)
    <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-neutral-900 rounded-lg max-w-md w-full">
            <div class="p-6 border-b border-neutral-200 dark:border-neutral-700">
                <h2 class="text-xl font-bold text-neutral-900 dark:text-white">
                    {{ $editMode ? 'Edytuj Uprawnienie' : 'Nowe Uprawnienie' }}
                </h2>
            </div>
            
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-neutral-900 dark:text-white mb-2">Nazwa uprawnienia</label>
                    <input wire:model="permissionName" type="text" placeholder="np. users.create" class="w-full px-4 py-2 bg-white dark:bg-neutral-800 border border-neutral-300 dark:border-neutral-600 rounded-lg text-neutral-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-neutral-500">
                    @error('permissionName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">Używaj formatu: moduł.akcja (np. posts.create, users.edit)</p>
                </div>
            </div>

            <div class="p-6 border-t border-neutral-200 dark:border-neutral-700 flex justify-end gap-2">
                <button wire:click="closeModal" class="px-4 py-2 bg-neutral-200 dark:bg-neutral-700 text-neutral-900 dark:text-white rounded-lg hover:opacity-90">
                    Anuluj
                </button>
                <button wire:click="{{ $editMode ? 'updatePermission' : 'createPermission' }}" class="px-4 py-2 bg-neutral-900 dark:bg-white text-white dark:text-neutral-900 rounded-lg hover:opacity-90">
                    {{ $editMode ? 'Zapisz' : 'Utwórz' }}
                </button>
            </div>
        </div>
    </div>
    @endif
</div>

<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('permission-created', () => {
            Swal.fire({
                icon: 'success',
                title: 'Sukces!',
                text: 'Uprawnienie zostało utworzone',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
        });
        
        Livewire.on('permission-updated', () => {
            Swal.fire({
                icon: 'success',
                title: 'Sukces!',
                text: 'Uprawnienie zostało zaktualizowane',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
        });
        
        Livewire.on('permission-deleted', () => {
            Swal.fire({
                icon: 'success',
                title: 'Sukces!',
                text: 'Uprawnienie zostało usunięte',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
        });
    });
</script>
