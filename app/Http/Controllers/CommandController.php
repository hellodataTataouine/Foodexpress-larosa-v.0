<?php

namespace App\Http\Controllers;
use App\Models\ClientRestaurat;
use App\Models\CommandProduct;
use App\Models\CommandProductOptions;
use App\Models\Imei;
use App\Models\PaimentMethod;
use App\Models\Seo;
use App\Models\User;
use App\Notifications\FirebaseNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Client;
use Illuminate\Support\Facades\Session;
use App\Models\ProduitsRestaurants;
use App\Models\CartOptionProductSelected;
use App\Models\CartDetails;
use App\Models\CarteUser;
use App\Models\PaimentRestaurant;
use App\Models\LivraisonRestaurant;
use App\Models\Command;
use App\Models\OptionsRestaurant;
use Illuminate\Support\Facades\Hash;
use SebastianBergmann\Environment\Console;
use App\Notifications\FirebaseNotificationNotification;
use Illuminate\Support\Facades\Notification;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Twilio\Rest\Client as twilioclient;

use Illuminate\Support\Facades\Mail;



class CommandController extends Controller
{
	public function cancelCommande($id)
    {
        $commande = Command::findOrFail($id);
        if ($commande->statut === 'Nouveau') {
            $commande->update(['statut' => 'Annulée']);
        }
        return redirect()->route('client.commandes');
    }


   public function addToCart(Request $request)
   {
    $cartItem = $request->input('cartItem');
    // dd($cartItem);
    $productId = $cartItem['id'];
	$productItem= $cartItem['idItem'];
    $productName = $cartItem['name'];
    $productImage = $cartItem['image'];
    $productPrice = $cartItem['price'];
    $productUnityPrice = $cartItem['unityPrice'];
    $productQuantity= $cartItem['quantity'];

    $customizationOptions = isset($cartItem['options']) ? $cartItem['options'] : null;

    if (!isset($cartItem['options'])) {
        $cartItem['options'] = [];
    }
//    $customizationOptions = $cartItem['options']; // An array containing selected options and their quantities

    $cart = session()->get('cart', []);


       $cartItem = [
           'id' => $productId,
		   'idItem' => $productItem,
           'name' => $productName,
           'image' => $productImage,
           'price' => $productPrice,
           'unityPrice' => $productUnityPrice,
           'quantity' => $productQuantity,



         //  'options' => $customizationOptions
       ];
    if ($customizationOptions !== null) {
        $cartItem['options'] = $customizationOptions;

    }else{ $cartItem['options'] = [];}

       $cart[] = $cartItem;

       session()->put('cart', $cart);

       return response()->json(['success' => true, 'message' => 'Produit  a été ajouté avec succès']);
   }
public function editCart(Request $request)
{
    $cartItem = $request->input('cartItem');

    $idItem = $cartItem['idItem'];
    $productQuantity = $cartItem['quantity'];
	 $productPrice = $cartItem['price'];
    $customizationOptions = isset($cartItem['options']) ? $cartItem['options'] : null;

    $cart = session()->get('cart', []);

    $itemExists = false;

    // Find the cart item to edit by matching the product ID
    foreach ($cart as &$item) {
        if ($item['idItem'] == $idItem) {
            // Update the quantity
            $item['quantity'] = $productQuantity;
             $item['price'] = $productPrice;
            // Update customization options if provided
            if ($customizationOptions !== null) {
                $item['options'] = $customizationOptions;
            }

            $itemExists = true;
            break; // Exit the loop once the item is updated
        }
    }

    if (!$itemExists) {
        // Item doesn't exist, so add a new item to the cart
        $cartItem['options'] = $customizationOptions ?? [];
        $cart[] = $cartItem;
    }

    session()->put('cart', $cart);

    return response()->json(['success' => true, 'message' => 'Produit a été modifié avec succès']);
}

