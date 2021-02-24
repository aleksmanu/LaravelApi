<?php
namespace App\Http\Middleware;

use Closure;

class ForceJson
{
    /**
     * Make it look like all requests are proper AJAX calls
     *  to fix some inconsistencies where Header('Accept: application/json') is NOT SET
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $request->headers->set('Accept', 'application/json');

        return $next($request);
    }
}
