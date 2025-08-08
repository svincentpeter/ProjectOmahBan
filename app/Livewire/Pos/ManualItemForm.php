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

    \Cart::instance('sale')->add([
        'id'       => 'manual_' . time(),
        'name'     => $this->name,
        'qty'      => 1, // << UBAH 'quantity' MENJADI 'qty' DI SINI
        'price'    => $this->price,
        'weight'   => 0,
        'options' => [
            'source_type' => 'manual',
            'code'        => 'SRV-' . time(),
            'stock'       => 'N/A',
            'unit'        => 'pcs',
            'hpp'         => 0,
            'profit'      => $this->price,
        ]
    ]);

    $this->reset(['name', 'price']);
    
    $this->dispatch('cartUpdated');
    $this->dispatch('showSuccess', ['message' => 'Item manual berhasil ditambahkan!']);
}
}