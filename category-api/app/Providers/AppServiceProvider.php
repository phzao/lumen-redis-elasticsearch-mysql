<?php

namespace App\Providers;

use App\AppProviders\BaseRepoServiceProvider;
use App\AppProviders\ElasticSearchServiceProvider;
use App\AppProviders\RedisServiceProvider;
use App\AppProviders\UtilsServiceProvider;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(BaseRepoServiceProvider::class);
        $this->app->register(ElasticSearchServiceProvider::class);
        $this->app->register(RedisServiceProvider::class);
        $this->app->register(UtilsServiceProvider::class);
    }
}