   public function fetchCart(Request $request)
   {

       $cartItems = Session::get('cart', []);
       $cartItemCount = count($cartItems);
       $totalPrice = 0;
       foreach ($cartItems as $item) {
           $totalPrice += $item['price'] ;
       }
	    $totalPrice = number_format($totalPrice, 2);
       return response()->json(compact('cartItems', 'cartItemCount','totalPrice'));
   }

   public function checkout(Request $request)
   {
    $restaurant_id = env('Restaurant_id');
    $client = Client::where('id', $restaurant_id)->firstOrFail();

	    if (auth()->guard('clientRestaurant')->check()){
            $userId = auth()->guard('clientRestaurant')->id();
           if ($userId) {
		   $clientRestaurant = ClientRestaurat::findOrFail($userId);
		   }

			 } else{
			 $clientRestaurant = null;
		 }

       $clientId =env('Restaurant_id');;

       $cartItems = Session::get('cart', []);
       $livraisons = LivraisonRestaurant::where('restaurant_id', $clientId)->get();
       $paiments = PaimentRestaurant::where('restaurant_id', $clientId)->get();
       $cart = session()->get('cart', []);
       $totalPrice = 0;
       foreach ($cartItems as $item) {
           $totalPrice += $item['price'] ;
       }
	    $totalPrice = number_format($totalPrice, 2);
        $seo = Seo::where('client_id',$restaurant_id)->firstOrFail();
       return view('client.checkout',compact('cartItems','seo','client','livraisons','paiments','cart', 'totalPrice', 'clientRestaurant'));

   }


