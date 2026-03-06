<div>
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-white">Gestion des Produits</h1>
            <p class="text-slate-400 text-sm mt-1">Gérez votre catalogue de produits</p>
        </div>
        <button wire:click="openCreateModal"
            class="flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-medium rounded-xl transition-all text-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Ajouter un produit
        </button>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-emerald-900/40 border border-emerald-700 rounded-xl text-emerald-300 text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Filters --}}
    <div class="flex gap-3 mb-6">
        <div class="relative flex-1 max-w-sm">
            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500" fill="none"
                viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input wire:model.live.debounce.250ms="search" type="search"
                placeholder="Rechercher par nom ou code-barres..."
                class="w-full pl-10 pr-4 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
        </div>
        <select wire:model.live="filterCategory"
            class="px-4 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="">Toutes les catégories</option>
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
                        Produit</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        Code-barres</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        Catégorie</th>
                    <th class="text-right px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Prix
                    </th>
                    <th class="text-right px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Stock
                    </th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Statut
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
                        <td class="px-6 py-4">
                            @if($product->barcode)
                                <div class="flex flex-col gap-1">
                                    {{-- Barcode SVG (fond blanc pour lisibilité) --}}
                                    <div class="bg-white rounded-md px-2 py-1 inline-block">
                                        {!! $this->getProductBarcodeSvg($product) !!}
                                    </div>
                                    <span class="text-[10px] text-slate-500 font-mono">{{ $product->barcode }}</span>
                                </div>
                            @else
                                <button wire:click="generateBarcodeForProduct({{ $product->id }})"
                                    class="flex items-center gap-1.5 px-2.5 py-1.5 bg-slate-800 hover:bg-indigo-900/40 hover:border-indigo-500 border border-slate-700 rounded-lg text-slate-400 hover:text-indigo-300 text-xs font-medium transition-all">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4" />
                                    </svg>
                                    Générer
                                </button>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-400">{{ $product->category?->name ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm font-semibold text-white text-right">
                            {{ number_format($product->price, 0, '.', ' ') }} F
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span @class(['text-sm font-semibold', 'text-emerald-400' => $product->stock > 5, 'text-amber-400' => $product->stock > 0 && $product->stock <= 5, 'text-red-400' => $product->stock <= 0])>
                                {{ $product->stock }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span @class(['text-xs px-2.5 py-1 rounded-full font-semibold', 'bg-emerald-900/40 text-emerald-400' => $product->is_active, 'bg-red-900/40 text-red-400' => !$product->is_active])>
                                {{ $product->is_active ? 'Actif' : 'Inactif' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2 justify-end">
                                <button wire:click="openEditModal({{ $product->id }})"
                                    class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-medium rounded-lg transition-colors">Modifier</button>
                                <button wire:click="toggleActive({{ $product->id }})"
                                    class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors {{ $product->is_active ? 'bg-red-900/40 hover:bg-red-800/50 text-red-400' : 'bg-emerald-900/40 hover:bg-emerald-800/50 text-emerald-400' }}">
                                    {{ $product->is_active ? 'Désactiver' : 'Activer' }}
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-500 text-sm">Aucun produit trouvé.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4 border-t border-slate-800">
            {{ $this->products->links() }}
        </div>
    </div>

    {{-- Modal Create/Edit --}}
    @if($showModal)
        <div class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-slate-900 border border-slate-700 rounded-2xl w-full max-w-md shadow-2xl">
                <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between">
                    <h2 class="text-base font-semibold text-white">
                        {{ $isEditing ? 'Modifier le produit' : 'Ajouter un produit' }}</h2>
                    <button wire:click="closeModal" class="text-slate-500 hover:text-slate-300"><svg class="w-5 h-5"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg></button>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1.5">Nom du produit <span
                                class="text-red-400">*</span></label>
                        <input wire:model="name" type="text" placeholder="Entrez le nom du produit"
                            class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                        @error('name') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                    </div>

                    {{-- Barcode field with generate button --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1.5">
                            Code-barres
                            <span class="text-slate-500 text-xs font-normal ml-1">(auto-généré si vide)</span>
                        </label>
                        <div class="flex gap-2">
                            <input wire:model.live="barcode" type="text" placeholder="ex: 1234567890128"
                                class="flex-1 px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm font-mono">
                            <button wire:click="generateBarcode" type="button"
                                class="px-3 py-2.5 bg-indigo-900/50 hover:bg-indigo-800/60 border border-indigo-700 text-indigo-300 rounded-xl text-xs font-medium transition-all flex items-center gap-1.5 whitespace-nowrap">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Générer
                            </button>
                        </div>
                        @error('barcode') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror

                        {{-- Barcode preview --}}
                        @if($this->barcodeSvg)
                            <div class="mt-3 p-3 bg-white rounded-xl inline-block">
                                {!! $this->barcodeSvg !!}
                            </div>
                            <p class="mt-1 text-[10px] text-slate-500 font-mono">{{ $barcode }}</p>
                        @endif
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Catégorie</label>
                            <select wire:model="category_id"
                                class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                                <option value="">Sélectionner...</option>
                                @foreach($this->categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Prix (F) <span
                                    class="text-red-400">*</span></label>
                            <input wire:model="price" type="number" min="0" step="1" placeholder="0"
                                class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                            @error('price') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1.5">Stock <span
                                class="text-red-400">*</span></label>
                        <input wire:model="stock" type="number" min="0" placeholder="0"
                            class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                        @error('stock') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex items-center gap-2">
                        <input wire:model="is_active" type="checkbox" id="product_active"
                            class="w-4 h-4 rounded bg-slate-800 border-slate-600 text-indigo-600 focus:ring-indigo-500">
                        <label for="product_active" class="text-sm text-slate-300">Actif (visible dans le POS)</label>
                    </div>
                </div>
                <div class="px-6 pb-5 flex gap-3">
                    <button wire:click="closeModal"
                        class="flex-1 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-medium rounded-xl text-sm transition-colors">Annuler</button>
                    <button wire:click="save"
                        class="flex-1 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-medium rounded-xl text-sm transition-all"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove>{{ $isEditing ? 'Mettre à jour' : 'Créer' }}</span>
                        <span wire:loading>Enregistrement...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>