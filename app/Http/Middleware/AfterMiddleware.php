<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class AfterMiddleware
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
        $response = $next($request);

        Log::info('Request end', ['url' => $request->fullUrl(), 'params' => $request->all()]);

        return $response;
    }

}
