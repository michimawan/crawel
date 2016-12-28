<?php

namespace App\Http\Middleware;

use Closure;
use Response;

class AjaxRequest
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
        if ($request->ajax()) {
            return $next($request);
        }
        return Response::json(['errors' => 'unauthorize access'], 401);
    }
}
