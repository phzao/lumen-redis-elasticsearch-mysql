<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    /**
     * @var integer HTTP status code - 200 (OK) by default
     */
    protected $statusCode = 200;

    /**
     * Gets the value of statusCode.
     *
     * @return integer
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Sets the value of statusCode.
     *
     * @param integer $statusCode the status code
     *
     * @return self
     */
    protected function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * @param       $data
     * @param array $headers
     *
     * @return JsonResponse
     */
    public function respond($data, $headers = [])
    {
        $response = [
            'status' => 'success',
            'data'   => $data
        ];

        return new JsonResponse($response,$this->statusCode, $headers);
    }

    /**
     * @param string $errors
     * @param array  $headers
     *
     * @return JsonResponse
     */
    public function respondWithErrors(string $errors, $headers = [])
    {
        $errors = json_decode($errors, true);

        return new JsonResponse($errors, $this->statusCode, $headers);
    }

    /**
     * @param array $message
     *
     * @return JsonResponse
     */
    public function respondValidationError(array $message)
    {
        $error['status'] = 'fail';
        $error['data']   = $message;
        $jsonErrors      = json_encode($error);

        return $this
                    ->setStatusCode(422)
                    ->respondWithErrors($jsonErrors);
    }

    /**
     * @return JsonResponse
     */
    public function respondUpdatedResource()
    {
        return $this->setStatusCode(204)
                    ->respond([]);
    }

    /**
     * @param array $data
     *
     * @return JsonResponse
     */
    public function respondCreated($data = [])
    {
        return $this->setStatusCode(201)
                    ->respond($data);
    }
}
