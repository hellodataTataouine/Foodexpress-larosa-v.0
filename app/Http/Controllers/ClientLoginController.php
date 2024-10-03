<?php

namespace App\Http\Controllers;

use App\Models\Seo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Client;
use App\Models\LivraisonRestaurant;
use Illuminate\Support\Facades\Hash;

class ClientLoginController extends Controller
{

  public function showLoginForm()
{

    $restaurant_id = env('Restaurant_id');
    $client = Client::where('id', $restaurant_id)->firstOrFail();
	$livraisons = LivraisonRestaurant::where('restaurant_id', $restaurant_id)->get();
 //dd($client->reservation_table);


        $seo = Seo::where('client_id',$restaurant_id)->firstOrFail();
        $cart = session()->get('cart', []);
        return view('client.login', compact('client', 'cart', 'livraisons','seo'));

}
    public function showRegistrationForm()
    {
        return view('client.register');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        // dd(Hash::make('admin123456'));
        // dd($credentials);
        // dd(Auth::guard('clientRestaurant'));
        if (Auth::guard('clientRestaurant')->attempt($credentials)) {
            return redirect()->intended('/');
        }

        // Authentication failed
        return redirect()->back()->withInput()->withErrors([
            'email' => 'Email ou Mot de passe non valides.',
        ]);
    }

    public function logout()
    {
        //dd('ok ');
        Auth::guard('clientRestaurant')->logout();

        return redirect('/');
    }
    public function register(Request $request)
    {
        $restaurant_id = env('Restaurant_id');
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:basic_users,email',
            'password' => 'required|min:8|confirmed',
        ]);
      $basicUser = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_admin' => 3,
            'restaurant_id' => $restaurant_id,
        ]);

        // Handle the rest of the registration process

        return redirect()->intended('/');
    }
}
