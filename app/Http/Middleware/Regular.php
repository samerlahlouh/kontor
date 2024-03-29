<?php

namespace Educators\Http\Middleware;

use Closure;
use Auth;

class Regular
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
        if (Auth::user() &&  Auth::user()->type == 'regular') {
            return $next($request);
     }

    return redirect('/home');
    }
}
