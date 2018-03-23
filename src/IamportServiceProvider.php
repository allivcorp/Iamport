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
        $this->app->singleton(Iamport::class, function () {
            return new Iamport(config('iamport'));
        });

        $this->app->alias(Iamport::class, 'iamport');
    }
}
