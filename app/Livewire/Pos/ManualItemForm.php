<?php

namespace App\Livewire\Pos;
use Gloudemans\Shoppingcart\Facades\Cart;
use Livewire\Component;


class ManualItemForm extends Component
{
    public string $cart_instance = 'sale';
    public string $name = '';
    public string $price = '';


    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'price' => 'required', // dibersihkan manual → integer
        ];
    }


    private function moneyToInt($v): int
    {
        if ($v === null || $v === '') return 0;
        if (is_numeric($v)) return (int) $v;
        return (int) preg_replace('/[^\d]/', '', (string) $v);
    }


    public function add(): void
    {
        $this->validate();
        $numeric = $this->moneyToInt($this->price);
        if ($numeric <= 0) {
            $this->dispatch('swal-warning', 'Harga harus lebih dari 0.');
            return;
        }


        Cart::instance($this->cart_instance)->add([
            'id' => 'MAN-' . uniqid(),
            'name' => $this->name,
            'qty' => 1,
            'price' => $numeric,
            'weight' => 1,
            'options' => [
                'source_type' => 'manual',
                'code' => '-'
            ],
        ]);


        $this->reset(['name', 'price']);
        $this->dispatch('cartUpdated')->to(\App\Livewire\Pos\Checkout::class);
        $this->dispatch('swal-success', 'Item manual ditambahkan.');
    }

    public function addToCart(): void
{
    // Alias untuk kompatibilitas dengan view lama yang memanggil addToCart
    if (method_exists($this, 'add')) {
        $this->add();
        return;
    }

    // fallback minimal jika add() belum ada (aman tetap jalan)
    $this->validate([
        'name'  => 'required|string|max:255',
        'price' => 'required',
    ]);

    $numeric = (int) preg_replace('/[^\d]/', '', (string) $this->price);
    if ($numeric <= 0) {
        $this->dispatch('swal-warning', 'Harga harus lebih dari 0.');
        return;
    }

    Cart::instance($this->cart_instance ?? 'sale')->add([
        'id'      => 'MAN-' . uniqid(),
        'name'    => $this->name,
        'qty'     => 1,
        'price'   => $numeric,
        'weight'  => 1,
        'options' => [
            'source_type' => 'manual',
            'code'        => '-',
        ],
    ]);

    $this->reset(['name', 'price']);
    $this->dispatch('cartUpdated')->to(\App\Livewire\Pos\Checkout::class);
    $this->dispatch('swal-success', 'Item manual ditambahkan.');
}


    public function render()
    {
        return view('livewire.pos.manual-item-form');
    }
}
