<?php

namespace App\Http\Middleware;

use Closure;
use Elasticsearch\Common\Exceptions\Unauthorized401Exception;

class SecureMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     *
     * @return \Illuminate\Http\RedirectResponse|\Laravel\Lumen\Http\Redirector|mixed
     * @throws Unauthorized401Exception
     */
    public function handle($request, Closure $next)
    {

        $key_authorization = config('services.authorization_api.key_authorization');
        $key_request       = $request->header('x-api-key');

        if ($key_authorization!==$key_request) {
            $errormsg["message"] = "You don't have permission to access this data.";
            throw new Unauthorized401Exception(json_encode($errormsg));
        }

        return $next($request);
    }
}
