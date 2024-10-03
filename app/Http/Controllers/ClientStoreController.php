<?php

namespace App\Http\Controllers;
use App\Models\Produits;
use App\Models\Client;
use App\Models\Option;
use App\Models\CarteUser;
use App\Models\CartDetails;
use App\Models\ProduitsFamilleOption;
use App\Models\FamilleOption;
use Geocoder\ProviderAggregator;


use App\Models\ProduitsFOptionsrestaurant;
use App\Models\ProduitsRestaurants;
use App\Models\OptionsRestaurant;
use App\Models\familleOptionsRestaurant;

use App\Models\Seo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientStoreController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $cartItems = session()->get('cartItems', []);
        return view('client.panier', compact('cartItems'));
    }

    public function store()
    {
        $produits = Produits::all();
        $restaurant_id = env('Restaurant_id');
        $seo = Seo::where('client_id',$restaurant_id)->firstOrFail();
        return view('client.index', compact('produits','seo'));
    }

    public function addToCart($productId)
    {
        // Retrieve the product based on the $productId
        $product = Produits::find($productId);

        // Set the default quantity
        $defaultQuantity = 1;

        // Retrieve the existing cart items from the session
        $cartItems = session()->get('cartItems', []);

        // Check if the product already exists in the cart
        $existingCartItem = null;
        foreach ($cartItems as $index => $item) {
            if ($item['product'] && $item['product']->id === $product->id) {
                // Increment the quantity if the product already exists in the cart
                $cartItems[$index]['qte'] += $defaultQuantity;
                $existingCartItem = $cartItems[$index];
                break;
            }
        }

        if (!$existingCartItem) {
            // Add the new product to the cart items array
            $cartItem = [
                'product' => $product,
                'qte' => $defaultQuantity
            ];
            $cartItems[] = $cartItem;
        }

        // Store the updated cart items back to the session
        session()->put('cartItems', $cartItems);

        // Redirect back or to the cart page
        return redirect()->route('panier.show');
    }


    public function confirmPanier()
    {
        // Retrieve the cart items from the session
        $cartItems = session()->get('cartItems', []);

        $restaurant_id = env('Restaurant_id');

        // Create a new cart
        $cart = new CarteUser();
        $cart->user_id = Auth::id();
        $idRestaurant = Client::where('id', $restaurant_id)->value('user_id');
        $cart->restaurant_id = $idRestaurant;
        $cart->save();

        // Insert the cart items into the database
        $totalPrice = 0;
        foreach ($cartItems as $item) {
            $product = $item['product'];

            $cartDetails = new CartDetails();
            $cartDetails->product_id = $product->id;
            $cartDetails->cart_id = $cart->id;
            $price = $product->prix;
            $quantity = $item['qte'];

            $subtotal = $price * $quantity;
            $totalPrice += $subtotal;

            $cartDetails->save();
        }

        // Clear the cart items from the session
        session()->forget('cartItems');

        // Redirect to a success page or any other appropriate action
        return redirect()->route('panier.confirmation');
    }


    public function getProductRestaurantDetails($productId)
    {
        // Retrieve the product details from the database based on the $productId
        $product = ProduitsRestaurants::find($productId);

        // Retrieve the product's famille options
       $familleOptions = ProduitsFOptionsrestaurant::where('id_produit_rest', $productId)
    ->orderBy('RowN') // Add this line to sort by RowN
    ->get();


        // Retrieve the options for each famille option
        foreach ($familleOptions as $familleOption) {
            $familleOption->options = OptionsRestaurant::where('famille_option_id_rest', $familleOption->id_familleoptions_rest)->get();
        }
        // Retrieve the name of the famille option
        foreach ($familleOptions as $familleOption) {
            $familleOption->famille_option = familleOptionsRestaurant::find($familleOption->id_familleoptions_rest);
        }
//dd($product);
        // Return the product details as a JSON response
        return response()->json([
            'product' => $product,
            'familleOptions' => $familleOptions
        ]);
    }
    public function getCurrentCartId()
    {
        return session('cart_id');
    }
    public function show()
    {
        $cartItems = session()->get('cartItems', []);
        $cartItemCount = count($cartItems);
        return view('client.panier', compact('cartItems', 'cartItemCount'));
    }

    public function removeFromCart(Request $request, $productId)
    {
        $cartItems = session()->get('cartItems', []);

        // Loop through the cart items to find the item with the matching cart details ID
        foreach ($cartItems as $key => $item) {
            if ($item->id == $productId) {
                // Delete the cart details record from the database
                CartDetails::destroy($item->id);

                // Remove the item from the cart items array
                unset($cartItems[$key]);
                // Reindex the array keys
                $cartItems = array_values($cartItems);
                // Update the cart items in the session
                session()->put('cartItems', $cartItems);

                return redirect()->route('panier.show')->with('success', 'Produit supprimé avec succès.');
            }
        }

        return redirect()->route('panier.show')->with('error', 'Erreur de supprimer le produit.');
    }

    public function calculeDistance(Request $request){
        $earthRadius = 6371; // in kilometers
        $lat1=32.92905968163634;
        $lon1=10.448639221238507;
        $lat2=$request->input('lat');
        $lon2=$request->input('long');
        $lat2=32.9290221;
        $lon2=10.4486335;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $lat1 = deg2rad($lat1);
        $lat2 = deg2rad($lat2);

        $a = sin($dLat/2) * sin($dLat/2) + sin($dLon/2) * sin($dLon/2) * cos($lat1) * cos($lat2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $distance = $earthRadius * $c;

        // $client_long=32.9290221;
        // $client_lat =10.4486335;
        // $latFrom = deg2rad(32.92905968163634);
        // $lonFrom = deg2rad(10.448639221238507);
        // $latTo = deg2rad($request->input('lat'));
        // $lonTo = deg2rad($request->input('long'));
        // $latTo = deg2rad(32.9290221);
        // $lonTo = deg2rad(10.4486335);
        // // Haversine formula
        // $lonDelta = $lonTo - $lonFrom;
        // $a = sin($latDelta = $latTo - $latFrom) / 2;
        // $a = $a * $a + cos($latFrom) * cos($latTo) * sin($lonDelta / 2) * sin($lonDelta / 2);
        // $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        // $distance = ($earthRadius * $c)/1000;
        return response()->json(['distance' => $distance]);

    }

}
