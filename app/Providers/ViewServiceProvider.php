<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Modules\Adjustment\Entities\StockOpname;

class ViewServiceProvider extends ServiceProvider
{
    public function boot()
    {
        View::composer(['layouts.menu-flowbite', 'layouts.sidebar-flowbite'], function ($view) {
            $opnameInProgress = StockOpname::where('status', 'in_progress')->count();

            $view->with('opnameInProgress', $opnameInProgress);
        });
    }
}
