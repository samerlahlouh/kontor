<?php

namespace Educators\Http\Middleware;

use Closure;
use Auth;

class IsAgent
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
        if (Auth::user() &&  Auth::user()->type == 'agent') {
            return $next($request);
        }

        return redirect('/home');
    }
}
