<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CheckIfTuesday
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $restaurant_id= env('Restaurant_id');
        $status = DB::table('clients')->where('id',$restaurant_id)->value('available');

        if ($status==0) {
            // dd($status);
            return response()->view('client.closed',compact('status'));
        }
        return $next($request);
    }
}
