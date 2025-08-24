<?php

namespace Modules\Reports\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DailyReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('access_reports') ?? false;
    }

    public function rules(): array
    {
        return [
            'report_date' => ['required','date','date_format:Y-m-d'],
        ];
    }
}
