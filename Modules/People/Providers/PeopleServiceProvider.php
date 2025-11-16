<?php

namespace Modules\People\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;

class PeopleServiceProvider extends ServiceProvider
{
    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path('People', 'Database/Migrations'));
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes(
            [
                module_path('People', 'Config/config.php') => config_path('people.php'),
            ],
            'config',
        );
        $this->mergeConfigFrom(module_path('People', 'Config/config.php'), 'people');
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/people');

        $sourcePath = module_path('People', 'Resources/views');

        $this->publishes(
            [
                $sourcePath => $viewPath,
            ],
            ['views', 'people-module-views'],
        );

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), 'people');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/people');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'people');
        } else {
            $this->loadTranslationsFrom(module_path('People', 'Resources/lang'), 'people');
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (\Config::get('view.paths') as $path) {
            if (is_dir($path . '/modules/people')) {
                $paths[] = $path . '/modules/people';
            }
        }
        return $paths;
    }
}
