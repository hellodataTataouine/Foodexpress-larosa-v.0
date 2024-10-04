<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="title" content="{{ $seo->title }}">
    <meta name="description" content="{{ $seo->description }}">
    <meta name="keywords" content="{{ $seo->keywords }}">
    <meta name="robots" content="{{ $seo->robots }}, {{ $seo->follow_links}}">
    <meta http-equiv="Content-Type" content="{{ $seo->content_type }}">
    <meta name="language" content="{{ $seo->language }}">
    <meta property="og:image" content="{{url('uploads/seo/'.$seo->image)}}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="author" content="cipherlink">

  <title>La Rosa | Checkout</title>
  <link rel="icon" type="image/x-icon" href="logo_larosa.png">
  <link rel="stylesheet" href="{{ asset('vendors/mdi/css/materialdesignicons.min.css') }}">
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
     integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
     crossorigin=""/>
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
  <style>
    button:disabled:hover {
            cursor: not-allowed; /* Change cursor to indicate not clickable */
            opacity: 0.6; /* Reduce opacity */
            /* Optionally change other styles like background color, border, etc. */
            pointer-events: none;
            border: 1px solid #999999;
            background-color: #cccccc;
            color: #666666;
            }
    button:disabled,
    button[disabled]{
        cursor: not-allowed;
        pointer-events: none;
        border: 1px solid #999999;
        background-color: #cccccc;
        color: #666666;
    }
    .icon-input-container.address-container {
    display: flex;
    align-items: center;
    flex-direction: column;
}
    .switchable-list {
        list-style-type: none;
        padding: 0;
        display: flex;
        gap: 10px;
    }

    .payment-method-btn {
        cursor: pointer;
        border: 1px solid #141310;
        border-radius: 15px; /* Adjust the border-radius to make it more rounded */
        padding: 10px;
        background-color: #fff;
        transition: background-color 0.3s;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .payment-method-btn.active {
        background-color: #02463a;
        color: #fff;
        border-color: #af191b;
    }
	.delivery-method-btn {
        cursor: pointer;
        border: 1px solid #141310;
        border-radius: 15px; /* Adjust the border-radius to make it more rounded */
        padding: 10px;
        background-color: #fff;
        transition: background-color 0.3s;
        display: flex;
        flex-direction: column;
        align-items: center;
		width: max-content;
    }

    .delivery-method-btn.active {
        background-color: #02463a;
        color: #fff;
        border-color: #af191b;
    }

.btn-social {
    text-align: left; /* Align the icon and text to the left */
    margin-right: 10px; /* Add space to the right of the first button */
	 border-radius: 9999px;
}

/* Adjust the icon size */
.btn-social i {
    font-size: 24px; /* Adjust the size as needed */
    margin-right: 10px; /* Add space between the icon and text */

    border-radius: 9999px;

	}
	.fa-google {
  background: conic-gradient(from -45deg, #ea4335 110deg, #4285f4 90deg 180deg, #34a853 180deg 270deg, #fbbc05 270deg) 73% 55%/150% 150% no-repeat;
  -webkit-background-clip: text;
  background-clip: text;
  color: transparent;
  -webkit-text-fill-color: transparent;

}

.check-form {

    background-color: #f9f9f9;
    padding: 10px;
    text-align: left;
    margin: 10px;
}
.flex-container {
  display: flex;
  flex-direction: row-reverse;
  justify-content: center;
  align-items: flex-start;
  margin-top: 5%
}
@media(max-width:853px) {
  .flex-container {
    flex-direction: column-reverse;
    /* flex-flow: column nowrap; */
    /* justify-content: center; */
    padding-top: 30%;
  }
  .flex-container >.check-form{
    padding: 0px;
    margin: 0px;
    padding-bottom: 10px;
  }
}
.editbtndeliverymode:hover{
    cursor: pointer;
}
.editbtndeliverymodelivraison:hover{
    cursor: pointer;
}
/*
.flex-container > div {
    margin-top: 5%;
    margin-right: 0.5%;
}
#cartbcont{
    padding: 0px;
    min-width: 576px;
}
@media(max-width:853px) {
  .flex-container {
    flex-direction: column-reverse;
    flex-flow: column nowrap;
    justify-content: center;
  }

  .flex-container > div {
    margin-top: 5%;
    margin-right: 0.5%;
    padding-left: 1px;
    padding-right: 1px;
    }


}*/
/* .checkout-billing{
    order: 2;
}
.check-form{
    order: 1;
} */
</style>
</head>
<body>
@include('client.layouts.top_menu_client')
<!-- Aside (Mobile Navigation) -->
@include('client.layouts.header_menu')
<section class="section">

    <div class="container">
        <a href="{{url('/')}}"><img src="logo_larosa.png" alt="" width="50px">Retour</a>
        <div class="flex-container">
            @php
                $restaurant_id=env('Restaurant_id');
                $horaire = App\Models\Horaire::where('client_id',$restaurant_id)->first();
            @endphp
            <input type="text" id="heure_ouverture" hidden value="{{$horaire->heure_ouverture}}">
            <input type="text" id="heure_fermeture" hidden value="{{$horaire->heure_fermeture}}">
            <div id="container_details" class="col-xl-5 checkout-billing">



                @if (count($cartItems) > 0)
                    {{-- <div class="row"> --}}


                    <h3>Votre Panier</h3>
                    <table class="ct-responsive-table" id='your_cart'>
                        <thead>
                            <tr>

                                <th>Article</th>
                                <th>Prix unitaire</th>


                                <th>Options</th>
                                <th>Quantity</th>
                                <th>Prix total</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cartItems as $index => $cartItem)
                                <tr data-product-id="{{ $cartItem['id'] }}">

                                    <td data-title="Article">{{ $cartItem['name'] }}</td>
                                    @if ($cartItem['unityPrice']==0)
                                    <td data-title="Prix unitaire">--</td>
                                    @else
                                    <td data-title="Prix unitaire">{{ $cartItem['unityPrice'] }} TND</td>
                                    @endif


                                    <td data-title="Options">
                                        @if ((isset($cartItem['options'])) &&($cartItem['options'] != []))
                                            {{$cartItem['options']}}
                                        @endif
                                    </td>
                                    <td data-title="Quantity">
                                        {{ $cartItem['quantity'] }}
                                    <!-- <button class="btn-quantity" data-product="{{ $cartItem['id'] }}" data-action="decrease">-</button>
                                        <input type="number" name="quantity[{{ $cartItem['id'] }}]" id="quantity_{{ $cartItem['id'] }}" value="{{ $cartItem['quantity'] }}" min="1" max="100">
                                        <button class="btn-quantity" data-product="{{ $cartItem['id'] }}" data-action="increase">+</button> -->
                                    </td>
                                    <td data-title="Prix total">{{ $cartItem['price'] }} TND</td>
                                    <td data-title="Action"><button class="btn-remove" data-product-id="{{ $cartItem['id'] }}">Annuler</button></td>
                                </tr>
                            @endforeach
                            @php
                                $delivery_cost = 0;
                            @endphp
                            @if (Cookie::get('deliverymode'))
                                <tr>
                                    <td><h6 class="mb-0">Frais de livraison</h6></td>
                                    <td></td>
                                    <td>ne pas inclus</td>

                                </tr>

                            @endif

                            <tr class="total">
                            <td>
                                <h6 class="mb-0">Total</h6>
                            </td>
                            <td></td>
                            <td> <div class="totalprice"> <span><strong>{{ $totalPrice + $delivery_cost }} TND</strong></span></div></td>
                        </tr>
                        </tbody>
                    </table>


                            <div id="CartBancaire" name="CartBancaire" style="display: none;">
                                <div  id="cartbcont">
                                    <div class="row">
                                        <div class="col-md-offset-3">
                                            <div class="panel panel-default credit-card-box" style="border: 2px solid #ccc; padding: 20px;">
                                                <div class="panel-heading display-table">
                                                    <h3 class="panel-title">Détails de paiement</h3>
                                                </div>
                                                <div class="panel-body">
                                                    <form role="form" class="require-validation"
                                                        data-cc-on-file="false"

                                                        id="payment-form">
                                                        @csrf
                                                        <div class='form-row row'>
                                                            <div class='col-xs-12'>
                                                                <i class="fab fa-cc-mastercard fa-2x card-icon" title="Mastercard"></i>
                                                                <i class="fab fa-cc-visa fa-2x card-icon" title="Visa"></i>
                                                                <i class="fab fa-cc-amex fa-2x card-icon" title="American Express"></i>
                                                                <i class="fab fa-cc-discover fa-2x card-icon" title="Discover"></i>
                                                                <i class="fab fa-cc-diners-club fa-2x card-icon" title="Diners Club"></i>
                                                                <i class="fab fa-cc-jcb fa-2x card-icon" title="JCB"></i>
                                                                <!-- Add more card icons for other card types here -->
                                                            </div>
                                                        </div>
                                                        <div class='form-row row'>
                                                            <div class='col-xs-12 form-group required'>
                                                                <label class='control-label'>Nom sur la Carte</label>
                                                                <input class='form-control' size='4' type='text'>
                                                            </div>
                                                        </div>
                                                        <div class='form-row row'>
                                                            <div class='col-xs-12 form group  required'>
                                                                <label class='control-label'>Numéro de Carte</label>
                                                                <input autocomplete='off' class='form-control card-number' size='20' type='text'>
                                                            </div>
                                                        </div>
                                                        <div class='form-row row'>
                                                            <div class='col-xs-12 col-md-4 form-group cvc required'>
                                                                <label class='control-label'>CVC</label>
                                                                <input autocomplete='off' class='form-control card-cvc' placeholder='ex. 311' size='4' type='text'>
                                                            </div>
                                                            <div class='col-xs-12 col-md-4 form-group expiration required'>
                                                                <label class='control-label'>Mois d'expiration</label>
                                                                <input class='form-control card-expiry-month' placeholder='MM' size='2' type='text'>
                                                            </div>
                                                            <div class='col-xs-12 col-md-4 form-group expiration required'>
                                                                <label class='control-label'>Année d'expiration</label>
                                                                <input class='form-control card-expiry-year' placeholder='YYYY' size='4' type='text'>
                                                            </div>
                                                        </div>
                                                        <div class='form-row row'>
                                                            <div class="col-md-12 error form-group hide">
                                                                <div class="alert-danger alert"></div>
                                                            </div>

                                                        </div>
                                                        <div class="row">
                                                            <div class="col-xs-12">
                                                                <button class="btn btn-primary btn-lg btn-block btn-cart">Payer et Confirmer <b>{{$totalPrice+$delivery_cost}} TND</b></button>
                                                            </div>
                                                            <div class="col-xs-12">
                                                                <div class="mb-4"></div>
                                                                <button class="btn btn-danger btn-lg btn-block" type="button" onclick="goBack()">Annuler</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <br/>
                                <br/>

                            </div>
                    {{-- </div> --}}

            </div>
            @if (auth('clientRestaurant')->check())
                <div class="col-xl-7 check-form">
                    <h4>Entrer vos détailles</h4>

                    <div>
                        <form id="checkoutForm"  method="POST" action="{{ route('client.checkout1.store') }}"  class="check-validation">
                            @csrf

                            <div class="row">
                                <div class="form-group col-xl-6">
                                    <label>Nom <span class="text-danger">*</span></label>
                                    <input type="text" placeholder="Nom" name="nom" id="nom" class="form-control" value="{{ optional($clientRestaurant)->FirstName }}" required="">
                                </div>
                                <div class="form-group col-xl-6">
                                    <label>Prénom <span class="text-danger">*</span></label>
                                    <input type="text" placeholder="Prénom" name="prenom" id="prenom" class="form-control" value="{{ optional($clientRestaurant)->LastName }}" required="">
                                </div>
                            </div>

                                <div class="row" id="address-div" style="display: none;">

                                    <div class="form-group col-xl-6" id="input-container">
                                        <div style="display: flex; align-items: center;">
                                            <i class="mdi mdi-crosshairs-gps geolocation-icon"  onclick="geolocation()" style="font-size: xx-large; cursor: pointer;"></i>
                                            <input type="text" placeholder="Adresse" name="adresse" class="form-control" value="" required="">


                                        </div>
                                    </div>
                                    </div>
                                    <div class="row">
                                    <div class="form-group col-xl-6">
                                        <label>Code Postal<span class="text-danger">*</span></label>
                                        <input type="text" placeholder="Code Postal" name="codePostal" class="form-control" value="{{ optional($clientRestaurant)->codepostal }}">
                                    </div>
                                    <div class="form-group col-xl-6">
                                        <label>Ville <span class="text-danger">*</span></label>
                                        <input type="text" placeholder="Ville" name="ville" class="form-control" value="{{ optional($clientRestaurant)->ville }}" required="">
                                    </div>
                                    </div>
                                </div>


                            <div class="row">
                                <div class="form-group col-xl-6">
                                    <label>Numéro de téléphone 1<span class="text-danger">*</span></label>
                                    <input type="text" placeholder="Numéro de téléphone " name="num1" id="num1" class="form-control" value="{{ optional($clientRestaurant)->phoneNum1 }}" required="">
                                </div>
                                <div class="form-group col-xl-6">
                                    <label>Numéro de téléphone 2</label>
                                    <input type="text" placeholder="Numéro de téléphone " name="num2" class="form-control" value="{{ optional($clientRestaurant)->phoneNum2 }}" >
                                </div>
                            </div>
                            <div id="cartDetailsAndDeliveryMethod">

                                @if (count($livraisons) > 0)
                                    <h3>La méthode de livraison choisie</h3>


                                    <ul id="delivery-method-list" class="switchable-list">



                                            @foreach ($livraisons as $livraison)
                                                @if ($livraison)
                                                    @php
                                                        $livraisonType = \App\Models\Livraison::find($livraison->livraison_id);
                                                    @endphp

                                                        @if ($livraisonType->methode == "Click & Collect")
                                                            <li class="delivery-method-btn active" data-method="{{ $livraisonType->methode }}" data-id="{{ $livraisonType->id }}" >
                                                                <i class="fas fa-truck">&nbsp;Sur place</i>

                                                                <i class="fa fa-calendar" id="ccdatetime"></i>




                                                            </li>
                                                            <br>
                                                            {{-- <input type="hidden" name="delivery_method" id="delivery_method_input" value="{{ $livraisonType->id }}"> --}}
                                                            <li class="editbtndeliverymode"><i class="fa fa-edit" style="font-size:36px"  data-bs-toggle="modal" data-bs-target="#myModalcc"></i></li>

                                                        @endif


                                                @endif
                                            @endforeach

                                            @foreach ($livraisons as $livraison)
                                                @if ($livraison)
                                                    @php
                                                        $livraisonType = \App\Models\Livraison::find($livraison->livraison_id);
                                                    @endphp

                                                    @if ($livraisonType->methode == "Livraison")
                                                        <li class="delivery-method-btn active" data-method="{{ $livraisonType->methode }}" data-id="{{ $livraisonType->id }}">
                                                            <i class="fa fa-truck">&nbsp;{{ $livraisonType->methode }}</i>

                                                        </li>
                                                        {{-- <input type="hidden" name="delivery_method" id="delivery_method_input" value="{{ $livraisonType->id }}"> --}}
                                                        <li class="editbtndeliverymodelivraison"><i class="fa fa-edit" style="font-size:36px" data-bs-toggle="modal" data-bs-target="#myModallivraison"></i></li>
                                                    @endif


                                                @endif
                                            @endforeach
                                    </ul>

                                        <ul id="delivery-method-list" class="switchable-list">
                                            @foreach ($livraisons as $livraison)
                                                @if ($livraison)
                                                    @php
                                                    $livraisonType = \App\Models\Livraison::find($livraison->livraison_id);
                                                    @endphp
                                                    @if ($livraisonType->methode == 'Click & Collect')
                                                        <li class="delivery-method-btn active" data-method="{{ $livraisonType->methode }}" data-id="{{ $livraisonType->id }}">
                                                            <i class="fas fa-truck"></i>
                                                            <i class="far fa-clock"></i>
                                                            <span>Sur Place</span>
                                                        </li>
                                                    @else
                                                        @if ($livraisonType->methode == 'Livraison')
                                                            @if ($horaire)

                                                                @php
                                                                    $tnow = date('H:i:s');
                                                                    $tminus = date('H:i:s', strtotime(' -15 minutes'));
                                                                @endphp
                                                                @foreach ($horaire as $item)
                                                                @if (is_array($item) && isset($item['heure_ouverture']) && isset($item['heure_fermeture']))
                                                                    @if (!($item->heure_ouverture < $tnow  && $item->heure_fermeture > $tminus))
                                                                        <li class="delivery-method-btn disabled"  data-method="{{ $livraisonType->methode }}" data-id="{{ $livraisonType->id }}">
                                                                        <i class="fas fa-truck"></i>
                                                                        <i class="far fa-clock"></i>
                                                                        <span>{{ $livraisonType->methode }}</span>
                                                                        </li>

                                                                    @else
                                                                        <li class="delivery-method-btn active"  data-method="{{ $livraisonType->methode }}" data-id="{{ $livraisonType->id }}">
                                                                            <i class="fas fa-truck"></i>
                                                                            <i class="far fa-clock"></i>
                                                                            <span>{{ $livraisonType->methode }}</span>
                                                                        </li>
                                                                    @endif
                                                                    @endif
                                                                @endforeach
                                                            @endif
                                                        @endif

                                                    @endif

                                                @endif
                                            @endforeach
                                            @if(!request()->is('client/login'))
                                                @if ($client)
                                                    @if(!is_null($client->reservation_table) && $client->reservation_table == 1)
                                                        {{-- <a href="{{url('/reservationtables')}}" >Réserver une table </a> --}}
                                                        <li class="delivery-method-btn" onclick="reserver()">
                                                        <i class="glyphicon glyphicon-cutlery"></i>
                                                        <i class="far fa-clock"></i>
                                                        <span>Réserver une table</span>
                                                        </li>
                                                    @endif
                                                @endif
                                            @endif
                                        </ul>

                                @endif
                            </div>
                            <!-- Payment Method -->
                            <div id="paymentMethod">
                                @if (count($paiments) > 0)
                                    <h3>Choisissez la méthode de paiement</h3>
                                    <ul id="payment-method-list" class="switchable-list">
                                        @foreach ($paiments as $paiment)
                                            @php
                                                $paimentType = \App\Models\PaimentMethod::find($paiment->paiment_id);
                                            @endphp
                                            <!-- Assuming the name field for the payment method is 'id' -->
                                            <li class="payment-method-btn" data-method="{{ $paimentType->type_methode }}" data-id="{{ $paimentType->id }}">
                                                <i class="far fa-credit-card"></i> <!-- Icon for credit card, adjust as needed -->
                                                <span>{{ $paimentType->type_methode }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif

                                <br>
                            </div>
                            <script>
                                document.addEventListener('DOMContentLoaded', function () {
                                    var paymentMethodButtons = document.querySelectorAll('.payment-method-btn');

                                    paymentMethodButtons.forEach(function (button) {
                                        button.addEventListener('click', function () {
                                            var dataMethod = this.getAttribute('data-method');
                                            var PaymentTypeId = this.getAttribute('data-id');

                                            // Update the hidden input value
                                            document.getElementById('payment_method_input').value = PaymentTypeId;
                                                var cb = document.getElementById('CartBancaire');
                                                // Toggle active class for switchable buttons
                                                paymentMethodButtons.forEach(function (btn) {
                                                    btn.classList.remove('active');
                                                });
                                                this.classList.add('active');


                                        });
                                    });
                                });
                            </script>
                            <div class="form-group col-xl-12 mb-0">
                                <button type="button" class="btn-custom" id="btncustom" onclick="return validateForm()">Confirmer</button>
                            </div>
                            <input type="hidden" name="payment_method" id="payment_method_input" value="">


                        </form>
                        <script>
                            function validateForm() {
                                var deliveryMethodButtons = document.querySelectorAll('.delivery-method-btn');
                                var selectedDeliveryMethod = Array.from(deliveryMethodButtons).find(btn => btn.classList.contains('active'));

                                var paymentMethodButtons = document.querySelectorAll('.payment-method-btn');
                                    var selectedPaymentMethod = Array.from(paymentMethodButtons).find(btn => btn.classList.contains('active'));

                                if (!selectedDeliveryMethod && selectedPaymentMethod === "") {
                                    alert("Veuillez sélectionner la méthode de livraison et de paiement avant de confirmer la commande.");
                                    return false; // Prevent form submission
                                } else if (!selectedDeliveryMethod) {
                                    alert("Veuillez sélectionner une méthode de livraison avant de confirmer la commande.");
                                    return false; // Prevent form submission
                                } else if (!selectedPaymentMethod) {
                                        alert("Veuillez sélectionner une méthode de paiement avant de confirmer la commande.");
                                        return false; // Prevent form submission
                                    }
                                    var nom = document.getElementById("nom").value;
                                    var prenom = document.getElementById('prenom').value;
                                    var tel = document.getElementById('num1').value;
                                    if(nom==="" || prenom==="" || tel===""){
                                        alert('Veillez remplir tous le champs');
                                        return false;
                                    }else{
                                        $.ajax({
                                                    headers: {
                                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                    },
                                                    url: '/check-user-commands',
                                                    method: 'GET',
                                                    data: {
                                                        nom: nom,
                                                        prenom: prenom,
                                                        tel: tel

                                                    },
                                                    success: function(response) {
                                                        console.log(response);
                                                        if (response.commands==0){

                                                            fetchForDrinks();

                                                        }else{
                                                            $('#checkoutForm').submit();
                                                        }
                                                    },
                                                    error: function(xhr) {
                                                        console.error(xhr.responseText);
                                                    }
                                                });
                                    }

                                // return true; // Allow form submission
                            }
                        </script>
            @else
                <div class="check-form">
                    <div class="row">
                        <!-- Buyer Info -->
                        <h4>Entrer vos détailles</h4>

                        <div class="col-xl-11">
                            <form id="checkoutForm"  method="POST" action="{{ route('client.checkout1.store') }}"  class="check-validation">
                                @csrf
                                <div class="row">
                                    <div class="form-group col-xl-6">
                                        <label>Nom <span class="text-danger">*</span></label>
                                        <input type="text" placeholder="Nom" name="nom" id="nom" class="form-control" value="" required="">
                                    </div>
                                    <div class="form-group col-xl-6">
                                        <label>Prénom <span class="text-danger">*</span></label>
                                        <input type="text" placeholder="Prénom" name="prenom" id="prenom" class="form-control" value="" required="">
                                    </div>
                                </div>
                                <div id="address-div" style="display: none;">

                                    <div class="form-group col-xl-12" id="input-container">
                                        <label> Adresse<span class="text-danger">*</span></label>
                                        <div style="display: flex; align-items: center;">
                                            <i class="mdi mdi-crosshairs-gps geolocation-icon"  onclick="geolocation()" style="font-size: xx-large; cursor: pointer;"></i>
                                            <input type="text" placeholder="Adresse" name="adresse" class="form-control" value="" required="">


                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-xl-6">
                                            <label>Code Postal<span class="text-danger">*</span></label>
                                            <input type="text" placeholder="Code Postal" name="codePostal" class="form-control" value="">
                                        </div>
                                        <div class="form-group col-xl-6">
                                            <label>Ville <span class="text-danger">*</span></label>
                                            <input type="text" placeholder="Ville" name="ville" class="form-control" value="" required="">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-xl-6">
                                        <label>Numéro de téléphone 1<span class="text-danger">*</span></label>
                                        <input type="text" placeholder="Numéro de téléphone " name="num1" id="num1" class="form-control" value="" required="">
                                    </div>
                                    <div class="form-group col-xl-6">
                                        <label>Numéro de téléphone 2</label>
                                        <input type="text" placeholder="Numéro de téléphone " name="num2" class="form-control" value="" >
                                    </div>
                                </div>
                                <div class="row">
                                    <br>
                                    <div class="form-group">
                                        <input id="checkbox" name="creer_un_compte" type="checkbox" class="form-control-light">
                                        <label>Créer un compte</label>
                                    </div>
                                    <div id="accountFields" style="display: none;">
                                        <div class="form-group col-xl-12">
                                            <label>Adresse Email  <span class="text-danger">*</span></label>
                                            <input type="email" placeholder=" Adresse Email" name="email" class="form-control" value="" >
                                        </div>
                                        <div class="form-group">
                                            <label>Mot de passe <span class="text-danger">*</span></label>

                                            <input id="password" type="password" class="form-control form-control-light" name="password" placeholder="Mot de passe"  autocomplete="new-password">
                                        </div>

                                    </div>


                                    <p>Vous avez déjà un compte? <a href="{{ route('client.login') }}">Connexion</a> </p>

                                </div>
                                <!-- Cart Details and Delivery Method -->
                                <div id="cartDetailsAndDeliveryMethod">
                                    @if (count($livraisons) > 0)
                                        <h3>La méthode de livraison choisie</h3>


                                        <ul id="delivery-method-list" class="switchable-list">

                                            @if (Cookie::get('ccmode') !== null)

                                                @foreach ($livraisons as $livraison)
                                                    @if ($livraison)
                                                        @php
                                                            $livraisonType = \App\Models\Livraison::find($livraison->livraison_id);
                                                        @endphp

                                                        @if ($livraisonType->methode == "Click & Collect")
                                                            <li class="delivery-method-btn active" data-method="{{ $livraisonType->methode }}" data-id="{{ $livraisonType->id }}" >
                                                                <i class="fas fa-truck">&nbsp;Sur Place</i>

                                                                <i class="fa fa-calendar" id="ccdatetime"></i>



                                                            </li>
                                                            <br>
                                                            {{-- <input type="hidden" name="delivery_method" id="delivery_method_input" value="{{ $livraisonType->id }}"> --}}
                                                            <li class="editbtndeliverymode"><i class="fa fa-edit" style="font-size:36px"  data-bs-toggle="modal" data-bs-target="#myModalcc"></i></li>

                                                        @endif


                                                    @endif
                                                @endforeach
                                            @endif
                                            @if (Cookie::get('deliverymode') !== null)
                                                @foreach ($livraisons as $livraison)
                                                    @if ($livraison)
                                                        @php
                                                            $livraisonType = \App\Models\Livraison::find($livraison->livraison_id);
                                                        @endphp

                                                        @if ($livraisonType->methode == "Livraison")
                                                            <li class="delivery-method-btn active" data-method="{{ $livraisonType->methode }}" data-id="{{ $livraisonType->id }}">
                                                                <i class="fa fa-truck">&nbsp;{{ $livraisonType->methode }}</i>

                                                            </li>
                                                            {{-- <input type="hidden" name="delivery_method" id="delivery_method_input" value="{{ $livraisonType->id }}"> --}}
                                                            <li class="editbtndeliverymodelivraison"><i class="fa fa-edit" style="font-size:36px" data-bs-toggle="modal" data-bs-target="#myModallivraison"></i></li>
                                                        @endif


                                                    @endif
                                                @endforeach

                                            @endif

                                        </ul>
                                        @if (Cookie::get('deliverymode') === null && Cookie::get('ccmode') === null)

                                            <ul id="delivery-method-list" class="switchable-list">
                                                @foreach ($livraisons as $livraison)
                                                    @if ($livraison)
                                                        @php
                                                            $livraisonType = \App\Models\Livraison::find($livraison->livraison_id);
                                                        @endphp
                                                        @if ($livraisonType->methode == 'Click & Collect')
                                                            <li class="delivery-method-btn" data-method="{{ $livraisonType->methode }}" data-id="{{ $livraisonType->id }}">
                                                                <i class="fas fa-truck"></i>
                                                                <i class="far fa-clock"></i>
                                                                <span>Sur Place</span>
                                                            </li>
                                                        @else
                                                            @if ($livraisonType->methode == 'Livraison')
                                                                @php
                                                                    $restaurant_id = env('Restaurant_id');
                                                                    $horaire = DB::table('horaires')->where('client_id', $restaurant_id)->get();

                                                                @endphp
                                                                @if ($horaire)

                                                                    @php
                                                                        $tnow = date('H:i:s');
                                                                        $tminus = date('H:i:s', strtotime(' -15 minutes'));
                                                                    @endphp
                                                                    @foreach ($horaire as $item)

                                                                        @if (!($item->heure_ouverture < $tnow  && $item->heure_fermeture > $tminus))
                                                                            <li class="delivery-method-btn disabled"  data-method="{{ $livraisonType->methode }}" data-id="{{ $livraisonType->id }}">
                                                                                <i class="fas fa-truck"></i>
                                                                                <i class="far fa-clock"></i>
                                                                                <span>{{ $livraisonType->methode }}</span>
                                                                                {{-- <input type="hidden" name="delivery_method" id="delivery_method_input" value="{{ $livraisonType->id }}"> --}}
                                                                            </li>

                                                                        @else
                                                                            <li class="delivery-method-btn"  data-method="{{ $livraisonType->methode }}" data-id="{{ $livraisonType->id }}">
                                                                                <i class="fas fa-truck"></i>
                                                                                <i class="far fa-clock"></i>
                                                                                <span>{{ $livraisonType->methode }}</span>
                                                                                {{-- <input type="hidden" name="delivery_method" id="delivery_method_input" value="{{ $livraisonType->id }}"> --}}
                                                                            </li>
                                                                        @endif
                                                                    @endforeach
                                                                @endif
                                                            @endif

                                                        @endif

                                                    @endif
                                                @endforeach
                                                @if(!request()->is('client/login'))
                                                    @if ($client)
                                                        @if(!is_null($client->reservation_table) && $client->reservation_table == 1)
                                                            {{-- <a href="{{url('/reservationtables')}}" >Réserver une table </a> --}}
                                                            <li class="delivery-method-btn" onclick="reserver()">
                                                            <i class="glyphicon glyphicon-cutlery"></i>
                                                            <i class="far fa-clock"></i>
                                                            <span>Réserver une table</span>
                                                            </li>
                                                        @endif
                                                    @endif
                                                @endif
                                            </ul>
                                        @endif
                                            <div id="ccdelivery-time-container" style="display: none;">
                                                <div class="form-group">
                                                    <label for="date">Date</label>
                                                        <input type="date" class="form-control" id="date" name="date" required>
                                                            @error('date')
                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                            @enderror
                                                </div>

                                                <div id="delivery-time-container">
                                                    <label for="delivery_time">Choisissez l'heure :</label>
                                                    <select id="delivery_time" name="delivery_time" class="form-control">
                                                        <option value="" disabled selected>Sélectionnez une heure</option>
                                                    </select>
                                                </div>
                                                <br>
                                            </div>
                                            <div class="livraisonContent" id="livraisonContent" style="display:none;">
                                                {{-- <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
                                                        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
                                                        crossorigin=""></script>
                                                <script src="https://cdn.jsdelivr.net/npm/geolib@3.3.4/lib/index.min.js"></script>

                                                <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
                                                <script src="https://cdn.jsdelivr.net/npm/leaflet@1.7.1/dist/leaflet.js"></script>
                                                <style>
                                                    #map{
                                                        height: 350px !important;
                                                        width: 100%;
                                                    }
                                                </style>
                                                <div id="map"></div>
                                                <script>
                                                    let map;
                                                    document.addEventListener("DOMContentLoaded", function () {
                                                        map = L.map('map',{
                                                            maxWidth: '100%',
                                                            maxHeight: '350px',
                                                            updateWhenIdle: true
                                                        }).setView([32.92905968163634, 10.448639221238507], 13);
                                                        const tiles = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                                        maxZoom: 19,
                                                        attribution: false
                                                        }).addTo(map);
                                                        oldMarker = L.marker([32.92905968163634, 10.448639221238507],{draggable: false}).addTo(map);
                                                        map.setView([32.92905968163634, 10.448639221238507], 13);
                                                        setTimeout(function() {
                                                            map.invalidateSize();
                                                            }, 100);
                                                    });
                                                </script> --}}

                                            </div>
                                        @endif
                                </div>
                                <!-- Payment Method -->
                                <div id="paymentMethod">
                                    @if (count($paiments) > 0)
                                        <h3>Choisissez la méthode de paiement</h3>
                                        <ul id="payment-method-list" class="switchable-list">
                                            @foreach ($paiments as $paiment)
                                                @php
                                                    $paimentType = \App\Models\PaimentMethod::find($paiment->paiment_id);
                                                @endphp
                                                <!-- Assuming the name field for the payment method is 'id' -->
                                                @if ($paimentType->type_methode=="Sur Place")
                                                <li class="payment-method-btn" data-method="{{ $paimentType->type_methode }}" data-id="{{ $paimentType->id }}">
                                                    <i class="far fa-credit-card"></i> <!-- Icon for credit card, adjust as needed -->
                                                    <span>Espèce</span>
                                                </li>
                                                @endif

                                            @endforeach
                                        </ul>
                                    @endif

                                    <br>
                                </div>

                                <script>
                                    document.addEventListener('DOMContentLoaded', function () {
                                        var paymentMethodButtons = document.querySelectorAll('.payment-method-btn');

                                        paymentMethodButtons.forEach(function (button) {
                                            button.addEventListener('click', function () {
                                                var dataMethod = this.getAttribute('data-method');
                                                var PaymentTypeId = this.getAttribute('data-id');

                                                // Update the hidden input value
                                                document.getElementById('payment_method_input').value = PaymentTypeId;

                                                // Toggle active class for switchable buttons
                                                paymentMethodButtons.forEach(function (btn) {
                                                    btn.classList.remove('active');
                                                });
                                                this.classList.add('active');
                                                if(dataMethod =="Carte Bancaire"){
                                                    var cb= document.getElementById("CartBancaire");
                                                    cb.style.display = "block";
                                                    document.getElementById("btncustom").disabled = true;
                                                }else{
                                                    var cb= document.getElementById("CartBancaire");
                                                    cb.style.display = "none";
                                                    document.getElementById("btncustom").disabled = false;
                                                }
                                            });

                                        });
                                    });
                                </script>
                                <div class="form-group col-xl-12 mb-0">
                                    <button type="button"   class="btn-custom" id="btncustom" onclick="return validateForm()">Confirmer</button>
                                </div>
                                <input type="hidden" name="delivery_method" id="delivery_method_input" value="">
                                <input type="hidden" name="payment_method" id="payment_method_input" value="">


                            </form>
                            <script>
                                function validateForm() {

                                    var paymentMethodButtons = document.querySelectorAll('.payment-method-btn');
                                    var selectedPaymentMethod = Array.from(paymentMethodButtons).find(btn => btn.classList.contains('active'));
                                    var deliveryMethode = document.querySelectorAll('.delivery-method-btn');
                                    selectedDeliveryMethod = Array.from(deliveryMethode).find(btn => btn.classList.contains('active'));
                                    if(!selectedDeliveryMethod){
                                        alert("Veuillez sélectionner une méthode livraison.");
                                        return false; // Prevent form submission
                                    }
                                    if (!selectedPaymentMethod) {
                                        alert("Veuillez sélectionner une méthode de paiement avant de confirmer la commande.");
                                        return false; // Prevent form submission
                                    }
                                    var nom = document.getElementById("nom").value;
                                    var prenom = document.getElementById('prenom').value;
                                    var tel = document.getElementById('num1').value;
                                    var selected_delivery_methode = selectedDeliveryMethod.getAttribute('data-method');
                                    if (selected_delivery_methode=="Livraison"){
                                        var address = $('input[name=adresse]').val();
                                        var city = $('input[name=ville]').val();
                                        var zipcode = $('input[name=codePostal]').val();
                                        if(address==""||city==""||zipcode==""){
                                            alert('Veillez entrer votre adresse !');
                                            return false;
                                        }
                                    }
                                    if(nom==="" || prenom==="" || tel===""){
                                        alert('Veillez remplir tous le champs');
                                        return false;
                                    }else{
                                        $.ajax({
                                                    headers: {
                                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                    },
                                                    url: '/check-user-commands',
                                                    method: 'GET',
                                                    data: {
                                                        nom: nom,
                                                        prenom: prenom,
                                                        tel: tel

                                                    },
                                                    success: function(response) {
                                                        console.log(response);
                                                        if (response.commands==0){

                                                            fetchForDrinks();

                                                        }else{
                                                            $('#checkoutForm').submit();
                                                        }
                                                    },
                                                    error: function(xhr) {
                                                        console.error(xhr.responseText);
                                                    }
                                                });
                                    }

                                    // return true; // Allow form submission
                                }

                            </script>
                        </div>
                    </div>


                </div>
            @endif
            @else
                <p>Aucun article dans le panier.</p>
            @endif
        <script>
            function goBack() {
              //  window.history.back();
                 //var detailscustom = document.getElementById('container_details');

                 var CartBancaireContainer = document.getElementById('CartBancaire');

                    CartBancaireContainer.style.display = 'none';

                    //CartBancaireContainer.style.display = 'block';
            }
        </script>
        <script type="text/javascript" src="https://js.stripe.com/v2/"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            // JavaScript to handle form visibility toggling
            const confirmOrderBtn = document.getElementById('confirmOrderBtn');
            const cartDetailsAndDeliveryMethod = document.getElementById('cartDetailsAndDeliveryMethod');
            const confirmDeliveryMethodBtn = document.getElementById('confirmDeliveryMethodBtn');
            const paymentMethod = document.getElementById('paymentMethod');
            const container_details = document.getElementById('container_details');
            const registerAndCheckoutBtn = document.getElementById('registerAndCheckoutBtn');
            const successMessage = document.getElementById('successMessage');
            $(document).ready(function () {
                $(".btn-remove").click(function () {
                    var button = $(this); // Get the clicked button
                    var row = button.closest("tr");
                    var productId = row.attr("data-product-id");

                    $.ajax({
                            headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            url: '{{ route("remove.CartItem") }}',
                            method: 'POST',
                            data: {
                                productId: productId,
                            },
                            success: function (response) {
                                // Update the UI and total price based on the response
                                row.remove();
                                // Update the cart item count in the header
                                var cartItemCount = $('.cart-item-count');
                                cartItemCount.text(response.cartItemCount);
                                var totalPriceElement = $('.totalprice span');
                                // Remove the row from the table
                                totalPriceElement.text(response.totalPrice + ' TND'); // Update total price
                            },
                            error: function (error) {
                                console.error('Error removing item:', error);
                                // Handle error gracefully
                            }
                        });
                });
                $(".btn-quantity").click(handleQuantityUpdate);

                function handleQuantityUpdate(event) {
                    const productId = event.target.getAttribute('data-product');
                    const action = event.target.getAttribute('data-action');
                    const inputElement = document.getElementById('quantity_' + productId);

                    let newQuantity = parseInt(inputElement.value);
                    if (action === 'increase') {
                        newQuantity++;
                    } else if (action === 'decrease' && newQuantity > 1) {
                        newQuantity--;
                    }

                    // Update the input element value immediately
                    inputElement.value = newQuantity;

                    // Send the updated quantity to the server
                    updateQuantity(productId, newQuantity);
                }

                function updateQuantity(productId, quantity) {
                    fetch('{{ route("update.quantity") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ productId, quantity })
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log(data.message);
                        // You can update the UI here to reflect the updated quantity in the cart
                    })
                    .catch(error => {
                        console.error(error);
                        // Handle any errors that may occur during the update
                    });
                }
            });
            // function getCookie(name) {
            //     // Split cookie string and get all individual name=value pairs in an array
            //     let cookieArr = document.cookie.split(";");

            //     // Loop through the array elements
            //     for(let i = 0; i < cookieArr.length; i++) {
            //         let cookiePair = cookieArr[i].split("=");

            //         /* Removing whitespace at the beginning of the cookie name
            //         and compare it with the given string */
            //         if(name == cookiePair[0].trim()) {
            //             // Decode the cookie value and return
            //             return decodeURIComponent(cookiePair[1]);
            //         }
            //     }

            //     // Return null if not found
            //     return null;
            // }
            $(document).ready(function() {
                // $('input[name=adresse]').val(getCookie('clientAdress'));
                // $('input[name=codePostal]').val(getCookie('postal_code'));
                // $('input[name=ville]').val(getCookie('clientCity'));
                // Select the checkbox and the container to show/hide
                var checkbox = $('#checkbox');
                var accountFields = $('#accountFields');

                // Toggle visibility when the checkbox state changes
                checkbox.change(function() {
                    if (checkbox.is(':checked')) {
                        accountFields.show(); // Show the fields
                    } else {
                        accountFields.hide(); // Hide the fields
                    }
                });
            });







            /*------------------------------------------
            --------------------------------------------
            Stripe Payment Code
            --------------------------------------------
            --------------------------------------------*/
            var $checkform = $(".check-validation");
            $(document).ready(function () {
                var $checkform = $(".check-validation");

                $checkform.on('submit', function (e) {
                    e.preventDefault(); // Prevent the default form submission
                    var selectedPaymentMethod = document.querySelector('.payment-method-btn.active');
                var CartBancaireContainer = document.getElementById('CartBancaire');
                    var btncustom = document.getElementById('container_details');
                    var dataMethod = selectedPaymentMethod.getAttribute('data-method');
                    console.log('Payment Method:', dataMethod);

                    if (dataMethod === "Carte Bancaire") {
                        CartBancaireContainer.style.display = 'block';

                        //btncustom.style.display = 'none';
                    } else{
                        //btncustom.style.display = 'block';
                        CartBancaireContainer.style.display = 'none';

                    var formData = new FormData($checkform[0]);
                    console.log(formData);

                    $checkform.get(0).submit();}
                });
            });
            $(function() {


                var  $checkform = $(".check-validation");
                var $form = $(".require-validation");

                $('form.require-validation').bind('submit', function(e) {
                    e.preventDefault(); // Prevent the default form submission
                    var $form = $(".require-validation"),
                    inputSelector = ['input[type=email]', 'input[type=password]',
                                    'input[type=text]', 'input[type=file]',
                                    'textarea'].join(', '),
                    $inputs = $form.find('.required').find(inputSelector),
                    $errorMessage = $form.find('div.error'),
                    valid = true;
                    $errorMessage.addClass('hide');

                    $('.has-error').removeClass('has-error');
                    $inputs.each(function(i, el) {
                    var $input = $(el);
                    if ($input.val() === '') {
                        $input.parent().addClass('has-error');
                        $errorMessage.removeClass('hide');
                        e.preventDefault();
                    }
                    });

                    if (!$form.data('cc-on-file')) {
                        e.preventDefault();
                        Stripe.setPublishableKey('{{ env('STRIPE_KEY') }}');

                        Stripe.createToken({
                            number: $('.card-number').val(),
                            cvc: $('.card-cvc').val(),
                            exp_month: $('.card-expiry-month').val(),
                            exp_year: $('.card-expiry-year').val()
                        }, stripeResponseHandler);
                    }

                });

                /*------------------------------------------
                --------------------------------------------
                Stripe Response Handler
                --------------------------------------------
                --------------------------------------------*/
                function stripeResponseHandler(status, response) {
                    if (response.error) {
                        $('.error')
                            .removeClass('hide')
                            .find('.alert')
                            .text(response.error.message);
                    } else {
                        var Payment = document.getElementById('payment_method');
                        var PaymentMethod = Payment.value;
                        var selectedOption = Payment.options[Payment.selectedIndex];

                        var dataMethod = selectedOption.getAttribute('data-method');
                        var paymentMethodId = selectedOption.value;

                        var token = response.id; // Use response.id to get the token

                        // Prevent the default form submission
                        // e.preventDefault();

                        $form.find('input[type=text]').empty();
                        $form.append("<input type='hidden' name='stripeToken' value='" + token + "'/>");

                        var routeUrl = '{{ route("stripe.post", ["paymentMethodId" => ":paymentMethodId"]) }}';
                        routeUrl = routeUrl.replace(':paymentMethodId', paymentMethodId);
                        // Make an AJAX request to your server to process the payment
                        $.ajax({
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                url: routeUrl,
                                method: 'POST',
                                data: {
                                    stripeToken: token // Send the Stripe token to your server
                                },
                                success: function (response) {
                                    // Handle the success response from your server
                                    console.log('Payment successful:', response.message);
                                    var $checkform = $(".check-validation");

                                    $checkform.get(0).submit();


                                },
                                error: function (error) {
                                    console.error('Error processing payment:', error.responseJSON.message);
                                    // Handle the error gracefully, e.g., show an error message to the user
                                }
                            });
                    }
                }
            });
        </script>
        <script type="text/javascript" src="https://js.stripe.com/v2/"></script>
                </div>
            </div>
        </div>


    </div>
