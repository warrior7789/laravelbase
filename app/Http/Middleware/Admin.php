<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Auth;

use Illuminate\Support\Facades\Gate;
class Admin 
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if( Auth::check() ){            
            if ( Auth::user()->hasRole('Super Admin') || Auth::user()->hasRole('Sub Admin') ) {
                return $next($request);
            }else if ( Auth::user()->hasRole('user')  ) {                 
                return redirect(route('home'));
            }
        }

        abort(404);  // for other user throw 404 error
    }
}