   public function store(Request $request)
   {


    // dd($request->all());
	 session::start();
    // dd($request->all());
     	     $cart = session()->get('cart', []);

        $cartItems = session('cart', []);
	    if($cartItems){

        $totalPrice = 0;


			$clientId = env('Restaurant_id');

            $client = Client::where('id', $clientId)->firstOrFail();

		$livraisons = LivraisonRestaurant::where('restaurant_id', $clientId)->get();

       $restaurantId = $client->id;
	   $user = User::findOrFail($client->user_id);
    // dd($user);
       foreach ($cartItems as $cartItem) {
        if (isset($cartItem['price'])  && is_numeric($cartItem['price']) ) {
            $totalPrice += $cartItem['price'];


        } else {
        }
    }


    // if($client->min_commande > $totalPrice){
    //     echo "<script>toastr.error('Désolé,La commande minimale($totalPrice) n'est pas atteinte.Veuillez ajouter plus d'articles à votre panier. Merci.');</script>";

    //     return redirect('/');
    //    // return view('client.checkout_success', compact('client','cart', 'cartItemCount', 'livraisons'));
    // }
$delivery_cost=0;
if(isset($_COOKIE['cost_delivery'])){
    $delivery_cost = $_COOKIE['cost_delivery'];
}
 $totalPrice = number_format($totalPrice+$delivery_cost, 2);
 $TVA = ($totalPrice * 20) / 100;
            $HT = $totalPrice - $TVA ;
	   //dd($request->input('delivery_method'));
    $deliveryMethodId = $request->input('delivery_method');

    $paymentMethodId = $request->input('payment_method');

if(Session::get('date')&&Session::get('time')){
    $deliveryTime = date("Y-m-d H:i:s", strtotime(Session::get('date') . Session::get('time')));

}else{
    $deliveryTime = date("Y-m-d H:i:s", strtotime($request->input('date') . $request->input('delivery_time')));
}
if($deliveryMethodId==11){

    $deliveryTime=null;

}
// dd($deliveryTime);
$PaymentMethode = PaimentMethod::findOrFail($paymentMethodId);
	//dd($PaymentMethode);
    if (auth()->guard('clientRestaurant')->check()){




		$userId = auth()->guard('clientRestaurant')->id();

        if ($userId) {
            $Userloggedin = ClientRestaurat::findOrFail($userId);



$Command = new Command;
$lanlat="";
if($_COOKIE['clientLat']&&$_COOKIE['clientlng']){
    $lanlat=$_COOKIE['clientLat']."/".$_COOKIE['clientlng'];
}


$Command->user_id = $userId;
$Command->restaurant_id = $client->id;
$Command->prix_total = $totalPrice;
$Command->prix_TVA = $TVA;
$Command->prix_HT = $HT;
$Command->methode_paiement = $paymentMethodId;
$Command->mode_livraison = $deliveryMethodId;
$Command->statut ='Nouveau';
$Command->Clientfirstname =$request->input('nom');
$Command->clientlastname =$request->input('prenom');
$Command->clientPostalcode =$request->input('codePostal');
$Command->clientAdresse =$request->input('adresse');
$Command->clientVille =$request->input('ville');
$Command->clientNum1 =$request->input('num1');
$Command->clientNum2 =$request->input('num2');
/*$Command->Clientfirstname =$Userloggedin->FirstName;
$Command->clientlastname =$Userloggedin->LastName;
$Command->clientPostalcode =$Userloggedin->codepostal;
$Command->clientAdresse =$Userloggedin->Address;
$Command->clientVille =$Userloggedin->ville;
$Command->clientNum1 =$Userloggedin->phoneNum1;
$Command->clientNum2 =$Userloggedin->phoneNum2;*/
$Command->clientEmail =$Userloggedin->email;
$Command->delivery_time = $deliveryTime;
//$Command->save();


		$basicUser = ClientRestaurat::find($userId);

        $basicUser->FirstName = $request->nom;
        $basicUser->LastName = $request->prenom;
        $basicUser->ville = $request->ville;
        $basicUser->Address = $request->adresse;
        $basicUser->codepostal = $request->codePostal;
        $basicUser->phoneNum1 = $request->num1;
        $basicUser->phoneNum2 = $request->num2;

        $basicUser->save();



			$cartDetailsArray = [];
	foreach ($cartItems as $cartItem) {
    $cartDetail = new CartDetails;
    $cartDetail->cart_id = $Command->id;
    $cartDetail->product_id = $cartItem['id'];
    $cartDetail->qte_produit = $cartItem['quantity'];
    $cartDetail->prixtotal_produit = $cartItem['price'];

   if(($cartItem['options'] != []))
     $cartDetail->optionsdetails = $cartItem['options'];
    else
    $cartDetail->optionsdetails = "";


    // Save each CartDetails to the array
    $cartDetailsArray[] = $cartDetail;
}

// confirmation Email

// Set the email in the session
$email= $user->email;

$data = [
    'clientFirstName' => $Userloggedin->FirstName,
    'clientLastName' => $Userloggedin->LastName,
	'clientNum1' => $Userloggedin->phoneNum1,
	'clientAdresse' => $Userloggedin->Address,
    'commandId' => $Command->id,
    'currentDateTime' => now()->format('d/m/Y H:i'),
    'cartItems' => $cartItems,
    'totalPrice' => $totalPrice,
    'clientEmail' => $Userloggedin->email,
    'email' => $email, // Use the email from the session-

];


session([
    'command' => $Command,
   'cartDetails' => $cartDetailsArray,
	'data' => $data,
]);


		if($PaymentMethode->type_methode == 'PayPal'){

    return redirect()->route('make.payment', [ 'paymentMethodId' => $paymentMethodId]);


}
			else{

					 $Command = session('command');

    $cartDetailsArray = session('cartDetails');

    // Save the Command to the database
    $Command->save();
 $data['commandId'] = $Command->id;
    // Iterate through the CartDetails array and save each one
    foreach ($cartDetailsArray as $cartDetail) {
        $cartDetail->cart_id = $Command->id; // Set the cart_id if needed
        $cartDetail->save();
    }
			// Define the email subject
$subject = 'Confirmation de commande';
// Store the email in the session

// Send the email using the Blade view
Mail::send('order_confirmation', $data, function ($message) use ($subject, $data) {
    $message->subject($subject)
        ->to($data['clientEmail']);
});
			Mail::send('order_confirmation_restaurant', $data, function ($message) use ($subject, $data) {
    $message->subject($subject)
        ->to($data['email']);
});
				//Notification
$firebaseToken = Imei::where('restaurant_id', $restaurantId)
    ->whereNotNull('fcm_token')
    ->pluck('fcm_token')
    ->all();
        $SERVER_API_KEY = env('FCM_SERVER_KEY');

//dd($devices);
// Create the notification instance




$receiverNumber =  env('Restaurant_num');



// Generate the message content
// Generate the message content
$message = "\nNouvelle Commande\n";
$message .= "Ticket N°: " . $Command->id . "\n";
$message .= "Nom: " . $Command->Clientfirstname . " " . $Command->clientlastname . "\n";
$message .= "Adresse: " . $Command->clientAdresse . "\n";
$message .= "Téléphone: " . $Command->clientNum1 . "\n";
$message .= "Détails des Produits:\n";

// Loop through each cart item and add its details to the message
foreach ($cartItems as $cartItem) {
    $message .= "- Produit: " . $cartItem['name'] . "\n";
    $message .= "  Quantité: " . $cartItem['quantity'] . "\n";
    $message .= "  Prix Unitaire: " . number_format($cartItem['price'], 2) . " TND\n";
    if (!empty($cartItem['options']) && is_array($cartItem['options'])) {
        $message .= "  Options: " . implode(", ", $cartItem['options']) . "\n";
    }

    else{
        $message .= "  Options: " . $cartItem['options']. "\n";
    }
    $message .= "\n";
}

$message .= "Prix Total: " . number_format($totalPrice, 2) . " TND\n";






       // $message = "This is testing from ItSolutionStuff.com";

        // try {

        //     $account_sid = getenv("TWILIO_SID");
        //     $auth_token = getenv("TWILIO_TOKEN");
        //     $twilio_number = getenv("TWILIO_FROM");

        //     $client = new twilioclient($account_sid, $auth_token);
        //     $client->messages->create($receiverNumber, [
        //         'from' => $twilio_number,
        //         'body' => $message]);

        //    // dd('SMS Sent Successfully.');

        // } catch (\Exception $e) {
        //  //   dd("Error: ". $e->getMessage());
        // }



try {
    // Send the notification to all the devices associated with the client
   // Notification::send($token, $notification)->notify($notification);
   $data = [
    "registration_ids" => $firebaseToken,
    "notification" => [
        "title" => "Nouvelle Commande",
        "body" => "Ticket N°: " . $Command->id . "\n" . $Command->Clientfirstname . ' ' . $Command->clientlastname,
    ]
];


$dataString = json_encode($data);

$headers = [
    'Authorization: key=' . $SERVER_API_KEY,
    'Content-Type: application/json',
];

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

$response = curl_exec($ch);
//dd($response);
    // Notification sent successfully
  //  Session::flash('success', 'Notification sent successfully.');
} catch (\Exception $e) {
    // Handle the exception and show an error message
    //Session::flash('error', 'Failed to send notification: ' . $e->getMessage());
}
		session()->forget('cartDetails');
	session()->forget('command');
session()->forget('data');
	session()->forget('cart');
			}
        }

  }

else{
	//dd($payment);
    $creerUnCompteChecked = $request->has('creer_un_compte');
	//dd($creerUnCompteChecked);
    $email = $request->input('email');
$password = $request->input('password');

if ($creerUnCompteChecked && !empty($email) && !empty($password)) {
        $basicUser = new ClientRestaurat;

        $basicUser->FirstName = $request->nom;
        $basicUser->LastName = $request->prenom;
        $basicUser->ville = $request->ville;
        $basicUser->Address = $request->adresse;
        $basicUser->codepostal = $request->codePostal;
        $basicUser->phoneNum1 = $request->num1;
        $basicUser->phoneNum2 = $request->num2;
        $basicUser->email = $request->email;
        $basicUser->password = Hash::make($request->password);
        $basicUser->restaurant_id = $restaurantId;


        $basicUser->save();
	$data = [
            'clientFirstName' => $basicUser->FirstName,
            'clientLastName' => $basicUser->LastName,
            'clientNum1' => $basicUser->phoneNum1,
            'clientAdresse' => $basicUser->Address,
            'email' => $basicUser->email,
        ];

        Mail::send('registration_confirmation', $data, function ($message) use ($data) {
            $message->subject('Confirmation d\'inscription')
                    ->to($data['email']);
        });


        // Log in the newly registered user
        auth('clientRestaurant')->login($basicUser);

    }



		$Command = new Command;
       $Command->user_id = Auth::id();
       $Command->restaurant_id = $client->id;
       $Command->prix_total = $totalPrice;
       $Command->prix_TVA = $TVA;
       $Command->prix_HT = $HT;
       $Command->methode_paiement = $paymentMethodId;
       $Command->mode_livraison = $deliveryMethodId;
       $Command->statut ='Nouveau';
       $Command->Clientfirstname =$request->input('nom');
       $Command->clientlastname =$request->input('prenom');
       $Command->clientPostalcode =$request->input('codePostal');
       $Command->clientAdresse =$request->input('adresse');
       $Command->clientVille =$request->input('ville');
       $Command->clientNum1 =$request->input('num1');
       $Command->clientNum2 =$request->input('num2');
      // $Command->clientEmail =$request->input('email');
	  $Command->delivery_time = $deliveryTime; // Save the selected delivery time

$cartDetailsArray = [];
	foreach ($cartItems as $cartItem) {
    $cartDetail = new CartDetails;
    $cartDetail->cart_id = $Command->id;
    $cartDetail->product_id = $cartItem['id'];
    $cartDetail->qte_produit = $cartItem['quantity'];
    $cartDetail->prixtotal_produit = $cartItem['price'];
//dd($cartItem['options']);
   if(($cartItem['options'] != []))
     $cartDetail->optionsdetails = $cartItem['options'];
    else
    $cartDetail->optionsdetails = "";


    // Save each CartDetails to the array
    $cartDetailsArray[] = $cartDetail;
}

$email= $user->email;
//$email = 'firas.saafi96@gmail.com';
$data = [
    'clientFirstName' => $Command->Clientfirstname,
    'clientLastName' => $Command->clientlastname,
	'clientNum1' => $Command->clientNum1,
	'clientAdresse' => $Command->clientAdresse,
    'commandId' => $Command->id,
    'currentDateTime' => now()->format('d/m/Y H:i'),
    'cartItems' => $cartItems,
    'totalPrice' => $totalPrice,
    'email' => $email,

];
	session([
    'command' => $Command,
   'cartDetails' => $cartDetailsArray,
	'data' => $data,
]);

/*
// Define the email subject
$subject = 'Confirmation de commande';
// Store the email in the session

			Mail::send('order_confirmation_restaurant', $data, function ($message) use ($subject, $data) {
    $message->subject($subject)
        ->to($data['email']);
});*/
      // $Command->save();
		if($PaymentMethode->type_methode == 'PayPal'){

    return redirect()->route('make.payment', ['paymentMethodId' => $paymentMethodId]);


}
			else{

					 $Command = session('command');

    $cartDetailsArray = session('cartDetails');

    // Save the Command to the database
    $Command->save();

    // Iterate through the CartDetails array and save each one
    foreach ($cartDetailsArray as $cartDetail) {
        $cartDetail->cart_id = $Command->id; // Set the cart_id if needed
        $cartDetail->save();
    }
		 $data['commandId'] = $Command->id;
			// Define the email subject
$subject = 'Réception de commande';
// Store the email in the session


			Mail::send('order_confirmation_restaurant', $data, function ($message) use ($subject, $data) {
    $message->subject($subject)
        ->to($data['email']);
});
				//Notification
$firebaseToken = Imei::where('restaurant_id', $restaurantId)
    ->whereNotNull('fcm_token')
    ->pluck('fcm_token')
    ->all();
        $SERVER_API_KEY = env('FCM_SERVER_KEY');

//dd($devices);

$receiverNumber =  env('Restaurant_num');

// Generate the message content
// Generate the message content
// Generate the message content
$message = "Nouvelle Commande\n";
$message .= "Ticket N°: " . $Command->id . "\n";
$message .= "Nom: " . $Command->Clientfirstname . " " . $Command->clientlastname . "\n";
$message .= "Adresse: " . $Command->clientAdresse . "\n";
$message .= "Téléphone: " . $Command->clientNum1 . "\n";
$message .= "Heure de Livraison: " . $Command->delivery_time . "\n\n";
$message .= "Détails des Produits:\n";

// Loop through each cart item and add its details to the message
foreach ($cartItems as $cartItem) {
    $message .= "- Produit: " . $cartItem['name'] . "\n";
    $message .= "  Quantité: " . $cartItem['quantity'] . "\n";
    $message .= "  Prix Unitaire: " . number_format($cartItem['price'], 2) . " TND\n";
    if (!empty($cartItem['options']) && is_array($cartItem['options'])) {
        $message .= "  Options: " . implode(", ", $cartItem['options']) . "\n";
    }
    else{
        $message .= "  Options: " . $cartItem['options']. "\n";
    }
    $message .= "\n";
}

$message .= "Prix Total: " . number_format($totalPrice, 2) . " TND\n";

        // try {

        //     $account_sid = getenv("TWILIO_SID");
        //     $auth_token = getenv("TWILIO_TOKEN");
        //     $twilio_number = getenv("TWILIO_FROM");

        //     $client = new twilioclient($account_sid, $auth_token);
        //     $client->messages->create($receiverNumber, [
        //         'from' => $twilio_number,
        //         'body' => $message]);

        //    // dd('SMS Sent Successfully.');

        // } catch (Exception $e) {
        //    // dd("Error: ". $e->getMessage());
        // }

try {
    // Send the notification to all the devices associated with the client
   // Notification::send($token, $notification)->notify($notification);
   $data = [
    "registration_ids" => $firebaseToken,
    "notification" => [
        "title" => "Nouvelle Commande",
        "body" => "Ticket N°: " . $Command->id . "\n" . $Command->Clientfirstname . ' ' . $Command->clientlastname,
    ]
];


$dataString = json_encode($data);

$headers = [
    'Authorization: key=' . $SERVER_API_KEY,
    'Content-Type: application/json',
];

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

$response = curl_exec($ch);
//dd($response);
    // Notification sent successfully
  //  Session::flash('success', 'Notification sent successfully.');
} catch (\Exception $e) {
    // Handle the exception and show an error message
    //Session::flash('error', 'Failed to send notification: ' . $e->getMessage());
}
		session()->forget('cartDetails');
	session()->forget('command');
session()->forget('data');
	session()->forget('cart');
			}

}




//Paiement

// Clear the cart sessions

$cartItemCount = count($cart);
$restaurant_id = env('Restaurant_id');
$seo = Seo::where('client_id',$restaurant_id)->firstOrFail();
return view('client.checkout_success', compact('client','cart', 'cartItemCount', 'livraisons','seo'));


		} else{


	  return redirect()->route('client.products.index');
		}

}

// Get all the devices associated with this Client (restaurant)





