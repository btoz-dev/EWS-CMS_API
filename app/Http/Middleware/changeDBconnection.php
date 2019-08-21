<?php

namespace App\Http\Middleware;

use Closure;
// use DB;
use Config;

class changeDBconnection
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
        Config::set('database.default', 'sqlsrv2');
        return $next($request);
    }
}
