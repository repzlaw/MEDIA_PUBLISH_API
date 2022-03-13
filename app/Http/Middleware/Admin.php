<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\IpAddress;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Admin
{
    use ApiResponse;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $ips = IpAddress::pluck('ip_address')->toArray();

        if (Auth::check()){
            if (Auth::user()->type === 'Admin' && (in_array($request->ip(), $ips))){
                return $next($request);
            }
        }
        return $this->error(null,
                            'you are not permitted to view this route',
                            403
                            );
    }
}
