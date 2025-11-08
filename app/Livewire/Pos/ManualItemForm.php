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

    /** @var int|string */
    public $price = 0; // integer|string (akan diparse)
    /** @var int|string */
    public $cost_price = 0; // integer|string (akan diparse)

    public int $manual_qty = 1; // Qty item/jasa

    // ✅ Disesuaikan dengan Blade (camelCase)
    public string $manualReason = '';

    protected function rules(): array
    {
        return [
            'item_type' => 'required|in:service,item',
            'name' => 'required|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'manual_qty' => 'required|integer|min:1',
            // ✅ validasi alasan manual (camelCase)
            'manualReason' => 'required|string|min:10|max:500',
        ];
    }

    protected $messages = [
        'item_type.required' => 'Pilih tipe: Jasa atau Item.',
        'name.required' => 'Nama wajib diisi.',
        'price.numeric' => 'Harga jual harus berupa angka.',
        'price.min' => 'Harga jual tidak boleh negatif.',
        'cost_price.numeric' => 'Harga beli harus berupa angka.',
        'cost_price.min' => 'Harga beli tidak boleh negatif.',
        'manual_qty.required' => 'Qty minimal 1.',
        'manual_qty.min' => 'Qty minimal 1.',
        // ✅ pesan khusus alasan manual (camelCase)
        'manualReason.required' => 'Alasan input manual wajib diisi!',
        'manualReason.min' => 'Alasan terlalu singkat. Minimal 10 karakter.',
        'manualReason.max' => 'Alasan terlalu panjang. Maksimal 500 karakter.',
    ];

    private function moneyToInt($v): int
    {
        if ($v === null || $v === '') {
            return 0;
        }
        if (is_int($v)) {
            return $v;
        }
        if (is_float($v)) {
            return (int) round($v);
        }
        if (is_numeric($v)) {
            return (int) $v;
        }

        // Hapus pemisah ribuan/karakter non-digit
        $clean = preg_replace('/[^\d-]/', '', (string) $v);
        return (int) ($clean === '' ? 0 : $clean);
    }

    public function add(): void
    {
        $this->validate();

        $qty = max(1, (int) $this->manual_qty);
        $sellPrice = $this->moneyToInt($this->price);

        // Tentukan manual_kind berdasarkan item_type
        if ($this->item_type === 'service') {
            $manualKind = 'service';
            $costPrice = 0; // Jasa tidak ada HPP
            $typeLabel = 'Jasa';
        } else {
            // Item fisik manual (BUKAN second!)
            $manualKind = 'goods';
            $costPrice = $this->moneyToInt($this->cost_price);

            // Warning jika harga jual < HPP (tetap boleh simpan)
            if ($sellPrice < $costPrice && $costPrice > 0) {
                $this->dispatch('swal-warning', 'Perhatian: Harga jual lebih kecil dari harga beli!');
            }

            $typeLabel = 'Barang';
        }

        // ✅ Tambahkan ke cart dengan FLAG manual untuk trigger notifikasi Owner di checkout
        Cart::instance($this->cart_instance)->add([
            'id' => 'MAN_' . uniqid(),
            'name' => trim($this->name),
            'qty' => $qty,
            'price' => $sellPrice,
            'weight' => 1,
            'options' => [
                'code' => '-',
                'source_type' => 'manual',
                'manual_kind' => $manualKind, // 'service' | 'goods'
                'cost_price' => $costPrice,
                'source_type_label' => $typeLabel, // 'Jasa' | 'Barang'

                // ✅ FLAG MANUAL INPUT (dipakai di halaman checkout untuk notifikasi Owner)
                'is_manual_input' => true,
                'manual_reason' => trim($this->manualReason), // simpan sebagai snake di options (boleh)
                'manual_input_by' => auth()->id(),
                'manual_input_at' => now()->toDateTimeString(),
            ],
        ]);

        // Reset field, pertahankan item_type agar nyaman dipakai kasir
        $this->reset(['name', 'price', 'cost_price', 'manualReason']);
        $this->manual_qty = 1;

        // Beri tahu komponen Checkout untuk refresh ringkasan
        $this->dispatch('cartUpdated')->to(\App\Livewire\Pos\Checkout::class);

        // Konsisten dengan event swal-* yang sudah kamu pakai
        $this->dispatch('swal-success', "{$typeLabel} manual berhasil ditambahkan. Owner akan menerima notifikasi.");
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
