<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class BeforeMiddleware
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        Log::info('Request start', ['url' => $request->fullUrl(), 'params' => $request->all()]);
        return $next($request);
    }

}
