<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Routing\Middleware;

class TokenValidatorMiddleware implements Middleware
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
        if (!empty($request->header('AUTHORIZATION'))) {
            if (!preg_match('/Bearer\s(\S+)/i', $request->header('AUTHORIZATION'), $matches)) {
                return response()->json(['error' => 'Malformed auth header'], 400);
            }
            return $next($request);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

}
