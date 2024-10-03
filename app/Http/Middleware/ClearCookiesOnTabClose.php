<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cookie;
class ClearCookiesOnTabClose
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // config(['session.lifetime' => 0]);  // Set session to expire on close
        // config(['session.expire_on_close' => true]);  // Ensure session expires on close
        if (!Cookie::has('app_open')) {
            // Get all cookies
            $cookies = $request->cookie();
            // dd($cookies);
            // Delete all cookies created by you
            // Cookie::expire("heure_ouverture");
            // Cookie::expire("heure_fermeture");
            // Cookie::expire("ccmode");
            // Cookie::expire("ccdate");
            // Cookie::expire("cctime");
            // Cookie::expire("cost_delivery");
            // Cookie::expire("clientCity");
            // Cookie::expire("postal_code");
            // Cookie::expire("deliverymode");
            // Cookie::expire("clientAdress");
            // Cookie::expire("clientLat");
            // Cookie::expire("clientlng");
            // Cookie::expire("comefrom");
            // Cookie::expire("laravel_session");
            // Cookie::expire("XSRF-TOKEN");
            // foreach ($cookies as $cookieName => $cookieValue) {
            //     if (str_starts_with($cookieName, 'my_app_')) { // adjust the prefix to match your cookie names
            //         Cookie::expire($cookieName);
            //     }
            // }
        }
        // session()->flush();
        return $next($request);
    }
}
