<?php

namespace App\Livewire\Pos;

use Gloudemans\Shoppingcart\Facades\Cart;
use Livewire\Component;

class ManualItemForm extends Component
{
    public $cart_instance = 'sale';
    public $name = '';
    public $price = ''; // angka mentah

    protected function rules()
    {
        return [
            'name'  => 'required|string|min:2|max:100',
            'price' => 'required|numeric|min:1',
        ];
    }

    public function render()
    {
        // Minta front-end re-init AutoNumeric setiap render
        $this->dispatch('init-manual-item-autonumeric')->self();
        return view('livewire.pos.manual-item-form');
    }

    public function addToCart()
    {
        $this->validate();

        // Konversi nilai mentah dari AutoNumeric menjadi integer yang benar.
        // Input "125.000" dari AutoNumeric akan menjadi string "125000".
        // Kita pastikan ini adalah integer.
        $numericPrice = (int) $this->price;

        if ($numericPrice <= 0) {
            $this->addError('price', 'Harga harus lebih besar dari 0.');
            return;
        }

        Cart::instance($this->cart_instance)->add([
            'id'     => 'manual-' . uniqid(),
            'name'   => $this->name,
            'qty'    => 1,
            'price'  => $numericPrice, // Gunakan harga yang sudah pasti integer
            'weight' => 1,
            'options' => [
                'source_type'           => 'manual',
                'product_discount'      => 0,
                'product_discount_type' => 'fixed',
                'sub_total'             => $numericPrice, // Gunakan harga yang sudah pasti integer
                'code'                  => '-',
                'stock'                 => 1,
                'unit'                  => 'unit',
                'product_tax'           => 0,
                'unit_price'            => $numericPrice, // Gunakan harga yang sudah pasti integer
                'hpp'                   => 0,
                'discount'              => 0,
                'tax'                   => 0,
            ],
        ]);

        $this->reset(['name', 'price']);
        // Ganti event 'cartUpdated' agar lebih spesifik targetnya ke komponen Checkout
        $this->dispatch('cartUpdated')->to(\App\Livewire\Pos\Checkout::class);
        $this->dispatch('showSuccess', ['message' => 'Item manual ditambahkan!']);
    }
}
