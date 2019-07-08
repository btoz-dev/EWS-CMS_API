<?php

namespace App\Http\Middleware;

use Closure;
// use DB;
use Config;

class changeDBconnectionStag
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
        Config::set('database.default', 'sqlsrv3');
        return $next($request);
    }
}