   // Display the cart for confirmation
   public function showCartForConfirmation()
   {
       // Fetch cart data from the session
       $cartItems = session()->get('cart', []);

       return view('restaurant.confirmation', compact('cartItems'));
   }


   public function removeCartItem(Request $request)
   {

    $productId = $request->input('productId');

    // Assuming you are storing cart data in the session
    $cart = session()->get('cart', []);

    // Find the index of the item to remove in the cart array
    $itemIndex = array_search($productId, array_column($cart, 'id'));

    if ($itemIndex !== false) {
        // Remove the item from the cart array
        array_splice($cart, $itemIndex, 1);

        // Recalculate total price
        $totalPrice = 0;
        foreach ($cart as $cartItem) {
            // Ensure 'price' and 'quantity' keys exist and are numeric
            if (isset($cartItem['price'])  && is_numeric($cartItem['price']) ) {
                // Perform the calculation and add to totalPrice
                $totalPrice += $cartItem['price'];

            } }
		 $totalPrice = number_format($totalPrice, 2);
        // Update the cart in the session
        session()->put('cart', $cart);
        $cartItemCount = count($cart);
        return response()->json([
            'cartItems' => $cart,
            'totalPrice' => $totalPrice,
            'cartItemCount' => $cartItemCount,

        ]);
    } else {
        return response()->json([
            'error' => 'Item not found in the cart.',
        ], 404);
    }
   }


