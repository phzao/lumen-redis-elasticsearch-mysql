<?php declare(strict_types=1);

namespace App\Utils\Errors;

/**
 * Interface ErrorMessageInterface
 * @package App\Utils\Errors
 */
interface ErrorMessageInterface
{
    /**
     * @param string $status
     * @param string $message
     *
     * @return string
     */
    public function getErrorMessage(string $status, string $message): string;
}
