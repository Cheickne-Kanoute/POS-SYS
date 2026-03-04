<div>
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-white">Product Management</h1>
            <p class="text-slate-400 text-sm mt-1">Manage your product catalog</p>
        </div>
        <button wire:click="openCreateModal"
            class="flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-medium rounded-xl transition-all text-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add Product
        </button>
    </div>

    {{-- Filters --}}
    <div class="flex gap-3 mb-6">
        <div class="relative flex-1 max-w-sm">
            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500" fill="none"
                viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input wire:model.live.debounce.250ms="search" type="search" placeholder="Search products or barcode..."
                class="w-full pl-10 pr-4 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
        </div>
        <select wire:model.live="filterCategory"
            class="px-4 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="">All Categories</option>
            @foreach($this->categories as $cat)
                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
            @endforeach
        </select>
    </div>

    {{-- Table --}}
    <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden">
        <table class="w-full">
            <thead>
                <tr class="border-b border-slate-800">
                    <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        Product</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        Barcode</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        Category</th>
                    <th class="text-right px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Price
                    </th>
                    <th class="text-right px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Stock
                    </th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Status
                    </th>
                    <th class="text-right px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800">
                @forelse($this->products as $product)
                    <tr class="hover:bg-slate-800/50 transition-colors">
                        <td class="px-6 py-4">
                            <p class="text-sm font-medium text-white">{{ $product->name }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-500 font-mono">{{ $product->barcode ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-slate-400">{{ $product->category?->name ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm font-semibold text-white text-right">
                            {{ number_format($product->price, 0, '.', ' ') }} F</td>
                        <td class="px-6 py-4 text-right">
                            <span @class(['text-sm font-semibold', 'text-emerald-400' => $product->stock > 5, 'text-amber-400' => $product->stock > 0 && $product->stock <= 5, 'text-red-400' => $product->stock <= 0])>
                                {{ $product->stock }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span @class(['text-xs px-2.5 py-1 rounded-full font-semibold', 'bg-emerald-900/40 text-emerald-400' => $product->is_active, 'bg-red-900/40 text-red-400' => !$product->is_active])>
                                {{ $product->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2 justify-end">
                                <button wire:click="openEditModal({{ $product->id }})"
                                    class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-medium rounded-lg transition-colors">Edit</button>
                                <button wire:click="toggleActive({{ $product->id }})"
                                    class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors {{ $product->is_active ? 'bg-red-900/40 hover:bg-red-800/50 text-red-400' : 'bg-emerald-900/40 hover:bg-emerald-800/50 text-emerald-400' }}">
                                    {{ $product->is_active ? 'Disable' : 'Enable' }}
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-500 text-sm">No products found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4 border-t border-slate-800">
            {{ $this->products->links() }}
        </div>
    </div>

    {{-- Modal --}}
    @if($showModal)
        <div class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-slate-900 border border-slate-700 rounded-2xl w-full max-w-md shadow-2xl">
                <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between">
                    <h2 class="text-base font-semibold text-white">{{ $isEditing ? 'Edit Product' : 'Add Product' }}</h2>
                    <button wire:click="closeModal" class="text-slate-500 hover:text-slate-300"><svg class="w-5 h-5"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg></button>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1.5">Product Name <span
                                class="text-red-400">*</span></label>
                        <input wire:model="name" type="text" placeholder="Enter product name"
                            class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                        @error('name') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Barcode</label>
                            <input wire:model="barcode" type="text" placeholder="e.g. 1234567890"
                                class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm font-mono">
                            @error('barcode') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Category</label>
                            <select wire:model="category_id"
                                class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                                <option value="">Select...</option>
                                @foreach($this->categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Price (F) <span
                                    class="text-red-400">*</span></label>
                            <input wire:model="price" type="number" min="0" step="1" placeholder="0"
                                class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                            @error('price') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Stock <span
                                    class="text-red-400">*</span></label>
                            <input wire:model="stock" type="number" min="0" placeholder="0"
                                class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                            @error('stock') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <input wire:model="is_active" type="checkbox" id="product_active"
                            class="w-4 h-4 rounded bg-slate-800 border-slate-600 text-indigo-600 focus:ring-indigo-500">
                        <label for="product_active" class="text-sm text-slate-300">Active (visible in POS)</label>
                    </div>
                </div>
                <div class="px-6 pb-5 flex gap-3">
                    <button wire:click="closeModal"
                        class="flex-1 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-medium rounded-xl text-sm transition-colors">Cancel</button>
                    <button wire:click="save"
                        class="flex-1 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-medium rounded-xl text-sm transition-all"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove>{{ $isEditing ? 'Update' : 'Create' }}</span>
                        <span wire:loading>Saving...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>