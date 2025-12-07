<div class="flex h-full w-full flex-1 flex-col gap-6 p-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-neutral-900 dark:text-white">
                🛡️ Zarządzanie Rolami
            </h1>
            <p class="text-neutral-600 dark:text-neutral-400 mt-1">
                Twórz i edytuj role użytkowników oraz przypisuj im uprawnienia
            </p>
        </div>
        @can('roles.create')
        <button wire:click="openModal" class="px-4 py-2 bg-neutral-900 dark:bg-white text-white dark:text-neutral-900 rounded-lg font-medium hover:opacity-90 transition">
            + Dodaj Rolę
        </button>
        @endcan
    </div>

    <!-- Lista ról -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($roles as $role)
        <div class="bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700 p-4">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">{{ $role->name }}</h3>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">
                        {{ $role->users_count ?? 0 }} użytkowników
                    </p>
                </div>
                <div class="flex gap-2">
                    @can('roles.edit')
                    <button wire:click="editRole({{ $role->id }})" class="text-neutral-600 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </button>
                    @endcan
                    @can('roles.delete')
                    @if(!in_array($role->name, ['admin', 'student', 'instruktor', 'wychowawca', 'koordynator']))
                    <button wire:click="deleteRole({{ $role->id }})" class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                    @endif
                    @endcan
                </div>
            </div>
            
            <div class="space-y-1">
                <p class="text-xs font-semibold text-neutral-700 dark:text-neutral-300 mb-2">Uprawnienia ({{ $role->permissions->count() }})</p>
                <div class="flex flex-wrap gap-1">
                    @forelse($role->permissions->take(5) as $permission)
                    <span class="inline-block px-2 py-0.5 bg-neutral-100 dark:bg-neutral-800 text-neutral-700 dark:text-neutral-300 text-xs rounded">
                        {{ $permission->name }}
                    </span>
                    @empty
                    <span class="text-xs text-neutral-400 dark:text-neutral-500 italic">Brak uprawnień</span>
                    @endforelse
                    @if($role->permissions->count() > 5)
                    <span class="text-xs text-neutral-500 dark:text-neutral-400">+{{ $role->permissions->count() - 5 }} więcej</span>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Modal -->
    @if($showModal)
    <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-neutral-900 rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-neutral-200 dark:border-neutral-700">
                <h2 class="text-xl font-bold text-neutral-900 dark:text-white">
                    {{ $editMode ? 'Edytuj Rolę' : 'Nowa Rola' }}
                </h2>
            </div>
            
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-neutral-900 dark:text-white mb-2">Nazwa roli</label>
                    <input wire:model="roleName" type="text" class="w-full px-4 py-2 bg-white dark:bg-neutral-800 border border-neutral-300 dark:border-neutral-600 rounded-lg text-neutral-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-neutral-500">
                    @error('roleName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-neutral-900 dark:text-white mb-2">Uprawnienia</label>
                    <div class="grid grid-cols-2 gap-2 max-h-64 overflow-y-auto p-2 bg-neutral-50 dark:bg-neutral-800 rounded-lg">
                        @foreach($permissions as $permission)
                        <label class="flex items-center gap-2 p-2 hover:bg-neutral-100 dark:hover:bg-neutral-700 rounded cursor-pointer">
                            <input type="checkbox" wire:model="rolePermissions" value="{{ $permission->name }}" class="rounded border-neutral-300 dark:border-neutral-600">
                            <span class="text-sm text-neutral-900 dark:text-white">{{ $permission->name }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="p-6 border-t border-neutral-200 dark:border-neutral-700 flex justify-end gap-2">
                <button wire:click="closeModal" class="px-4 py-2 bg-neutral-200 dark:bg-neutral-700 text-neutral-900 dark:text-white rounded-lg hover:opacity-90">
                    Anuluj
                </button>
                <button wire:click="{{ $editMode ? 'updateRole' : 'createRole' }}" class="px-4 py-2 bg-neutral-900 dark:bg-white text-white dark:text-neutral-900 rounded-lg hover:opacity-90">
                    {{ $editMode ? 'Zapisz' : 'Utwórz' }}
                </button>
            </div>
        </div>
    </div>
    @endif
</div>

<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('role-created', () => {
            Swal.fire({
                icon: 'success',
                title: 'Sukces!',
                text: 'Rola została utworzona',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
        });
        
        Livewire.on('role-updated', () => {
            Swal.fire({
                icon: 'success',
                title: 'Sukces!',
                text: 'Rola została zaktualizowana',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
        });
        
        Livewire.on('role-deleted', () => {
            Swal.fire({
                icon: 'success',
                title: 'Sukces!',
                text: 'Rola została usunięta',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
        });
    });
</script>
