<?php declare(strict_types=1);

namespace App\AppProviders;

use App\Repositories\Redis\RedisRepository;
use App\Repositories\Redis\RedisRepositoryInterface;
use Illuminate\Support\ServiceProvider;

/**
 * Class RedisServiceProvider
 * @package App\AppProviders
 */
class RedisServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(
            RedisRepositoryInterface::class,
            RedisRepository::class

        );
    }
}