</section>
<script>
    // $(document).ready(function(){

    //     $('.editbtndeliverymode').click(function(){

    //        // $('#staticBackdrop').show();
    //        var deliverydate = document.getElementById('date_edit');
    //           var deliverytime = document.getElementById('delivery_time_edit');
    //         //   deliverytime.value = document.getElementById('heure_ouverture').value;
    //           console.log(document.getElementById('heure_ouverture').value);
    //           populateDeliveryTimeOptions(deliverytime,document.getElementById('heure_ouverture').value,document.getElementById('heure_fermeture').value,deliverydate.value);
    //           deliverydate.valueAsDate = new Date();
    //     });


    // });
    // function livraisonChecked(){
    //     var checkBox = document.getElementById("livraisoncheckbox");
    //     if (checkBox.checked == true){
    //         $('#date_edit').attr('disabled','disabled');
    //         $('#delivery_time_edit').attr('disabled','disabled');
    //     }else{
    //         $('#date_edit').removeAttr('disabled');
    //         $('#delivery_time_edit').removeAttr('disabled');
    //     }
    // }

    // function savemodificationmodalcc(){
    //     var checkBox = document.getElementById("livraisoncheckbox");
    //     if (checkBox.checked == false){
    //         document.cookie = "ccdate="+ $('#date_edit').val();
    //         document.cookie = "cctime="+ $('#delivery_time_edit').val();
    //         $('#ccdatetime').text(" "+$('#date_edit').val()+" "+$('#delivery_time_edit').val());

    //     }else if (checkBox.checked == true){
    //         document.cookie.split(";").
    //         forEach(function(c) {
    //                                 document.cookie = c.replace(/^ +/, "").replace(/=.*/, "=;expires=" + new Date().toUTCString() + ";path=/");
    //                             });
    //         var url ="{{ route('client.products.index') }}";

    //         location.href = url;
    //         document.cookie = "comefrom=/checkout";
    //     }

    // }
    // function savemodificationmodalliv(){
    //     var checkBoxcc = document.getElementById("cccheckbox");
    //     if (checkBoxcc.checked == false){
    //         document.cookie = "compadresse=" +$('#address-input').val();
    //         document.cookie= "ville="+$('#ville').val();
    //         document.cookie= "rue="+$('#road').val();
    //         document.cookie= "postal_code="+ $('#codepostale').val();
    //         location.reload();
    //     }else if (checkBoxcc.checked == true){
    //         document.cookie = "postal_code=;expires=" + new Date(0).toUTCString();
    //         document.cookie = "deliverymode=;expires=" + new Date(0).toUTCString();
    //         document.cookie = "clientlng=;expires=" + new Date(0).toUTCString();
    //         document.cookie = "clientLat=;expires=" + new Date(0).toUTCString();
    //         document.cookie = "clientAdress=;expires=" + new Date(0).toUTCString();
    //         document.cookie = "clientCity=;expires=" + new Date(0).toUTCString();
    //         document.cookie = "ccmode="+ true;
    //         document.cookie = "ccdate="+$('#date_cc').val();
    //         document.cookie = "cctime="+$('#delivery_time_cc').val();
    //         location.reload();

    //     }
    // }

    function populateDeliveryTimeOptions(delverytimeselect,heureouverture,heurefermeture,ndate) {
        delverytimeselect.innerHTML = '';

          var now = new Date();


        const formatDate = date => {
          return date.toISOString().split('T')[0]; // YYYY-mm-dd
        };
        var datenow = formatDate(now);
        var datenew = ndate;
        const currentHour = now.getHours()+1;
        var ho = heureouverture.substr(0,2);
        var hf= heurefermeture.substr(0,2);
        var hoint = parseInt(ho);
        var hfint = parseInt(hf);
        if (ndate === datenow){
            if (currentHour<=hoint+1 && currentHour<hfint-1){
                for (let hour = hoint+1; hour <= hfint-1; hour++) {
                    for (let minute = 0; minute <= 45; minute += 15) {
                        const formattedHour = hour.toString().padStart(2, '0');
                        const formattedMinute = minute.toString().padStart(2, '0');

                        const option = document.createElement('option');
                        option.value = `${formattedHour}:${formattedMinute}`;
                        option.text = `${formattedHour}:${formattedMinute}`;

                        delverytimeselect.appendChild(option);
                    }
                }
            }else if (currentHour>= hoint+1 && currentHour<hfint-1){
                for (let hour = currentHour; hour <= hfint-1; hour++) {
                    for (let minute = 0; minute <= 45; minute += 15) {
                        const formattedHour = hour.toString().padStart(2, '0');
                        const formattedMinute = minute.toString().padStart(2, '0');

                        const option = document.createElement('option');
                        option.value = `${formattedHour}:${formattedMinute}`;
                        option.text = `${formattedHour}:${formattedMinute}`;

                        delverytimeselect.appendChild(option);

                    }
                }
            }
        }else{
            for (let hour = hoint+1; hour <= hfint-1; hour++) {
                for (let minute = 0; minute <= 45; minute += 15) {
                    const formattedHour = hour.toString().padStart(2, '0');
                    const formattedMinute = minute.toString().padStart(2, '0');

                    const option = document.createElement('option');
                    option.value = `${formattedHour}:${formattedMinute}`;
                    option.text = `${formattedHour}:${formattedMinute}`;

                    delverytimeselect.appendChild(option);
                }
            }
        }
    }
    function geolocation(){

        console.log("geo clicked");
        if ("geolocation" in navigator) {
            navigator.geolocation.getCurrentPosition(function (position) {
              const latitude = position.coords.latitude;
              const longitude = position.coords.longitude;
              getAddress(latitude, longitude);
            });
          } else {
            alert("La géolocalisation n'est pas prise en charge dans ce navigateur.");
          }
    }
    function getAddress(latitude, longitude) {
        if (latitude && longitude) {
          fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${latitude}&lon=${longitude}`)
            .then(response => response.json())
            .then(data => {
                console.log(data);
              const state = data.address.state;
              const region = data.address.state_district;
              const road = data.address.county;
              const postalcode = data.address.postcode;
              const adresse = document.querySelector("input[name='adresse']");
              adresse.value = region+","+road+","+state+","+3200;
            //   const ville = document.getElementById('ville');
            //   ville.value="dfgdfgdfg";
            //   console.log('ville = ',state);
            //   ville.value = state;
              const rue = document.querySelector("input[name='ville']");
              rue.value = road;
              const codp = document.querySelector("input[name='codePostal']");
              codp.value = postalcode;

            //   document.cookie = "clientAdress=" + adresse.value;
            //   document.cookie = "clientLat=" + latitude;
            //   document.cookie = "clientlng=" + longitude;
            //   document.cookie = "postal_code=" + postalcode;
            })

            .catch(error => {
              console.error("Error fetching postal code:", error);
            });
        }
    }

    // function ccChecked(){
    //     var checkBox = document.getElementById("cccheckbox");
    //     if (checkBox.checked == true){
    //             $('#ville').attr('disabled','disabled');
    //             $('#road').attr('disabled','disabled');
    //             $('#codepostale').attr('disabled','disabled');
    //             $('#address-input').attr('disabled','disabled');
    //             $('#geoicon').attr('disabled','disabled');
    //             $('#geoicon').css("cursor",'not-allowed');
    //             $('.address-container').css('display','none');
    //             $('.ccon').css('display','block');
    //             var deliverydate = document.getElementById('date_cc');
    //             var deliverytime = document.getElementById('delivery_time_cc');
    //             populateDeliveryTimeOptions(deliverytime,document.getElementById('heure_ouverture').value,document.getElementById('heure_fermeture').value,deliverydate.value);
    //             deliverydate.valueAsDate = new Date();

    //     }else{
    //         $('#address-input').removeAttr('disabled');
    //         $('#ville').removeAttr('disabled');
    //         $('#road').removeAttr('disabled');
    //         $('#codepostale').removeAttr('disabled');
    //         $('#geoicon').removeAttr('disabled');
    //         $('#geoicon').css("cursor",'pointer');
    //         $('.address-container').css('display','block');
    //         $('.ccon').css('display','none');
    //     }
    // }
    // $(document).ready(function($) {
    //     var datepicker = document.getElementById('date_edit');
    //     datepicker.valueAsDate=new Date();
    //     $.ajax({
    //             url: "/gethoraire/"+datepicker.value,
    //             type: 'GET',
    //             dataType: "json",
    //             success: function(data) {
    //                 // log response into console
    //                 // console.log(data);
    //                 // console.log(data.horaire[0].heure_ouverture);
    //                 // console.log(data.horaire[0].heure_fermeture);
    //                 populateDeliveryTimeOptions(document.getElementById('delivery_time'),data.horaire[0].heure_ouverture,data.horaire[0].heure_fermeture,datepicker.value);
    //             }
    //         });
    //     $('#date_edit').change(function() {
    //         var date = new Date($(this).val());
    //         const day = date.getDay();
    //         const selectedday = date.getDate();


    //         $.ajax({
    //             url: "/gethoraire/"+datepicker.value,
    //             type: 'GET',
    //             dataType: "json",
    //             success: function(data) {
    //                 // log response into console
    //                 // console.log(data);
    //                 // console.log(data.horaire[0].heure_ouverture);
    //                 // console.log(data.horaire[0].heure_fermeture);
    //                 populateDeliveryTimeOptions(document.getElementById('delivery_time_edit'),data.horaire[0].heure_ouverture,data.horaire[0].heure_fermeture,datepicker.value);
    //             }
    //         });
    //     });

    // });
    // $(document).ready(function(){
    //     if(getCookie('comefrom')!= null){
    //         document.cookie = "comefrom=;expires=" + new Date(0).toUTCString();
    //     }
    // });
</script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@latest/dist/confetti.browser.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.9.1/gsap.min.js"></script>
    <div id="winnerPopup" class="modal-popup" style="display: none;">
        <div class="modal-popup-content">
            <span class="close" id="closeWinnerPopup">&times;</span>
            <h2>Félicitations !</h2>
            <p>Vous avez gagné! 🎉</p>
            <p>Un boisson gratuit!</p>
            <button id="claimPrize">Choisir un boisson</button>
            <button id="esc_claim">Non merci</button>
        </div>
    </div>
    <div id="drinksPopup" class="modal-drinks-popup" style="display: none;">
      <div class="modal-drinks-popup-content">
          {{-- <span class="close" id="closeWinnerPopup">&times;</span> --}}
          <h2>Choisie votre boisson préférée</h2>
          <form action="#" method="post" id="drinks_form">

          </form>
          <button id="ok">Ok</button>
      </div>
    </div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<style>
    .modal-popup {
        display: none; /* Hidden by default */
        position: fixed; /* Stay in place */
        z-index: 9999; /* High z-index to sit on top */
        left: 0;
        top: 0;
        width: 100%; /* Full width */
        height: 100%; /* Full height */
        background-color: rgba(0, 0, 0, 0.7); /* Black background with opacity */
        justify-content: center; /* Center modal content horizontally */
        align-items: center; /* Center modal content vertically */
    }

    .modal-popup-content {
        background-color: white;
        padding: 20px;
        border-radius: 8px;
        width: 300px; /* Width of the popup */
        text-align: center;
        transform: scale(0); /* Start scaled down for animation */
        transition: transform 0.5s ease; /* Smooth scaling transition */
    }
    .modal-drinks-popup {
        display: none; /* Hidden by default */
        position: fixed; /* Stay in place */
        z-index: 9999; /* High z-index to sit on top */
        left: 0;
        top: 0;
        width: 100%; /* Full width */
        height: 100%; /* Full height */
        background-color: rgba(0, 0, 0, 0.7); /* Black background with opacity */
        justify-content: center; /* Center modal content horizontally */
        align-items: center; /* Center modal content vertically */
    }

    .modal-drinks-popup-content {
        background-color: white;
        padding: 20px;
        border-radius: 8px;
        width: 300px; /* Width of the popup */
        text-align: center;
        transform: scale(0); /* Start scaled down for animation */
        transition: transform 0.5s ease; /* Smooth scaling transition */
        display: flex; /* Flexbox layout */
        flex-direction: column; /* Vertical alignment */
    }
    #drinks_form {
        display: flex;
        align-content: flex-start;
        flex-direction: column;
        align-items: baseline;
    }
    canvas {
        position: fixed; /* Position it relative to the viewport */
        top: 0;
        left: 0;
        width: 100vw; /* Cover the full width */
        height: 100vh; /* Cover the full height */
        pointer-events: none; /* Allow clicks to go through */
        z-index: 9999; /* Ensure it's on top */
    }
</style>
<script>
    $("#esc_claim").click(function(){
        // window.location="/checkout";
        const modalContent = document.querySelector('.modal-popup-content');
        const winnerPopup = document.getElementById('winnerPopup');
        clearInterval(confettiInterval); // Stop the confetti interval
            gsap.to(modalContent, { scale: 0, duration: 0.5, onComplete: () => {
                winnerPopup.style.display = 'none'; // Hide the modal after animation
                // document.body.removeChild(myCanvas); // Remove canvas after use
                // window.location="/checkout";
            }});
            $('#checkoutForm').submit();
    });
</script>
<script>
    let freedrinkscart;
    let boissons=[];
    let boisson;
    let drinkExists=false;
    let updated_free_drink = false;
    function fetchForDrinks(){
        var today = new Date();
        var endpromo = new Date(2024, 7, 31);
        var nom = document.getElementById("nom").value;
        var prenom = document.getElementById('prenom').value;
        var tel = document.getElementById('num1').value;
        if(nom==="null" || prenom==="" || tel===""){

            alert('Veillez remplir tous le champs');
            return ;
        }
        if(today.getTime()<endpromo.getTime()){
            $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "/fetch-cart-for-drinks",
                    type: 'GET',
                    success: function(data) {
                            freedrinkscart = data.cart;
                            console.log("Cart fetched successfully:", freedrinkscart);


                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching cart:', error);
                    }
                });

            $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '/marwen',
                    method: 'GET',
                    success: function(response) {

                        if (response.drinkExists) {
                            showWinnerPopup();
                        }else{
                            showWinnerPopup();
                        }
                    },
                    error: function(xhr) {
                        console.error('Error fetching data:', xhr.responseText);
                    }
                });
            }else{
                $('#checkoutForm').submit();
            }

        }
        let confettiInterval;

        function showWinnerPopup() {
            $('#btncustom').attr('disabled','disabled');
            // document.getElementById("btncustom").disabled = true;
            const winnerPopup = document.getElementById('winnerPopup');
            const modalContent = document.querySelector('.modal-popup-content');
            const closePopupButton = document.getElementById('closeWinnerPopup');

            // Show the modal
            winnerPopup.style.display = 'flex'; // Use flex to center content
            gsap.to(modalContent, { scale: 1, duration: 0.5, ease: "bounce.out" }); // Animate popup scaling

            // Create a canvas for confetti
            const myCanvas = document.createElement('canvas');
            myCanvas.width = window.innerWidth;
            myCanvas.height = window.innerHeight;
            document.body.appendChild(myCanvas);

            const myConfetti = confetti.create(myCanvas, { resize: true });
            let confettiCount = 0; // Counter for the number of confetti bursts
            const maxConfettiCount = 5; // Set the maximum number of bursts

            // Function to trigger confetti
            function triggerConfetti() {
                if (confettiCount < maxConfettiCount) {
                    myConfetti({
                        particleCount: 100,
                        spread: 70,
                        origin: { y: 0.6 }
                    });
                    confettiCount++;
                } else {
                    clearInterval(confettiInterval); // Stop the interval after reaching the limit
                }
            }

            // Start the interval for confetti
            const confettiInterval = setInterval(triggerConfetti, 500); // Repeat every 500ms

            // Clean up when the popup is closed
            closePopupButton.onclick = function() {
                clearInterval(confettiInterval); // Stop the confetti interval
                gsap.to(modalContent, { scale: 0, duration: 0.5, onComplete: () => {
                    winnerPopup.style.display = 'none'; // Hide the modal after animation
                    document.body.removeChild(myCanvas); // Remove canvas after use
                    // window.location="/checkout";
                }});
            };

            // Optional: Claim prize functionality
            document.getElementById('claimPrize').onclick = function() {

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '/marwen',
                    method:'GET',
                    success: function(response) {

                        if (response.drinkExists) {
                            const form = document.getElementById('drinks_form');
                            form.innerHTML = '';
                            fetchDrinks()
                            .then(boissons => {
                                console.log(boissons);
                                // Dynamically create radio buttons

                                boissons.forEach(drink => {
                                    const label = document.createElement('label');
                                    const input = document.createElement('input');

                                    input.type = 'radio';
                                    input.name = 'drink';
                                    input.value = drink; // Use lower case for value
                                    input.style= "margin: 2px";
                                    label.textContent = drink.replace(/\s*\(.*?\)\s*/, ' (gratuit)');

                                    label.prepend(input); // Add radio button to label
                                    form.appendChild(label); // Add label (with radio button) to form
                                    // form.appendChild(document.createElement('br')); // Add a line break
                                    const drinksPopup = document.getElementById('drinksPopup');
                                    const modalDrinksContent = document.querySelector('.modal-drinks-popup-content');
                                    drinksPopup.style.display = 'flex'; // Use flex to center content
                                    gsap.to(modalDrinksContent, { scale: 1, duration: 0.5, ease: "bounce.out" }); // Animate popup scaling

                                });
                            })
                            .catch(error => {
                                console.error('Error fetching drinks:', error);
                            });

                        }else{
                            const form = document.getElementById('drinks_form');
                            form.innerHTML = '';
                            fetchDrinks()
                            .then(boissons => {
                                console.log(boissons);
                                // Dynamically create radio buttons

                                boissons.forEach(drink => {
                                    const label = document.createElement('label');
                                    const input = document.createElement('input');

                                    input.type = 'radio';
                                    input.name = 'drink';
                                    input.value = drink; // Use lower case for value
                                    input.style= "margin: 2px";
                                    label.textContent = drink.replace(/\s*\(.*?\)\s*/, ' (gratuit)');

                                    label.prepend(input); // Add radio button to label
                                    form.appendChild(label); // Add label (with radio button) to form
                                    // form.appendChild(document.createElement('br')); // Add a line break
                                    const drinksPopup = document.getElementById('drinksPopup');
                                    const modalDrinksContent = document.querySelector('.modal-drinks-popup-content');
                                    drinksPopup.style.display = 'flex'; // Use flex to center content
                                    gsap.to(modalDrinksContent, { scale: 1, duration: 0.5, ease: "bounce.out" }); // Animate popup scaling

                                });
                            })
                            .catch(error => {
                                console.error('Error fetching drinks:', error);
                            });
                        }
                    },
                    error: function(xhr) {
                        console.error('Error updating cart:', xhr.responseText);
                    }
                });
                closePopupButton.onclick(); // Close popup after claiming
            };


            window.onclick = function(event) {
                if (event.target === winnerPopup) {
                    closePopupButton.onclick();
                }
            };
        }
        function fetchDrinks() {
            return new Promise((resolve, reject) => {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "/get-drinks-list",
                    method: "GET",
                    success: function(response) {
                        const boisson = response.boissons;
                        boissons=[];
                        boisson.forEach(element => {
                            boissons.push(element.nom_produit + ' (' + parseFloat(element.prix).toString() + 'TND)');
                        });

                        resolve(boissons); // Resolve the promise with the boissons array
                    },
                    error: function(error) {
                        reject(error); // Reject the promise if there's an error
                    }
                });
            });
        }
        document.getElementById('ok').addEventListener('click', function() {
            const selectedDrink = document.querySelector('input[name="drink"]:checked');
            if (selectedDrink) {
                const drinkValue = selectedDrink.value;
                const drinksPopup= document.getElementById('drinksPopup');
                freedrinkscart[0]['options']+=drinkValue.replace(/\s*\(.*?\)\s*/, ' (gratuit)')+",";

                $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: '/update-cart',
                        method: 'POST',
                        data: {
                            cartItem: freedrinkscart[0],           // Send freedrink as 1 or 0

                        },
                        success: function(response) {
                            if (response.success) {
                                drinksPopup.style.display = 'none';
                                $('#your_cart').load(location.href + ' #your_cart');
                                setTimeout(function() {
                                // Code to execute after 2 seconds
                                console.log("Waited for 2 seconds!");
                                    $('#checkoutForm').submit();
                                }, 5000); // 2000 milliseconds = 2 seconds

                            }
                        },
                        error: function(xhr) {
                            console.error('Error updating cart:', xhr.responseText);
                        }
                });
            }

        });
        var deliveryTimeContainer = document.getElementById('ccdelivery-time-container');
        //var deliveryTimeButtonsContainer = document.getElementById('delivery-time-buttons');
        var livraisonContent = document.getElementById('livraisonContent');
        var deliverydate = document.getElementById('date');
        var deliverytime = document.getElementById('delivery_time');
        var address_div = document.getElementById('address-div');
        var delivery_methodButton = document.getElementById('delivery_method_input');
        document.querySelectorAll('.delivery-method-btn').forEach(function(button) {
                button.addEventListener('click', function() {
                    var dataMethod = this.getAttribute('data-method');
                    var deliveryTypeId = this.getAttribute('data-id');
                    delivery_methodButton.value = deliveryTypeId;
                    if (dataMethod === "Livraison") {
                        address_div.style.display="block";

                        livraisonContent.style.display = "block";
                        if ("geolocation" in navigator) {
                            navigator.geolocation.getCurrentPosition(function (position) {
                                latg = position.coords.latitude;
                                lngg = position.coords.longitude
                                getAddress(latg, lngg);
                            });
                        } else {
                            alert("La géolocalisation n'est pas prise en charge dans ce navigateur.");
                        }
                    } else {
                        address_div.style.display="none";
                        livraisonContent.style.display = 'none';
                    }
                    if(dataMethod === "Click & Collect"){
                        deliveryTimeContainer.style.display = 'block';
                        deliverytime.setAttribute('required','required');
                        deliverydate.setAttribute('required','required');
                        deliverydate.valueAsDate = new Date();
                        populateDeliveryTimeOptions(deliverytime,document.getElementById('heure_ouverture').value,document.getElementById('heure_fermeture').value,deliverydate.value);



                    }else{
                        deliveryTimeContainer.style.display = "none";
                        deliverytime.removeAttribute('required');
                        deliverydate.removeAttribute('required');

                    }

                                // Toggle active class for switchable buttons
                    document.querySelectorAll('.delivery-method-btn').forEach(function(btn) {
                        btn.classList.remove('active');
                    });
                this.classList.add('active');
            });
        });
</script>

@include('client.layouts.footer_client')
</body>
</html>






