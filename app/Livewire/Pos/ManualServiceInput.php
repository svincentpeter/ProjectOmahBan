<?php

namespace App\Livewire\Pos;

use Livewire\Component;
use Modules\Product\Entities\ServiceMaster;
use App\Helpers\PriceValidationHelper;

class ManualServiceInput extends Component
{
    // Properties
    public $services = []; // Daftar jasa dari master
    public $selected_service = ''; // ID jasa yang dipilih
    public $service_price = 0; // Harga input kasir
    public $service_qty = 1; // Jumlah jasa
    public $reason = ''; // Alasan perubahan harga

    // UI State
    public $show_reason_field = false; // Tampilkan field alasan
    public $show_supervisor_pin = false; // Tampilkan field PIN supervisor
    public $supervisor_pin = ''; // PIN supervisor

    protected $listeners = ['openManualServiceModal'];

    public function mount()
    {
        $this->loadServices();
    }

    /**
     * Load daftar jasa aktif dari master
     */
    public function loadServices()
    {
        $this->services = ServiceMaster::where('status', 1)->orderBy('service_name')->get()->pluck('service_name', 'id')->toArray();
    }

    /**
     * Ketika kasir pilih jasa dari dropdown
     * Auto-fill harga standar
     */
    public function updatedSelectedService()
    {
        if ($this->selected_service) {
            $service = ServiceMaster::find($this->selected_service);
            if ($service) {
                $this->service_price = $service->standard_price;
                $this->show_reason_field = false;
                $this->show_supervisor_pin = false;
            }
        }
    }

    /**
     * Ketika kasir ubah harga manual
     * Validasi deviasi harga
     */
    public function updatedServicePrice()
    {
        if (!$this->selected_service || !$this->service_price) {
            $this->show_reason_field = false;
            $this->show_supervisor_pin = false;
            return;
        }

        $service = ServiceMaster::find($this->selected_service);
        if (!$service || $service->standard_price === 0) {
            return; // Jasa custom, skip validasi
        }

        // Validasi harga
        $validation = PriceValidationHelper::validateServicePrice($service->service_name, $this->service_price, auth()->id());

        if ($validation['action'] === 'warning') {
            // Deviasi 30-50%: perlu alasan
            $this->show_reason_field = true;
            $this->show_supervisor_pin = false;
            $this->dispatch('swal-info', "Harga berbeda {$validation['variance_percent']}% dari standar " . format_currency($service->standard_price));
        } elseif ($validation['action'] === 'critical') {
            // Deviasi >50%: perlu alasan + PIN supervisor
            $this->show_reason_field = true;
            $this->show_supervisor_pin = true;
            $this->dispatch('swal-warning', 'Deviasi >50%! Perlu PIN Supervisor.');
        } else {
            $this->show_reason_field = false;
            $this->show_supervisor_pin = false;
        }
    }

    /**
     * Validasi PIN supervisor
     */
    private function validateSupervisorPin(): bool
    {
        $supervisor = \App\Models\User::where('role', 'supervisor')
            ->where('supervisor_pin', md5($this->supervisor_pin))
            ->exists();

        if (!$supervisor) {
            $this->addError('supervisor_pin', 'PIN supervisor salah.');
            return false;
        }

        return true;
    }

    /**
     * Tambahkan jasa ke keranjang
     */
    public function addService()
    {
        // Validasi input
        if (!$this->selected_service) {
            $this->dispatch('swal-warning', 'Pilih jasa terlebih dahulu.');
            return;
        }

        $service = ServiceMaster::find($this->selected_service);

        // Jika perlu PIN supervisor
        if ($this->show_supervisor_pin) {
            if (!$this->supervisor_pin) {
                $this->dispatch('swal-warning', 'Masukkan PIN supervisor.');
                return;
            }
            if (!$this->validateSupervisorPin()) {
                return;
            }
        }

        // Jika warning, alasan wajib
        if ($this->show_reason_field && !$this->reason) {
            $this->dispatch('swal-warning', 'Masukkan alasan perubahan harga.');
            return;
        }

        // Siapkan data untuk dikirim ke Checkout component
        $payload = [
            'id' => uniqid('manual_service_'),
            'product_name' => $service->service_name,
            'product_code' => 'SRV-' . $service->id,
            'product_price' => $this->service_price,
            'product_cost' => 0, // Jasa tidak ada HPP
            'product_order_tax' => 0,
            'product_tax_type' => null,
            'product_quantity' => $this->service_qty,
            'source_type' => 'manual_service',
            'manual_kind' => 'service',
            'original_price' => $service->standard_price,
            'is_price_adjusted' => $this->service_price !== $service->standard_price,
            'price_adjustment_amount' => $this->service_price - $service->standard_price,
            'price_adjustment_note' => $this->reason,
            'is_manual_input' => $this->servicePrice != $service->standard_price, // TRUE jika harga berubah
            'manual_reason' => $this->reason, // Alasan sudah ada
            'variance_level' => $this->showSupervisorPin ? 'critical' : ($this->showReasonField ? 'warning' : 'minor'),
        ];

        // Dispatch event ke Checkout component
        $this->dispatch('productSelected', $payload)->to('App\\Livewire\\Pos\\Checkout');

        // Reset form
        $this->reset(['selected_service', 'service_price', 'reason', 'supervisor_pin']);
        $this->show_reason_field = false;
        $this->show_supervisor_pin = false;

        $this->dispatch('swal-success', 'Jasa ditambahkan ke keranjang.');
        $this->dispatch('closeManualServiceModal');
    }

    public function render()
    {
        return view('livewire.pos.manual-service-input', [
            'reason_presets' => [
                'pelanggan_langganan' => 'Pelanggan Langganan',
                'promo_khusus' => 'Promo Khusus',
                'kompetitor_lebih_murah' => 'Kompetitor Lebih Murah',
                'bundling_paket' => 'Bundling Paket',
            ],
        ]);
    }
}
