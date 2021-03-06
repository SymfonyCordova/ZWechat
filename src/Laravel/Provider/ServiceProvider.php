<?php


namespace Zler\Wechat\Laravel\Provider;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;
use Zler\Wechat\Service\GzhService;
use Zler\Wechat\Service\Impl\GzhServiceImpl;

class ServiceProvider extends LaravelServiceProvider implements DeferrableProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(GzhService::class, function ($app) {
            return new GzhServiceImpl(config('zler-wechat.gzh'));
        });
    }

    /**
     * 启动应用服务
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/zler-wechat.php' => config_path('zler-wechat.php'),
        ]);

        //$this->loadRoutesFrom(__DIR__ . '/../routes/routes.php');

        //$this->loadMigrationsFrom(__DIR__.'/path/to/migrations');

        $this->publishes([
            __DIR__.'/../publish/gzh' => public_path('vendor/zler-wechat'),
        ], 'public');
    }

    /**
     * 获取由提供者提供的服务。
     *
     * @return array
     */
    public function provides()
    {
        return array(GzhService::class);
    }

}