<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Modules\Sale\Entities\Sale;
use Illuminate\Queue\SerializesModels;

class ManualInputCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    
    public $sale;
    public $manualItems;
    public $cashier;
    
    public function __construct(Sale $sale, $manualItems, $cashier)
    {
        $this->sale = $sale;
        $this->manualItems = $manualItems;
        $this->cashier = $cashier;
    }
}
