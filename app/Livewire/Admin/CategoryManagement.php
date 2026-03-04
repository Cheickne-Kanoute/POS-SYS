<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use Livewire\Component;

class CategoryManagement extends Component
{
    public string $name = '';
    public bool $showModal = false;
    public bool $isEditing = false;
    public ?int $editingId = null;

    protected $rules = [
        'name' => 'required|string|max:255|unique:categories,name',
    ];

    public function getCategoriesProperty()
    {
        return Category::withCount('products')->orderBy('name')->get();
    }

    public function openCreateModal(): void
    {
        $this->name = '';
        $this->isEditing = false;
        $this->editingId = null;
        $this->showModal = true;
        $this->resetValidation();
    }

    public function openEditModal(int $id): void
    {
        $cat = Category::findOrFail($id);
        $this->editingId = $id;
        $this->name = $cat->name;
        $this->isEditing = true;
        $this->showModal = true;
        $this->resetValidation();
    }

    public function save(): void
    {
        $rules = $this->rules;
        if ($this->isEditing) {
            $rules['name'] = 'required|string|max:255|unique:categories,name,' . $this->editingId;
        }
        $this->validate($rules);

        if ($this->isEditing) {
            Category::findOrFail($this->editingId)->update(['name' => $this->name]);
            session()->flash('success', 'Category updated.');
        } else {
            Category::create(['name' => $this->name]);
            session()->flash('success', 'Category created.');
        }

        $this->showModal = false;
        $this->name = '';
    }

    public function delete(int $id): void
    {
        $cat = Category::findOrFail($id);
        if ($cat->products()->exists()) {
            session()->flash('error', 'Cannot delete category with products.');
            return;
        }
        $cat->delete();
        session()->flash('success', 'Category deleted.');
    }

    public function render()
    {
        return view('livewire.admin.category-management')
            ->layout('components.layouts.app');
    }
}
