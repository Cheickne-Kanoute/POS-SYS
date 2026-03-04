<div>
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-white">Categories</h1>
            <p class="text-slate-400 text-sm mt-1">Organize your products by category</p>
        </div>
        <button wire:click="openCreateModal"
            class="flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-medium rounded-xl transition-all text-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add Category
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse($this->categories as $category)
            <div
                class="bg-slate-900 border border-slate-800 rounded-2xl p-5 flex items-center justify-between group hover:border-slate-700 transition-colors">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-indigo-600/20 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-white">{{ $category->name }}</p>
                        <p class="text-xs text-slate-500 mt-0.5">{{ $category->products_count }} product(s)</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button wire:click="openEditModal({{ $category->id }})"
                        class="p-1.5 bg-slate-800 hover:bg-slate-700 text-slate-400 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </button>
                    <button wire:click="delete({{ $category->id }})" wire:confirm="Delete this category?"
                        class="p-1.5 bg-red-900/40 hover:bg-red-800/50 text-red-400 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center text-slate-500 text-sm">No categories yet. Create one!</div>
        @endforelse
    </div>

    @if($showModal)
        <div class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-slate-900 border border-slate-700 rounded-2xl w-full max-w-sm shadow-2xl">
                <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between">
                    <h2 class="text-base font-semibold text-white">{{ $isEditing ? 'Edit Category' : 'New Category' }}</h2>
                    <button wire:click="$set('showModal', false)" class="text-slate-500 hover:text-slate-300"><svg
                            class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg></button>
                </div>
                <div class="p-6">
                    <label class="block text-sm font-medium text-slate-300 mb-1.5">Category Name <span
                            class="text-red-400">*</span></label>
                    <input wire:model="name" type="text" placeholder="e.g. Beverages"
                        class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                    @error('name') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                </div>
                <div class="px-6 pb-5 flex gap-3">
                    <button wire:click="$set('showModal', false)"
                        class="flex-1 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-medium rounded-xl text-sm transition-colors">Cancel</button>
                    <button wire:click="save"
                        class="flex-1 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-medium rounded-xl text-sm transition-all">Save</button>
                </div>
            </div>
        </div>
    @endif
</div>