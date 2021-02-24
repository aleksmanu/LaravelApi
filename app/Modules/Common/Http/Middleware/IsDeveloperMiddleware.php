<?php

namespace App\Modules\Common\Http\Middleware;

use App\Modules\Auth\Models\Role;
use Closure;
use Illuminate\Support\Facades\Auth;

class IsDeveloperMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!Auth::check() || !request()->user()->isA(Role::DEVELOPER)) {
            return response('Maybe some other time bro', 403);
        }

        return $next($request);
    }
}
