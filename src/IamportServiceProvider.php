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
        $this->publishes([
            __DIR__ . '/../config/iamport.php' => config_path('iamport.php')
        ]);

        $this->mergeConfigFrom(__DIR__ . '/../config/iamport.php', 'iamport');
    }

    /**
     * Register the service provider
     */
    public function register()
    {
        $this->app->singleton('iamport', function ($app) {
            $config = $app->make('config')->get('iamport');

            return new Iamport($config);
        });

        $this->app->alias(Iamport::class, 'iamport');
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
