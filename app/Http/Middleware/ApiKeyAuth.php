<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Env;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
		$api_key = Env::get('API_KEY');
		$supplied_api_key = $request->header('X-API-Key');

		if (is_null($supplied_api_key)) {
			abort(401, 'Missing X-API-Key header');
		}
		
		if (is_null($api_key)) {
			abort(500, 'Environment variable API_KEY not set');
		}
	
		if ($api_key !== $supplied_api_key) {
			abort(403, 'Invalid API key supplied');
		}

        return $next($request);
    }
}
