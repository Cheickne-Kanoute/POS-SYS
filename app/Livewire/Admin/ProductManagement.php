<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class ProductManagement extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterCategory = '';
    public bool $showModal = false;
    public bool $isEditing = false;
    public ?int $editingId = null;

    // Form fields
    public string $name = '';
    public string $barcode = '';
    public string $price = '';
    public string $stock = '';
    public string $category_id = '';
    public bool $is_active = true;

    protected function rules(): array
    {
        $barcodeRule = $this->isEditing
            ? 'nullable|string|unique:products,barcode,' . $this->editingId
            : 'nullable|string|unique:products,barcode';

        return [
            'name' => 'required|string|max:255',
            'barcode' => $barcodeRule,
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'is_active' => 'boolean',
        ];
    }

    public function getProductsProperty()
    {
        return Product::with('category')
            ->when($this->search, fn($q) => $q->search($this->search))
            ->when($this->filterCategory, fn($q) => $q->where('category_id', $this->filterCategory))
            ->orderBy('name')
            ->paginate(10);
    }

    public function getCategoriesProperty()
    {
        return Category::orderBy('name')->get();
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showModal = true;
        $this->isEditing = false;
    }

    public function openEditModal(int $productId): void
    {
        $product = Product::findOrFail($productId);
        $this->editingId = $productId;
        $this->name = $product->name;
        $this->barcode = $product->barcode ?? '';
        $this->price = (string) $product->price;
        $this->stock = (string) $product->stock;
        $this->category_id = (string) ($product->category_id ?? '');
        $this->is_active = $product->is_active;
        $this->isEditing = true;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'barcode' => $this->barcode ?: null,
            'price' => $this->price,
            'stock' => $this->stock,
            'category_id' => $this->category_id ?: null,
            'is_active' => $this->is_active,
        ];

        if ($this->isEditing) {
            Product::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'Product updated successfully.');
        } else {
            Product::create($data);
            session()->flash('success', 'Product created successfully.');
        }

        $this->closeModal();
    }

    public function toggleActive(int $productId): void
    {
        $product = Product::findOrFail($productId);
        $product->update(['is_active' => !$product->is_active]);
        session()->flash('success', 'Product status updated.');
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->isEditing = false;
        $this->name = '';
        $this->barcode = '';
        $this->price = '';
        $this->stock = '';
        $this->category_id = '';
        $this->is_active = true;
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.admin.product-management')
            ->layout('components.layouts.app');
    }
}
