<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TimeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        
        if(Auth::check())
        {
            $user =  User::find(Auth::user()->id);
            if(!$user->isAdministrator())
            {
                //dd($user->isAdministrator());
                return redirect('/dashboard');
            }

            return $next($request);

        }
        else{
            return redirect('/login');
        }
        

    }
}
