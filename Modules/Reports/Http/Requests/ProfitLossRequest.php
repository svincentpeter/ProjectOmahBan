<?php

namespace Modules\Reports\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfitLossRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Kalau pakai permission:
        return $this->user()?->can('access_reports') ?? false;
        // Atau sementara saat dev:
        // return true;
    }

    public function rules(): array
    {
        return [
            'start_date' => ['required','date','date_format:Y-m-d'],
            'end_date'   => ['required','date','date_format:Y-m-d'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $s = $this->input('start_date');
        $e = $this->input('end_date');
        if ($s && $e && $s > $e) {
            $this->merge(['start_date' => $e, 'end_date' => $s]);
        }
    }
}
