<?php

namespace App\Livewire\Pos;

use Livewire\Component;

class Filter extends Component
{
    public $categories;
    public $brands; // Tambahkan ini
    public $category = '';
    public $brand = ''; // Tambahkan ini
    public $showCount = 9;

    public function mount($categories, $brands) // Tambahkan parameter brands
    {
        $this->categories = $categories;
        $this->brands = $brands; // Inisialisasi brands
    }

    public function render() 
    {
        return view('livewire.pos.filter');
    }

    public function updatedCategory() 
    {
        $this->dispatch('selectedCategory', $this->category);
    }

    public function updatedBrand() // Method baru untuk brand
    {
        $this->dispatch('selectedBrand', $this->brand);
    }

    public function updatedShowCount() 
    {
        $this->dispatch('showCount', $this->showCount);
    }
}
