<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;
use Picqer\Barcode\BarcodeGenerator;
use Picqer\Barcode\BarcodeGeneratorSVG;

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

    /**
     * Génère un code-barres EAN-13 unique.
     */
    private function generateUniqueBarcode(): string
    {
        do {
            // EAN-13: 12 chiffres + 1 checksum calculé automatiquement
            $body = str_pad(mt_rand(1, 999999999999), 12, '0', STR_PAD_LEFT);
            // Calcul du chiffre de contrôle EAN-13
            $sum = 0;
            for ($i = 0; $i < 12; $i++) {
                $sum += (int) $body[$i] * (($i % 2 === 0) ? 1 : 3);
            }
            $checkDigit = (10 - ($sum % 10)) % 10;
            $barcode = $body . $checkDigit;
        } while (Product::where('barcode', $barcode)->exists());

        return $barcode;
    }

    /**
     * Génère (ou régénère) un code-barres pour le produit en cours d'édition/création.
     */
    public function generateBarcode(): void
    {
        $this->barcode = $this->generateUniqueBarcode();
    }

    /**
     * Génère et persiste un code-barres pour un produit existant sans barcode.
     */
    public function generateBarcodeForProduct(int $productId): void
    {
        $product = Product::findOrFail($productId);
        if (!$product->barcode) {
            $product->update(['barcode' => $this->generateUniqueBarcode()]);
            session()->flash('success', 'Code-barres généré avec succès.');
        }
    }

    /**
     * Retourne le SVG du code-barres courant dans le formulaire.
     */
    public function getBarcodeSvgProperty(): string
    {
        if (strlen($this->barcode) < 8) {
            return '';
        }
        $generator = new BarcodeGeneratorSVG();
        return $generator->getBarcode(
            $this->barcode,
            BarcodeGenerator::TYPE_EAN_13,
            2,
            60,
            'black'
        );
    }

    /**
     * Retourne le SVG du code-barres pour un produit donné.
     */
    public function getProductBarcodeSvg(Product $product): string
    {
        if (!$product->barcode || strlen($product->barcode) < 8) {
            return '';
        }
        $generator = new BarcodeGeneratorSVG();
        return $generator->getBarcode(
            $product->barcode,
            BarcodeGenerator::TYPE_EAN_13,
            1.5,
            50,
            'black'
        );
    }

    public function save(): void
    {
        // Auto-génère un barcode si le champ est vide (création uniquement)
        if (!$this->isEditing && empty($this->barcode)) {
            $this->barcode = $this->generateUniqueBarcode();
        }

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
            session()->flash('success', 'Produit mis à jour avec succès.');
        } else {
            Product::create($data);
            session()->flash('success', 'Produit créé avec succès.');
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
        /** @var \Illuminate\View\View $view */
        $view = view('livewire.admin.product-management');

        return $view->layout('components.layouts.app');
    }
}
