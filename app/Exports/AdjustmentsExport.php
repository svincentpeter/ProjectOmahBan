<?php

namespace App\Exports;

use App\Models\Adjustment;
use Maatwebsite\Excel\Concerns\FromCollection;

class AdjustmentsExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Adjustment::all();
    }
}
