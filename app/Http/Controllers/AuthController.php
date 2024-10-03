<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function logout(Request $request)
    {
        Auth::logout();

        return redirect()->route('restaurant.login');
    }
    public function logoutResto(Request $request)
    {
        Auth::logout();

        return redirect()->route('login');
    }
}
