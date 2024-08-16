<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class isKepsek
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->user()->roles_id == 1){
            return $next($request);
        }

        switch(auth()->user()->roles_id) {
            case 1:
                return redirect()->route('kepsek.dashboard');
            case 2:
                return redirect()->route('bendahara.dashboard');
            case 3:
                return redirect()->route('walikelas.dashboard');
            case 4:
                return redirect()->route('siswa.dashboard');
            default:
                return redirect()->route('login');
        }
    }
}
