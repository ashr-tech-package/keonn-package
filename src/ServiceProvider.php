<?php

namespace Ashr\Keonn;

use Ashr\Keonn\Services\KeonnApi;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/keonn.php' => $this->app->configPath('keonn.php')
        ], 'ashr-keonn');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/keonn.php', 'keonn');

        if (config('keonn.keonn_storage_driver') === 'sftp') {
            config(['filesystems.disks.keonn_sftp' => config('keonn.disks.keonn_sftp')]);
        } else {
            config(['filesystems.disks.keonn_webdav' => config('keonn.disks.keonn_webdav')]);
        }

        $this->app->singleton(KeonnApi::class, function (Container $app) {
            return new KeonnApi($app['config']['keonn']);
        });
    }
}