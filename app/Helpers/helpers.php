<?php

if (!function_exists('settings')) {
    function settings()
    {
        $settings = cache()->remember('settings', 24 * 60, function () {
            return \Modules\Setting\Entities\Setting::firstOrFail();
        });

        return $settings;
    }
}

if (!function_exists('format_currency')) {
    function format_currency($value, $format = true)
    {
        // Kalau minta raw tanpa format, kembalikan integer bulat (tanpa desimal)
        if (!$format) {
            return (int) round((float) $value, 0, PHP_ROUND_HALF_UP);
        }

        // Paksa gaya Indonesia: Rp 100.000 (tanpa desimal)
        $symbol             = 'Rp';
        $thousand_separator = '.';  // 1.000
        $decimal_separator  = ',';  // tidak dipakai karena 0 desimal
        $position           = 'prefix'; // selalu "Rp 100.000"

        // Bulatkan ke 0 desimal dan pastikan integer
        $intValue = (int) round((float) $value, 0, PHP_ROUND_HALF_UP);

        // Format angka: 1.425.000
        $number = number_format($intValue, 0, $decimal_separator, $thousand_separator);

        // Tambahkan spasi antara simbol dan angka
        return $position === 'prefix' ? ($symbol . ' ' . $number) : ($number . ' ' . $symbol);
    }
}

if (!function_exists('rupiah')) {
    function rupiah($value) {
        return format_currency($value, true);
    }
}


if (!function_exists('merge_bank_into_note')) {
    function merge_bank_into_note(string $method, ?string $bank, ?string $note): ?string {
        $parts = [];
        if (in_array($method, ['Transfer','QRIS']) && $bank) {
            $parts[] = 'Bank: '.$bank;
        }
        if ($note) { $parts[] = $note; }
        return $parts ? implode(' | ', $parts) : null;
    }
}


if (!function_exists('make_reference_id')) {
    function make_reference_id($prefix, $number)
    {
        $padded_text = $prefix . '-' . str_pad($number, 5, 0, STR_PAD_LEFT);

        return $padded_text;
    }
}

if (!function_exists('array_merge_numeric_values')) {
    function array_merge_numeric_values()
    {
        $arrays = func_get_args();
        $merged = array();
        foreach ($arrays as $array) {
            foreach ($array as $key => $value) {
                if (!is_numeric($value)) {
                    continue;
                }
                if (!isset($merged[$key])) {
                    $merged[$key] = $value;
                } else {
                    $merged[$key] += $value;
                }
            }
        }

        return $merged;
    }
}