   public function updateQuantity(Request $request)
   {
       $productId = $request->input('productId');
       $quantity = $request->input('quantity');

       // Get the current cart data from the session or database
       $cart = session()->get('cart', []);

       // Find the item in the cart with the matching productId
       $itemToUpdate = null;
       foreach ($cart as &$cartItem) {
           if ($cartItem['id'] == $productId) {
               $itemToUpdate = &$cartItem;
               break;
           }
       }

       if ($itemToUpdate) {
           // Update the quantity of the item in the cart
           $itemToUpdate['quantity'] = $quantity;
           $itemToUpdate['price'] = $quantity * $itemToUpdate['unityPrice'];

           // Update the cart data in the session or database
           session()->put('cart', $cart);
           $totalPrice = 0;
           foreach ($cart as $cartItem) {
               // Ensure 'price' and 'quantity' keys exist and are numeric
               if (isset($cartItem['price'])  && is_numeric($cartItem['price']) ) {
                   // Perform the calculation and add to totalPrice
                   $totalPrice += $cartItem['price'];

               } }
		    $totalPrice = number_format($totalPrice, 2);
           return response()->json([
               'message' => 'Quantité a été modifié avec succès',


                'totalPrice' => $totalPrice,




           ]);
       } else {
           return response()->json([
               'error' => 'Item not found in the cart.',
           ], 404);
       }
   }


