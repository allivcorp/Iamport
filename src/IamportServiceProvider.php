<?php
namespace Alliv\Iamport;

use Illuminate\Support\ServiceProvider;

class IamportServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    public function boot()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/iamport.php', 'iamport');

        $this->publishes([
            __DIR__ . '/../config/iamport.php' => config_path('iamport.php')
        ]);
    }

    /**
     * Register the service provider
     */
    public function register()
    {
        $this->app->bind('iamport', function () {
            return new Iamport(config('iamport'));
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['iamport'];
    }
}
