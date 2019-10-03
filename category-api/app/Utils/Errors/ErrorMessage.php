<?php declare(strict_types=1);

namespace App\Utils\Errors;

/**
 * Class ErrorMessage
 * @package App\Utils\Errors
 */
class ErrorMessage implements ErrorMessageInterface
{
    /**
     * @param string $status
     * @param string $message
     *
     * @return string
     */
    public function getErrorMessage(string $status, string $message): string
    {
        $errormsg["status"]  = $status;
        $errormsg["message"] = $message;

        return json_encode($errormsg);
    }
}
