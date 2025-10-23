<?php

namespace App\Livewire\Pos;

use Gloudemans\Shoppingcart\Facades\Cart;
use Livewire\Component;

class ManualItemForm extends Component
{
    public string $cart_instance = 'sale';

    // Pilihan tipe input (service/item)
    public string $item_type = 'service';

    public string $name = '';
    public $price = 0; // ✅ CHANGED: Ubah dari string ke nullable integer
    public $cost_price = 0; // ✅ CHANGED: Ubah dari string ke nullable integer
    public int $manual_qty = 1; // Qty item/jasa

    protected function rules(): array
    {
        return [
            'item_type' => 'required|in:service,item',
            'name' => 'required|string|max:255',
            'price' => 'nullable|numeric|min:0', // ✅ FIXED: Nullable, numeric, dan min:0
            'cost_price' => 'nullable|numeric|min:0', // ✅ FIXED: Nullable, numeric, dan min:0
            'manual_qty' => 'required|integer|min:1',
        ];
    }

    protected $messages = [
        'item_type.required' => 'Pilih tipe: Jasa atau Item.',
        'name.required' => 'Nama wajib diisi.',
        'price.numeric' => 'Harga jual harus berupa angka.',
        'price.min' => 'Harga jual tidak boleh negatif.',
        'cost_price.numeric' => 'Harga beli harus berupa angka.',
        'cost_price.min' => 'Harga beli tidak boleh negatif.',
        'manual_qty.*' => 'Qty minimal 1.',
    ];

    private function moneyToInt($v): int
    {
        if ($v === null || $v === '') {
            return 0;
        }
        if (is_numeric($v)) {
            return (int) $v;
        }
        return (int) preg_replace('/[^\d-]/', '', (string) $v);
    }

    public function add(): void
    {
        $this->validate();

        $qty = max(1, (int) $this->manual_qty);
        $sellPrice = $this->moneyToInt($this->price);
        
        // ✅ REMOVED: Tidak perlu validasi < 0 karena sudah di rules

        // Tentukan manual_kind berdasarkan item_type
        if ($this->item_type === 'service') {
            $manualKind = 'service';
            $costPrice = 0; // Jasa tidak ada HPP
            $typeLabel = 'Jasa';
        } else {
            // Item fisik manual (BUKAN second!)
            $manualKind = 'goods';

            $costPrice = $this->moneyToInt($this->cost_price);

            // ✅ REMOVED: Validasi sudah di rules

            // Warning jika harga jual < HPP (tapi tetap boleh simpan)
            if ($sellPrice < $costPrice && $costPrice > 0) {
                $this->dispatch('swal-warning', 'Perhatian: Harga jual lebih kecil dari harga beli!');
            }

            $typeLabel = 'Barang';
        }

        // Semua manual items pakai source_type='manual' + manual_kind
        Cart::instance($this->cart_instance)->add([
            'id' => 'MAN-' . uniqid(),
            'name' => trim($this->name),
            'qty' => $qty,
            'price' => $sellPrice,
            'weight' => 1,
            'options' => [
                'code' => '-',
                'source_type' => 'manual',
                'manual_kind' => $manualKind, // 'service' atau 'goods'
                'cost_price' => $costPrice,
                'source_type_label' => $typeLabel, // 'Jasa' atau 'Barang'
            ],
        ]);

        // Reset field numeric & nama, biarkan item_type tetap (biar user nyaman)
        $this->reset(['name', 'price', 'cost_price']);
        $this->manual_qty = 1;

        // Beri tahu komponen Checkout untuk refresh ringkasan
        $this->dispatch('cartUpdated')->to(\App\Livewire\Pos\Checkout::class);

        $this->dispatch('swal-success', "{$typeLabel} manual berhasil ditambahkan ke keranjang.");
    }

    public function addToCart(): void
    {
        $this->add();
    }

    public function render()
    {
        return view('livewire.pos.manual-item-form');
    }
}
