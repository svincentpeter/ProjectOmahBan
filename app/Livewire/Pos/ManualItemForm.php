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

        $numericPrice = (float) $this->price;
        if ($numericPrice <= 0) {
            $this->addError('price', 'Harga harus lebih besar dari 0.');
            return;
        }

        Cart::instance($this->cart_instance)->add([
            'id'     => 'manual-' . uniqid(),
            'name'   => $this->name,
            'qty'    => 1,
            'price'  => $numericPrice,
            'weight' => 1,
            'options' => [
                'source_type'           => 'manual',
                'product_discount'      => 0,
                'product_discount_type' => 'fixed',
                'sub_total'             => $numericPrice,
                'code'                  => '-',
                'stock'                 => 1,
                'unit'                  => 'unit',
                'product_tax'           => 0,
                'unit_price'            => $numericPrice,
                'hpp'                   => 0,
                'discount'              => 0,
                'tax'                   => 0,
            ],
        ]);

        $this->reset(['name', 'price']);
        $this->dispatch('cartUpdated')->to(\App\Livewire\Pos\Checkout::class);
        $this->dispatch('showSuccess', ['message' => 'Item manual ditambahkan!']);
    }
}
