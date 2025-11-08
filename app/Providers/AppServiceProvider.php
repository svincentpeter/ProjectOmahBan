<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Model::preventLazyLoading(!app()->isProduction());
        Blade::directive('money', function ($expr) {
        return "<?php echo e(format_currency($expr)); ?>";
        Blade::directive('rupiah', function ($expr) {
    return "<?php echo e(format_currency($expr)); ?>";
});
        
    });
    }
}