   //historic commandes
   public function commandes(Request $request)
   {


       // Get the ID of the logged-in user
       $userId = auth()->guard('clientRestaurant')->id();
       if ($userId) {

       // Fetch all commandes of the logged-in user from the database
       $commandes = Command::where('user_id', $userId)->orderByDesc('id')->get();
       $cart = session()->get('cart', []);
       // Pass the list of commandes to the view
       return view('client.commandes', compact('commandes','client','cart'));
   }
}
public function fetchCartForDrinks(){
    $restaurant_id = env('restaurant_id');
    $boissonsDB = ProduitsRestaurants::where('restaurant_id',$restaurant_id)->where('categorie_rest_id',237)->get();
    $boissons=[];
    foreach ($boissonsDB as $drink){
        $boissons[].= $drink->nom_produit.' ('.$drink->prix.')';
    }
    $cartItems = session()->get('cart', []);
    $existingItems=array();

    foreach ($cartItems as $cart){
        $options=array_map('trim',explode(',',$cart['options']));

        foreach($options as $op){
            if(in_array($op,$options)){
                $existingItems[].=$op;
            }
        }


    }
    $user=Auth::guard('clientRestaurant')->user();
    // dd($user->id);
    $existingCommands=0;
    if($user){
        $commands = Command::where('restaurant_id',$restaurant_id)->where('user_id',$user->id)->get();
        // dd(count($commands));
        if (count($commands)>=1){
            $existingCommands= count($commands);
        }
    }
    // dd(['cart'=>$existingItems,'commands'=>$existingCommands]);
    return response()->json(['cart'=>$cartItems,'commands'=>$existingCommands]);
}
public function checkUser(Request $request){
    $restaurant_id = env('Restaurant_id');
    $user=Auth::guard('clientRestaurant')->user();
    $existingCommands=0;
    if($user){
        $commands = Command::where('restaurant_id',$restaurant_id)->where('user_id',$user->id)->get();
        // dd(count($commands));
        if (count($commands)>=1){
            $existingCommands= count($commands);
        }
    }else{
        $nom = $request->input('nom');
        $prenom = $request->input('prenom');
        $tel = $request->input('tel');
        // dd($request->all());
        $commands = Command::where('restaurant_id', $restaurant_id)
            ->where(function ($query) use ($nom, $prenom, ) {
                $query->whereRaw('lower(clientfirstname) = ?', [strtolower($nom)])
                    ->whereRaw('lower(clientlastname) = ?', [strtolower($prenom)]);
                    // ->where('clientNum1', $tel);
            })
            ->get();
            $existTel = Command::where('restaurant_id',$restaurant_id)->where('clientNum1',$tel)->get();
            // dd(count($commands),count($existTel));
            // dd(count($existTel));
            if(count($commands)<count($existTel) || count($commands)==count($existTel) ){
                $existingCommands= count($existTel);
            }else if(count($commands)>count($existTel)){
                $existingCommands= count($commands);
            }

            // if ($commands->isNotEmpty() && $existTel->isNotEmpty()) {
            //     // return false;

            //     if (count($commands)>=1 || count($existTel)>=1){
            //         $existingCommands= count($commands);
            //     }
            // }

            // return true;


    }

    return response()->json(['commands'=>$existingCommands]);
}
public function marwen(){
    $cartItems = session()->get('cart', []);
    $boissons = $this->transDrinksArray();
    $optionsArray = array();
    foreach($cartItems as $item){
        // $optionsArray = array_map('trim',explode(',',$item['options']));

        $optionsArray []= array_filter(array_map('trim', explode(',', $item['options'])), function($value) {
            // Remove slashes and newlines
            $value = str_replace(['/', "\n"], '', $value);
            return !empty($value); // Filter out empty elements
        });
    }
    $freeDrink=0;

    $optionsArray = call_user_func_array('array_merge', $optionsArray);
    foreach($optionsArray as $item){
        if(strpos($item, "gratuit") !== false){
            $freeDrink=1;
            break;
        }
    }
    // dd($freeDrink);
    // dd($optionsArray);
    // dd($optionsArray);
    $drinkExists = false;

        foreach ($boissons as $drink) {
            foreach ($optionsArray as $option) {
                // Trim whitespace, remove parentheses and their contents, convert to lowercase
                $trimmedOption = strtolower(preg_replace('/[\n\/\s]*\([^)]*\)|[\n\/\s]*/', '', $option));

                $trimmedDrink = strtolower(preg_replace('/[\n\/\s]*\([^)]*\)|[\n\/\s]*/', '', $drink));

                if ($trimmedOption == $trimmedDrink) {
                    $drinkExists = true;
                    break 2; // Break out of both loops if match is found
                }
            }
        }


    return response()->json(['drinkExists'=>$drinkExists,'freeDrink'=>$freeDrink]);

}
public function transDrinksArray(){
    $restaurant_id = env('Restaurant_id');
    $boissonsDB = ProduitsRestaurants::where('restaurant_id',$restaurant_id)->where('categorie_rest_id',237)->get();
    $boissons = [];
    foreach ($boissonsDB as $element) {
        // Format the drink information
        $formattedDrink = $element['nom_produit'] . ' (' . number_format($element['prix'], 2) . 'TND)';
        $boissons[] = $formattedDrink;
    }

    return $boissons;
}
public function getDrinksList(){
    $restaurant_id = env('Restaurant_id');
    $boissonsDB = ProduitsRestaurants::where('restaurant_id',$restaurant_id)->where('categorie_rest_id',237)->get();
    return response()->json(['boissons'=>$boissonsDB]);
}
public function addFreeDrinkToCart(Request $request){
    // Validate the request
    $request->validate([
        'freedrink' => 'required|boolean',  // Validate freedrink as a boolean
    ]);

    // Retrieve the current cart from the session or initialize it
    $cart = session()->get('cart', []);

    // Check if there is at least one item in the cart
    if (!empty($cart)) {
        // Add freedrink value to the first item
        $cart[0]['freedrink'] = $request->freedrink;
    }

    // Save the updated cart back to the session
    session(['cart' => $cart]);

    return response()->json(['success' => true, 'cart' => $cart]);
}


}
