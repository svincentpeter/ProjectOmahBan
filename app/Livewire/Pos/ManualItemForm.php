<?php

namespace App\Livewire\Pos;

use Livewire\Component;

class ManualItemForm extends Component
{
    public $name;
    public $price;

    protected $rules = [
        'name' => 'required|string|max:255',
        'price' => 'required|numeric|min:1',
    ];

    public function render()
    {
        return view('livewire.pos.manual-item-form');
    }

    public function addToCart()
    {
        $this->validate();

        \Cart::add([
            'id' => 'manual_' . time(), // ID unik untuk item manual
            'name' => $this->name,
            'price' => $this->price,
            'quantity' => 1,
            'attributes' => [
                'source_type' => 'manual', // INI KUNCINYA!
            ]
        ]);

        $this->reset(['name', 'price']); // Kosongkan form setelah berhasil
        $this->emit('cartUpdated');
        $this->dispatchBrowserEvent('showSuccess', ['message' => 'Item manual ditambahkan!']);
    }
}