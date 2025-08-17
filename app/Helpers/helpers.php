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

        $settings  = settings();
        $currency  = $settings->currency ?? null;

        // Default fallback kalau setting/currency belum lengkap
        $symbol             = $currency->symbol ?? 'Rp';
        $thousand_separator = $currency->thousand_separator ?? '.';
        $decimal_separator  = $currency->decimal_separator ?? ','; // tak terpakai untuk 0 desimal, tapi biarkan saja
        $position           = $settings->default_currency_position ?? 'prefix';

        // Pastikan nilainya dibulatkan ke 0 desimal
        $intValue = (int) round((float) $value, 0, PHP_ROUND_HALF_UP);

        // Format angka Indonesia tanpa desimal: 1.425.000
        $number = number_format($intValue, 0, $decimal_separator, $thousand_separator);

        // Prefix/suffix sesuai setting
        return $position === 'prefix' ? ($symbol . $number) : ($number . $symbol);
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
