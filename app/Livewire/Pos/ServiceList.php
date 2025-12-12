<?php

namespace App\Livewire\Pos;

use Livewire\Component;
use Modules\Product\Entities\ServiceMaster;
use Gloudemans\Shoppingcart\Facades\Cart;

class ServiceList extends Component
{
    public $services;
    public string $cartInstance = 'sale';

    public function mount()
    {
        $this->loadServices();
    }

    public function loadServices()
    {
        $this->services = ServiceMaster::where('status', 1)->orderBy('service_name')->get();
    }

    public function addServiceToCart($serviceId)
    {
        $service = ServiceMaster::find($serviceId);

        if (!$service) {
            $this->dispatch('swal-error', 'Jasa tidak ditemukan!');
            return;
        }

        // Check if already in cart
        $existingItem = Cart::instance($this->cartInstance)->search(function ($cartItem) use ($serviceId) {
            return data_get($cartItem->options, 'service_master_id') === $serviceId;
        });

        if ($existingItem->isNotEmpty()) {
            $this->dispatch('swal-warning', 'Jasa sudah ada di keranjang.');
            return;
        }

        // ✅ Format ID yang konsisten
        $cartId = 'SERVICE_' . $service->id;

        // Add to cart
        Cart::instance($this->cartInstance)->add([
            'id' => $cartId,
            'name' => $service->service_name,
            'qty' => 1,
            'price' => $service->standard_price,
            'weight' => 1,
            'options' => [
                'code' => 'SRV-' . $service->id,
                'stock' => 999, // ✅ TAMBAHAN: Set stock unlimited untuk service
                'source_type' => 'service_master',
                'service_master_id' => $service->id,
                'category' => $service->category ?? 'service',
                'original_price' => $service->standard_price,
                'is_from_master' => true,
                'cost_price' => 0,
                'product_type' => 'service',
            ],
        ]);

        // ✅ FIX: Dispatch tanpa `to()` untuk broadcast ke semua component
        $this->dispatch('cartUpdated');

        // ✅ Dispatch success message
        $this->dispatch('swal-success', "Jasa '{$service->service_name}' berhasil ditambahkan ke keranjang.");
    }

    public function render()
    {
        return view('livewire.pos.service-list');
    }
}
