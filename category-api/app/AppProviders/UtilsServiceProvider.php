<?php declare(strict_types=1);

namespace App\AppProviders;

use App\Utils\Errors\ErrorMessage;
use App\Utils\Errors\ErrorMessageInterface;
use Illuminate\Support\ServiceProvider;

/**
 * Class UtilsServiceProvider
 * @package App\AppProviders
 */
class UtilsServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(
            ErrorMessageInterface::class,
            ErrorMessage::class

        );
    }
}

