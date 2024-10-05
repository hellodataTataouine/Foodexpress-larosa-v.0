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
  <meta name="theme-color" content="#6777ef"/>
  <link rel="apple-touch-icon" href="{{ asset('logo_larosa.png') }}"/>
  <link rel="manifest" href="{{ asset('/manifest.json') }}"/>
  <title>La Rosa</title>
  <link rel="icon" type="image/x-icon" href="{{asset('logo_larosa.png')}}">
    <!-- Banner Start -->
    <style>
      .truncate-description {
      max-height: 70px; /* Set a maximum height for the description */
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
  }

  .product {
      height: auto; /* Ensure card height is dynamic */
      /* Add any additional styling for the product card */
  }



      .product-image-container {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 300px;
      width: 300px;/* Set the desired height for the image container */
      }



      /* Adjust the image size if necessary */
      .product-image-container img {
        max-width: 100%;
        max-height: 100%;
      }
      .product-thumb img.customizeBtn {
      cursor: pointer;
  }
      .note-section {
      margin-top: 20px;
    }

    .note-section label {
      font-weight: bold;
    }

    .note-section textarea {
      width: 100%;
      padding: 10px;
      margin-top: 5px;
      border: 1px solid #ccc;
      border-radius: 5px;
      resize: vertical; /* Allow vertical resizing of the textarea */
    }

      .scroll-indicators {
      display: inline-block;
      vertical-align: middle;
  }



  .scroll-indicators-container {
      display: flex;
  }

  .scroll-indicators {
      margin-right: 10px; /* Optional: Adjust the spacing between the two indicators */
  }

  #customizeModal .modal-body {
      position: relative;
  }

  #customizeModal .cancelButton {
      position: absolute;
      top: 10px;
      right: 10px;
  }
  #customizeModal .cancelButton1 {
      position: absolute;
      right: 10px;
  }
  .slick-prev:before {
            content: url('assetsClients/img/arrow-back.svg');
            /* font-size: 30px;
            font-style: oblique; */
          }
  .slick-next:before {
            content: url('assetsClients/img/arrow-next.svg');
            /* background: url('assetsClients/img/arrow-next.svg') 0
              0 / 100% no-repeat;; */
            /*background: */
            /* font-size: 30px;
            font-style: oblique; */
          }
          .popup {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 20px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            text-align: center;
        }
        .overlay {
            display: none;
            position: fixed;
            z-index: 999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }
        .popup h2 {
            margin-bottom: 10px;
        }
        .popup p {
            margin-bottom: 20px;
        }
        .popup button {
            background-color: #4CAF50; /* Green */
            border: none;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 5px;
            cursor: pointer;
            border-radius: 5px;
        }
        .popup button:hover {
            background-color: #45a049; /* Darker green */
        }
        #closePopup {
            background-color: #f44336; /* Red */
        }
        #closePopup:hover {
            background-color: #da190b; /* Darker red */
        }
  </style>
</head>
<body>
    <div class="overlay" id="overlay" style="display: none;"></div>
    <div class="popup" id="installPopup" style="display: none;">
        <h2>Bienvenu chez La Rosa</h2>
        <p>Voulez vous installer cette application ?</p>
        <p>Cliquez sur le bouton ci-dessous pour installer l'application sur votre appareil.</p>
        <button id="installButton">Installer</button>
        <button id="closePopup">Fermer</button>
    </div>
    <script src="{{ asset('/sw.js') }}"></script>
    <script>

        if (!navigator.serviceWorker.controller) {
            navigator.serviceWorker.register("/sw.js").then(function (reg) {
                console.log('Service Worker registered with scope:', reg.scope);
                // const isiOS = navigator.platform === 'iPhone' || navigator.platform === 'iPad' || navigator.platform === 'iPod';
                // const isAndroid = navigator.platform === 'Android';
                // console.log('is android ', isAndroid);
                // if(isAndroid){
                //    console.log("its an android device");
                // }
                // if(isiOS){
                //     console.log("its an ios device");
                // }
                const isStandalone = navigator.standalone || window.matchMedia('(display-mode: standalone)').matches;
                setTimeout(() => {
                        if(testCanShowPrompt() && !isStandalone){
                            showInstallPrompt();
                        }
                    }, 50000);

            });
        }
        let deferredPrompt;

        window.addEventListener('beforeinstallprompt', (e) => {
            // Prevent Chrome 67 and earlier from automatically showing the prompt
            console.log('beforeinstallprompt event fired first one');
            e.preventDefault();
            // Stash the event so it can be triggered later
            deferredPrompt = e;
        });
        function testCanShowPrompt() {
            if (deferredPrompt && typeof deferredPrompt.prompt === 'function') {
                console.log('Can show prompt');
                return true
            } else {

                console.log('Cannot show prompt');
                return false;
            }
        }
        function showInstallPrompt() {
            window.addEventListener('beforeinstallprompt', (e) => {
                // Prevent Chrome 67 and earlier from automatically showing the prompt
                console.log('beforeinstallprompt event fired fel show');
                e.preventDefault();
                // Stash the event so it can be triggered later
                deferredPrompt = e;

            });
            // Show the overlay and the popup
            document.getElementById('overlay').style.display = 'block';
            document.getElementById('installPopup').style.display = 'block';
        }

        document.getElementById('installButton').addEventListener('click', () => {
            // Show the install prompt
            deferredPrompt.prompt();
            // Wait for the user to respond to the prompt
            deferredPrompt.userChoice.then((choiceResult) => {
                if (choiceResult.outcome === 'accepted') {
                    console.log('User accepted the install prompt');
                } else {
                    console.log('User dismissed the install prompt');
                }
                // Reset the deferred prompt variable, as it can only be used once
                deferredPrompt = null;
                // Hide the overlay and popup
                document.getElementById('overlay').style.display = 'none';
                document.getElementById('installPopup').style.display = 'none';

            });
        });

        document.getElementById('closePopup').addEventListener('click', () => {
            // Hide the overlay and popup
            document.getElementById('overlay').style.display = 'none';
            document.getElementById('installPopup').style.display = 'none';

        });
        window.addEventListener('appinstalled', (evt) => {
            console.log('PWA installée avec succès, masquage du popup...');
            document.getElementById('overlay').style.display = 'none';
            document.getElementById('installPopup').style.display = 'none';

        });

    </script>
  @include('client.layouts.top_menu_client')
   <!-- Cart Sidebar Start -->
   @include('client.layouts.cart_client')
   <!-- Aside (Mobile Navigation) -->
   @include('client.layouts.header_menu')


   @include('client.layouts.voirmonpanier')





   <!-- Banner End -->
  <!-- Customize Modal Start -->
  <div class="modal fade" data-bs-backdrop="static" id="customizeModal" tabindex="-1" role="dialog" aria-hidden="true">
       <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
         <div class="modal-content">

           <div class="modal-header modal-bg">
               <button type="button" class="close-btn" data-dismiss="modal" aria-label="Close">
             <span></span>
             <span></span>
           </button>

           </div>
           <div class="modal-body">

             <div class="customize-meta">
               <h4 class="customize-title"><span class="custom-primary">Prix total:</span> </h4>
               <p ></p>

             </div>
             <div class="customize-variations">
               <div class="customize-size-wrapper">
                 <!-- Size variations -->
               </div>
               <div class="row">
               </div>
               <!-- Other customization options -->
             </div>
             <div class="customize-controls">
               <div class="qty">
                 <span class="qty-subtract"><i class="fas fa-minus"></i></span>
               <input type="number"   name="totalquantity"  id="totalquantity" value="1" min="1" readonly>
               <span class="qty-add"><i class="fas fa-plus"></i></span>
               </div>
               <div class="customize-total" >
                 <h5 >Prix : <span class="final-price custom-primary"> <span>TND</span> </span> </h5>
               </div>

             </div>
          <div class="note-section">
           <label for="userNote">Une note avant de confirmer:</label>
           <textarea id="userNote" name="userNote" rows="2" placeholder="Ajouter une note..."></textarea>
         </div>
         <button type="button" class="btn btn-sm cancelButton1" data-dismiss="modal" aria-label="Annuler" title="Annuler">
          {{-- <i class="fas fa-times-circle" style="font-size:18px;color:red"></i> --}}
          <span> Annuler</span></button>
            </div>

         </div>
       </div>
     </div>
   <!-- Customize Modal End -->


 <!-- Menu Categories Start -->
 <div  class="ct-menu-categories menu-filter">

     <div class="container" >

         <div id="menu-section" class="menu-category-slider">
            <!-- Scroll icon at the beginning -->
            <a href="#" data-filter="*" class="ct-menu-category-item menu-first-item initial-category" >
             <div class="menu-category-thumb" >
               <img loading="lazy" src="{{ $client->Category_photo }}" alt="category" >
             </div>
             <div class="menu-category-desc">
               <h6>Menu</h6>

           </div>
           </a>



     @foreach($categories->sortBy('RowN') as $index => $category)
                 @if(isset($firstProducts[$category->id]))
                     @php
                         $firstProduct = $firstProducts[$category->id];
                     @endphp

                       {{-- <a href="#" data-filter=".{{ $category->id }}" class="ct-menu-category-item {{ $category->RowN === 1 ? 'initial-category' : '' }}"> --}}
                        <a href="#" data-filter=".{{ $category->id }}" class="ct-menu-category-item">
                         <div class="menu-category-thumb">
                             <img loading="lazy" src="{{ $category->url_image }}" alt="category" style="height: 86px; width: 86px;">
                         </div>
                         <div class="menu-category-desc">
                             <h6>{{ $category->name }}</h6>

                         </div>
                     </a>
                 @endif
     @endforeach



         </div>
       </div>
     </div>


   <!-- Menu Categories End -->
  <!-- Menu Wrapper Start -->
  <div class="section section-padding">
     <div class="container">

       <div class="menu-container row">
  <!-- Product Start -->

  @foreach ($paginator as $product)

         <!-- Product Start -->

       <div class="col-lg-3 col-md-6  {{ $product->categorie_rest_id}}">
           <div class="product">
             <div>
                 <i class="far fa-heart" style="color: white;"></i>
             </div>

             <a class="product-thumb">
                 <img loading="lazy" class="customizeBtn" data-bs-toggle="modal" data-bs-target="#customizeModal"
                     data-product-id="{{ $product->id }}" data-product-name="{{ $product->nom_produit }}"
                     data-product-image="{{ asset($product->url_image) }}" data-product-price="{{ $product->prix }}"
                     src="{{ asset($product->url_image) }}" alt="menu item" class="center"
                     style="height: 296px; width: 296px;" />
             </a>

             <div class="product-body">
                 <div class="product-desc">
                     <h4 class="truncate-description customizeBtn" data-bs-toggle="modal" data-bs-target="#customizeModal" data-product-id="{{ $product->id }}" data-product-name="{{ $product->nom_produit }}"
                        data-product-image="{{ asset($product->url_image) }}" data-product-price="{{ $product->prix }}" style="cursor: pointer;"> {{ $product->nom_produit }} </h4>
                     <div class="ct-rating-wrapper">
                     </div>
                    @unless($product->description === null || $product->description === "")
                     <p class="truncate-description" >{{ $product->description }}</p>
                     @else
                     <br><br>
                    @endunless
                    @if ($product->prix !=0)
                    <p class="product-price">{{ $product->prix }} TND</p>
                    @endif
                     <a class="btn-custom btn-sm shadow-none customizeBtn" data-bs-toggle="modal" data-bs-target="#customizeModal"
                         data-product-id="{{ $product->id }}" data-product-name="{{ $product->nom_produit }}"
                         data-product-image="{{ asset($product->url_image) }}" data-product-price="{{ $product->prix }}"
                         >Commander <i class="fas fa-shopping-cart"></i></a>
                 </div>
             </div>
         </div>
         </div>

         @endforeach
         <!-- Product End -->

       </div>
     </div>
   </div>
   <!-- Menu Wrapper End -->
   <!-- Newsletter start -->
   <style>
    .whatsapp-button {
        position: fixed;
        bottom: 50px;
        left: 20px;
        z-index: 1000;
    }
</style>
<a href="https://wa.me/+21699916287" class="whatsapp-button" target="_blank">
    <img loading="lazy" src="{{asset('assetsClients/img/WhatsApp_icon.png')}}" alt="Chat with us on WhatsApp" width="60px">
</a>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
 <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


 <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
 <script>
  $(document).ready(function() {
    // Code to execute when the document is ready
    $('.customizeBtn').click(function() {

        // alert('btn customize clicked');
        $('.customize-variation-wrapper input[type="checkbox"]:checked, .customize-variation-wrapper input[type="radio"]:checked').each(function() {
          var optionId = $(this).attr('id');
          var optionName = $(this).siblings('label').text();
          var optionPrice = parseFloat($(this).closest('.customize-variation-item').data('price'));
          var optionType = $(this).attr('type');
          var optionQuantity = 1;

          selectedOptions.push({
              id: optionId,
              name: optionName,
              price: optionPrice,
              type: optionType,
              Quantity: optionQuantity,
          });
      });

      $('.customize-variation-wrapper input[name="quantity"]').each(function() {
          var quantityInput = $(this);
          var optionItem = quantityInput.closest('.customize-variation-item');
          var optionPrice = parseFloat(optionItem.data('price'));

          var quantity = parseInt(quantityInput.val());

          if (quantity >= 1) {
              var optionId = $(this).attr('id');
              var optionName = $(this).siblings('label').text();
              var optionPrice = parseFloat(optionItem.data('price'));
              var optionType = $(this).attr('type');
              var optionQuantity = quantity;

              // Push the selected option's details into the selectedOptions array
              selectedOptions.push({
                  id: optionId,
                  name: optionName,
                  price: optionPrice,
                  type: optionType,
                  Quantity: optionQuantity,
              });
          }
      });
    // $.ajax({
    //     url: "/fetch-cart",
    //     type: 'GET',
    //     dataType: "json",
    //     success: function(data) {
    //         var cartItemCount = data.cartItemCount;
    //         $('#cartItemCount').text(cartItemCount + " element(s)");
    //         }
    // });

    });

});

</script>
   <script>
     toastr.options = {
   closeButton: true,                // Show a close button
   timeOut: 2000,                    // Close the notification after 3 seconds
   positionClass: 'toast-top-right',
   backgroundColor: '#FCCC4C',
     //progressBar: true,// Set the notification background color (with quotes)
 };


 </script>

   <script>
     var selectedOptionsproduits = [];
      var selectedOptions = {};
     var response; // Global variable to store the response object

     $(document).ready(function() {
       $('.customizeBtn').click(function() {
         var productId = $(this).data('product-id');
         var productOptions = $(this).data('data-product-options');
         $.ajax({
           url: '/panier/getProductRestaurantDetails/' + productId,
           method: 'GET',
           success: function(res) {
             response = res; // Store the response in the global variable

             $('.customize-title').empty();
             $('.customize-meta p').empty();
             $('.modal-header').empty();
             $('.customize-variations').empty();
             $('.custom-primary').empty();
             $('.final-price.custom-primary').empty();
           $('.note-section textarea').val('');
             // Update the modal content with the returned data

            if(response.product.prix!=0){
                $('.customize-title').html(response.product.nom_produit + ' <span class="custom-primary">'  + response.product.prix + ' TND</span>');
            }else{

                $('.customize-title').html(response.product.nom_produit);
            }


         var addToCartBtn = $('<button type="submit"  class="order-item btn-custom btn-sm shadow-none customizeBtn" data-product-id="' + productId + '" data-product-name="' + response.product.nom_produit + '" data-product-image="' + response.product.url_image + '" data-product-price="' + response.product.prix + '">Ajouter au Panier <i class="fas fa-shopping-cart"></i></button>');
 addToCartBtn.appendTo('.modal-body');
         addToCartBtn.appendTo('.customize-meta');
                 var $pElement = $('.customize-meta p');

 // Add text to the <p> element

 $pElement.text(response.product.description);
         $('.customize-title').append(addToCartBtn);
             // Update the background image
            // $('.modal-header').css('background-image', 'url("' + response.product.url_image + '")');
   $('.modal-bg').css({
     'background-image': 'url("' + response.product.url_image + '")',
     'height': '406px',

 });
       var cancelButton = $('<button type="button" class="btn btn-sm cancelButton" data-dismiss="modal"></button>');
 var cancelText = $('<span> Annuler</span>');
 var cancelIcon = $('<i class="fas fa-times-circle" style="font-size:18px;color:red"></i>');

 cancelButton.append(cancelIcon, cancelText);

 // Optionally, you can add a label or tooltip for accessibility
 cancelButton.attr('aria-label', 'Annuler');
 cancelButton.attr('title', 'Annuler');

 // Append the button to the modal header or any other desired location
 $('.modal-header').append(cancelButton);
         cancelButton.on('click', function() {

 // Close the modal when the button is clicked
 $('#customizeModal').modal('hide');
 });
 $('.cancelButton1').on('click', function(){
  $('#customizeModal').modal('hide');
 });
            $('.customize-variations').empty();
             for (var i = 0; i < response.familleOptions.length; i++) {
               var familleOption = response.familleOptions[i];
               var options = familleOption.options;

               if (i % 3 === 0) {
                 // Create a new row
                 var row = $('<div class="row"></div>');
                 row.appendTo('.customize-variations');
               }

               var variationElement = $('<div class="col-lg-4 col-12"></div>');
               var variationWrapper = $('<div class="customize-variation-wrapper"></div>');
               var variationTitle = $('<h5>' + familleOption.famille_option.nom_famille_option + '</h5>');

               variationTitle.appendTo(variationWrapper);
         if (familleOption.famille_option.nbre_choix != null && familleOption.famille_option.nbre_choix != 0 && familleOption.famille_option.type != "simple") {
      var variationsubTitle = $('<p>' + "Choisissez-en " + familleOption.famille_option.nbre_choix +  " max." + '<p>');

          variationsubTitle.appendTo(variationWrapper);

         }else if((familleOption.famille_option.nbre_choix == null || familleOption.famille_option.nbre_choix == 0) && (familleOption.famille_option.type == "multiple")){
            var variationsubTitle = $('<p>' + "Choisissez-en " + familleOption.options.length +  " max." + '<p>');

          variationsubTitle.appendTo(variationWrapper);
         }
         if(familleOption.famille_option.type == "simple" ){
            var variationsubTitle = $('<p>' + "Choisissez-en 1 " + '<p>');
          variationsubTitle.appendTo(variationWrapper);

         }
               variationWrapper.appendTo(variationElement);



               var aucunOption = null;

               for (var j = 0; j < options.length; j++) {
                 let option = options[j]; // Use let instead of var to correctly scope the variable

                 var variationItem = $('<div class="customize-variation-item" data-price="' + option.prix + '"></div>');
                 var variationIcon = $('<i class="flaticon-food-tray"></i>');


             var customControl = $('<div class="custom-control custom-' + (familleOption.famille_option.type === 'simple' ? 'radio' : 'checkbox') + ' ' + generateUniqueClassName() + '"></div>');

                 function generateUniqueClassName() {

   var uniqueId = new Date().getTime(); // Using a timestamp as a unique identifier
   return 'unique-' + uniqueId;
 }
                 var inputType = familleOption.famille_option.type === 'simple' ? 'radio' : 'checkbox';

    // Store the type of the option (checkbox or qte) in the selectedOptions object
    selectedOptions[option.id] = {
     name: option.nom_option,
     price: option.prix,
     type: familleOption.famille_option.type,
   };
                 if (familleOption.famille_option.type === 'qte') {
   inputType = 'number';
   var input = $('<input type="' + inputType + '" id="' + option.id + '" name="quantity" min=0 class="custom-control-input">');
           input.css('width', '50px');
           // Set the height to 30 pixels
   // For "qte" type options, attach an event handler to update the quantity
   input.on('input', function() {
       var quantity = parseInt($(this).val(), 10) || 1; // Ensure a valid integer (default to 1 if invalid)
       var input = $('<input type="' + inputType + '" id="' + option.id + '" name="quantity" class="custom-control-input">');

       input.attr('data-option-id', option.id); // Set a data-* attribute to store option.id on the input element

   // For "qte" type options, attach an event handler to update the quantity
   input.on('input', function() {
     var optionId = $(this).data('option-id'); // Retrieve option.id from the data-* attribute
     var quantity = parseInt($(this).val(), 10) || 1; // Ensure a valid integer (default to 1 if invalid)

     // Update the quantity in the selectedOptions object using optionId
     selectedOptions[optionId].Quantity = quantity;
   });
     });
 } else if (inputType === 'radio') {

     if (j === 0) {
             input = $('<input type="' + inputType + '" id="' + option.id + '" name="' + familleOption.id + '" class="custom-control-input" checked>');

     // input.on('change', handledefaultchecked(familleOption));

         } else {
             input = $('<input type="' + inputType + '" id="' + option.id + '" name="' + familleOption.id + '" class="custom-control-input">');
         }
 }else if (inputType === 'checkbox') {
      var input = $('<input type="' + inputType + '" id="' + option.id + '" name="' + familleOption.id + '" class="custom-control-input">');

     // Add an event listener to the checkbox
      input.on('change', createCheckboxChangeHandler(familleOption));
 }
   var label = $('<label class="custom-control-label" for="' + option.id + '"> ' + option.nom_option + ' </label>');
   if(option.prix != null){
                 var price = $('<span> +' + option.prix + 'TND </span>');
   }else {
     var price = $('');
   }
                 input.appendTo(customControl).css('margin-right', '7px');
                 label.appendTo(customControl);
                 customControl.appendTo(variationItem);
                 price.appendTo(variationItem);
                 variationIcon.appendTo(variationWrapper);
                 variationItem.appendTo(variationWrapper);
 if (option.nom_option === 'Aucun') {
         aucunOption = customControl; // Save the 'Aucun' option for later
     }
               }
 if (aucunOption) {
     aucunOption.appendTo(variationWrapper);
 }

               variationElement.appendTo(row);
             }
             var customizeControls = $('.customize-controls');

            var quantityLabel = $('.qty');
 //var quantityLabel = $('<div class="qty"></div>');
 var subtractButton = $('.qty-subtract');
 //var quantityInput = $('<input type="number" name="totalquantity"  id="totalquantity" value="1">');
 var addButton  = $('.qty-add');





 var priceLabel = $('.customize-total');
     $('.final-price.custom-primary').text( response.product.prix + 'TND' );


 addButton.off('click');
 subtractButton.off('click');

 //action
 addButton.on('click', function() {
   var totalquantityInput = $(this).siblings('input[name="totalquantity"]');
   var currentQuantity = parseInt(totalquantityInput.val());
   if (!isNaN(currentQuantity)) {
     totalquantityInput.val(currentQuantity + 1);
     updateTotalPrice();
   }
 });
 subtractButton.on('click', function() {
   var totalquantityInput = $(this).siblings('input[name="totalquantity"]');
   var currentQuantity = parseInt(totalquantityInput.val());
   if (!isNaN(currentQuantity) && (currentQuantity > 1)) {
     totalquantityInput.val(currentQuantity - 1);
     updateTotalPrice();
   }
 });

 $('.modal-body .order-item.btn-custom.btn-sm.shadow-none.customizeBtn').remove();


 var addToCartBtn = $('<button type="submit"  class="order-item btn-custom btn-sm shadow-none customizeBtn"  data-product-id="' + productId + '" data-product-name="' + response.product.nom_produit + '" data-product-image="' + response.product.url_image + '" data-product-price="' + response.product.prix + '">Ajouter au Panier <i class="fas fa-shopping-cart"></i></button>');
 addToCartBtn.appendTo('.modal-body');
         // Create the "Cancel" button
         $('.modal-body .btn.btn-sm.shadow-none.cancelButton').remove();
 //var cancelButton = $('<button type="button" class="btn btn-sm shadow-none cancelButton" data-dismiss="modal">Annuler</button>');

 // Append both buttons to the modal body
 $('.modal-body').append(addToCartBtn);
 cancelButton.on('click', function() {

 // Close the modal when the button is clicked
 $('#customizeModal').modal('hide');
 });
         // Calculate and update the total price
             updateTotalPrice();

             // Show the modal
             $('#customizeModal').on('shown.bs.modal', function() {
          $('.customize-variation-wrapper input[type="radio"]:checked').each(function() {
     // Trigger the 'change' event for each initially checked radio button
     $(this).trigger('change');
   });
     //initializeTotalQuantity();



        //  $('.final-price.custom-primary').text(response.product.prix);
   });
          var totalquantityInput = $('#totalquantity');
     totalquantityInput.val('1');

             $('#customizeModal').modal('show');
           },
           error: function(err) {
             console.log(err);
           }
         });

       });

      // selectedOptions = [];


          $(document).on('change', '.customize-variation-wrapper input[type="checkbox"], .customize-variation-wrapper input[type="radio"], .customize-variation-wrapper input[type="number"], .customize-variations #totalquantity', function() {
   // Clear the selectedOptions array to start fresh
  // selectedOptions = [];
 // Loop through the selected options to store their details
 $('.customize-variation-wrapper input[type="checkbox"]:checked, .customize-variation-wrapper input[type="radio"]:checked').each(function() {
         var optionId = $(this).attr('id');
         var optionName = $(this).siblings('label').text();
         var optionPrice = parseFloat($(this).closest('.customize-variation-item').data('price'));
         var optionType = $(this).attr('type');
         var optionQuantity = 1;

         selectedOptions.push({
             id: optionId,
             name: optionName,
             price: optionPrice,
             type: optionType,
       Quantity: optionQuantity,
         });

     });

     $('.customize-variation-wrapper input[name="quantity"]').each(function() {


       var quantityInput = $(this);
   var optionItem = quantityInput.closest('.customize-variation-item');
   var optionPrice = parseFloat(optionItem.data('price'));

   var quantity = parseInt(quantityInput.val());

   if(quantity >= 1){
       var optionId = $(this).attr('id');
         var optionName = $(this).siblings('label').text();
         var optionPrice = parseFloat(optionItem.data('price'));
         var optionType = $(this).attr('type');
         var optionQuantity = quantity;

   // Push the selected option's details into the selectedOptions array

   selectedOptions.push({
             id: optionId,
             name: optionName,
             price: optionPrice,
             type: optionType,
             Quantity: optionQuantity,
         });
   }
     });


  // Handle quantity input if present
  var totalQuantity = parseInt($('#totalquantity').val());
     if (isNaN(totalQuantity) || totalQuantity < 1) {
         totalQuantity = 1;
     }
           // Calculate and update the total price
   updateTotalPrice();

       });


 function createCheckboxChangeHandler(familleOption) {
    return function() {
         // Get all checkboxes in this specific familleOption group
         var checkboxes = $('input[name="' + familleOption.id + '"]');

         // Get the number of checkboxes selected in this group
         var selectedCount = checkboxes.filter(':checked').length;
         if (familleOption.famille_option.nbre_choix != null && familleOption.famille_option.nbre_choix != 0)
         // Check if the selectedCount exceeds the maximum allowed for this group
     { if (selectedCount >= familleOption.famille_option.nbre_choix) {
             // Uncheck and disable checkboxes if the limit is exceeded
             checkboxes.filter(':not(:checked)').prop('disabled', true);
         } else {
             // Enable all checkboxes within the group if the limit is not exceeded
             checkboxes.prop('disabled', false);
         }}
     };
 }

       // Function to calculate and update the total price
       function updateTotalPrice() {
         var totalPrice = parseFloat(response.product.prix);
         console.log('Total Price:', totalPrice);

         // Loop through the selected options
         $('.customize-variation-wrapper input[type="checkbox"]:checked, .customize-variation-wrapper input[type="radio"]:checked').each(function() {

           var optionPrice = $(this).closest('.customize-variation-item').data('price');
               console.log('optionPrice:', optionPrice);

               if (optionPrice != null) {

     optionPrice = parseFloat(optionPrice);

 }else{
   optionPrice = 0;
 }
           totalPrice += optionPrice;
         });

      // Handle quantity for options
 var optionQuantity = 0;
 var optionQuantityPrice = 0;

 $('.customize-variation-wrapper input[name="quantity"]').each(function() {
   var quantityInput = $(this);
   var optionItem = quantityInput.closest('.customize-variation-item');
   var optionPrice = parseFloat(optionItem.data('price'));

   var quantity = parseInt(quantityInput.val());
   if (isNaN(quantity) || quantity < 1) {
     quantity = 0;
   }


   //optionQuantity += quantity;
   if (!isNaN(optionPrice))
          {
   optionQuantityPrice += optionPrice * quantity;
   }

   console.log('Option Quantity:', quantity);
 });
 totalPrice += optionQuantityPrice;


 console.log('Option Quantity Price:', optionQuantityPrice);

   // Handle quantity for total price
   var totalPriceQuantity = 1;
   var totalPriceQuantityInput = $('#totalquantity');
   if (totalPriceQuantityInput.length > 0) {
     totalPriceQuantity = parseInt(totalPriceQuantityInput.val());
     if (isNaN(totalPriceQuantity) || totalPriceQuantity < 1) {
       totalPriceQuantity = 1;
     }
   }




   // Update the total price based on the quantities
   totalPrice *= totalPriceQuantity;

   //var addToCartBtn = $('.order-item');
    // addToCartBtn.attr('data-product-price', totalPrice);
         // Update the total price display
         $('.total-price').html(totalPrice + 'TND');

         var priceTotal = $('.final-price.custom-primary');
         priceTotal.text(totalPrice.toFixed(3) + 'TND');

       }

       $('.close-btn').on('click', function() {
     $('#customizeModal').modal('hide');

   });
    $('#customizeModal').on('hidden.bs.modal', function () {
     // Set the text of the element with class 'final-price.custom-primary' to an empty string
     $('.final-price.custom-primary').text("");
       selectedOptions = [];
   });

 });





       $(document).ready(function() {
       $(document).on("click", ".customizeBtnedit", function () {

         var productIdAdd = $(this).data('product-id');
       var  productIdEdit = $(this).data('product-id-edit');
       var productquantity = $(this).data('product-qauntity');
       var productprix = $(this).data('product-price');
       var productitem = $(this).data('product-item');

          var encodedString = $(this).data('product-options');
       console.log(encodedString);
 if (encodedString !== null && encodedString !== undefined && (typeof encodedString === 'string' || Array.isArray(encodedString))) {
     // Parse the JSON string directly
    if (typeof encodedString === 'string') {
         var productOptionsedit = JSON.parse(encodedString);
         var productOptions = productOptionsedit.replace(/\([^)]*\)/g, '');
         console.log(productOptions);
       }
 }
        // Initialize selectedOptions as an empty array
     //var selectedOptions = [];
       if (productIdEdit !== undefined) {
       productId = productIdEdit;
     } else {
       productId = productIdAdd;
     }
         $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
           url: '/panier/getProductRestaurantDetails/' + productId,
           method: 'GET',
           success: function(res) {
             response = res; // Store the response in the global variable
           //  var selectedOptions = {};
             $('.customize-title').empty();
             $('.customize-meta p').empty();
             $('.modal-header').empty();
             $('.customize-variations').empty();
             $('.custom-primary').empty();
             $('.final-price.custom-primary').empty();
          $('.note-section textarea').val('');
             // Update the modal content with the returned data

             $('.customize-title').html(response.product.nom_produit + ' <span class="custom-primary">'  + response.product.prix + 'TND</span>');
                 var $pElement = $('.customize-meta p');

 // Add text to the <p> element

 $pElement.text(response.product.description);
             // Update the background image
            // $('.modal-header').css('background-image', 'url("' + response.product.url_image + '")');

  $('.modal-bg').css({
     'background-image': 'url("' + response.product.url_image + '")',
     'height': '406px',

 });
       var cancelButton = $('<button type="button" class="btn btn-sm cancelButton" data-dismiss="modal"></button>');
 var cancelText = $('<span> Annuler</span>');
 var cancelIcon = $('<i class="fas fa-times-circle"></i>');

 cancelButton.append(cancelIcon, cancelText);

 // Optionally, you can add a label or tooltip for accessibility
 cancelButton.attr('aria-label', 'Annuler');
 cancelButton.attr('title', 'Annuler');

 // Append the button to the modal header or any other desired location
 $('.modal-header').append(cancelButton);
         cancelButton.on('click', function() {

 // Close the modal when the button is clicked
 $('#customizeModal').modal('hide');
 });


            $('.customize-variations').empty();
             for (var i = 0; i < response.familleOptions.length; i++) {
               var familleOption = response.familleOptions[i];
               var options = familleOption.options;

               if (i % 3 === 0) {
                 // Create a new row
                 var row = $('<div class="row"></div>');
                 row.appendTo('.customize-variations');
               }

               var variationElement = $('<div class="col-lg-4 col-12"></div>');
               var variationWrapper = $('<div class="customize-variation-wrapper"></div>');
               var variationTitle = $('<h5>' + familleOption.famille_option.nom_famille_option + '</h5>');

               variationTitle.appendTo(variationWrapper);
         if (familleOption.famille_option.nbre_choix != null && familleOption.famille_option.nbre_choix != 0) {
      var variationsubTitle = $('<p>' + "Choisissez-en " + familleOption.famille_option.nbre_choix +  " max." + '<p>');

          variationsubTitle.appendTo(variationWrapper);

         }else if((familleOption.famille_option.nbre_choix == null || familleOption.famille_option.nbre_choix == 0) && (familleOption.famille_option.type == "multiple")){
            var variationsubTitle = $('<p>' + "Choisissez-en " + familleOption.options.length +  " max." + '<p>');

          variationsubTitle.appendTo(variationWrapper);
         }
         if(familleOption.famille_option.type == "simple" ){
            var variationsubTitle = $('<p>' + "Choisissez-en 1 " + '<p>');
          variationsubTitle.appendTo(variationWrapper);

         }
               variationWrapper.appendTo(variationElement);

 var selectedOptionsnonSupplementaires = [];
 var selectedOptionsSupplementaires = [];

 // Check if productOptions is defined and not empty
 if (productOptions !== undefined && productOptions.trim() !== '') {
   // Split the 'productOptions' into an array
   var optionsArray = productOptions.split('/');

   // Separate the options into two variables
   var nonSupplementairesArray = optionsArray[0].trim();
   console.log('nonSupplementairesArray', nonSupplementairesArray);
   selectedOptionsnonSupplementaires = nonSupplementairesArray.split(',').map(function(option) {
     return option.trim(); // Remove leading/trailing spaces
   });
  if (optionsArray.length > 1) {
   var supplementairesArray = optionsArray[1].trim();
    console.log('supplementairesArray', supplementairesArray);
     selectedOptionsSupplementaires = supplementairesArray.split(',').map(function(option) {
     return option.trim(); // Remove leading/trailing spaces
   });
 //disableBTN(0);

 }
    }




         for (var j = 0; j < options.length; j++) {

                 let option = options[j]; // Use let instead of var to correctly scope the variable

                 var variationItem = $('<div class="customize-variation-item" data-price="' + option.prix + '"></div>');
                 var variationIcon = $('<i class="flaticon-bread-roll"></i>');


             var customControl = $('<div class="custom-control custom-' + (familleOption.famille_option.type === 'simple' ? 'radio' : 'checkbox') + ' ' + generateUniqueClassName() + '"></div>');

                 function generateUniqueClassName() {

   var uniqueId = new Date().getTime(); // Using a timestamp as a unique identifier
   return 'unique-' + uniqueId;
 }
                 var inputType = familleOption.famille_option.type === 'simple' ? 'radio' : 'checkbox';


           // Check if an option should be pre-selected

   var shouldBeCheckedSupp = selectedOptionsSupplementaires.includes(option.nom_option);
           console.log(shouldBeCheckedSupp);
 var shouldBeCheckedNSupp = selectedOptionsnonSupplementaires.includes(option.nom_option);


                if (familleOption.famille_option.type === 'qte') {
   inputType = 'number';
   input = $('<input type="' + inputType + '" id="' + option.id + '" name="quantity" min="0" class="custom-control-input">');
   input.css('width', '50px');
   // For "qte" type options, attach an event handler to update the quantity
   input.on('input', function() {
     var optionId = $(this).attr('id');
     var quantity = parseInt($(this).val(), 10) || 1; // Ensure a valid integer (default to 1 if invalid)

     // Update the quantity in the selectedOptions object using optionId
     if (selectedOptions.hasOwnProperty(optionId)) {
       selectedOptions[optionId].Quantity = quantity;
     }
   });
 } else if (inputType === 'radio') {
   if (productIdEdit !== undefined) {
     input = $('<input type="radio" id="' + option.id + '" name="' + familleOption.id + '" class="custom-control-input">');
      // Check the first radio option by default if no option is selected
         if (j === 0 && !shouldBeCheckedSupp && !shouldBeCheckedNSupp) {
             input.prop('checked', true);
         } else {

     if (!isNaN(option.prix) && option.prix !== null) {
       if (shouldBeCheckedSupp) {
         input.prop('checked', true);
       }
     } else if (shouldBeCheckedNSupp) {
         input.prop('checked', true);
       }
      else if (j === 0) {
         input.prop('checked', true);
       }


   }}

 } else if (inputType === 'checkbox') {
   if (productIdEdit !== undefined) {
     input = $('<input type="checkbox" id="' + option.id + '" name="' + familleOption.id + '" class="custom-control-input">');
     if (!isNaN(option.prix) && option.prix !== null) {
       if (shouldBeCheckedSupp) {
         input.prop('checked', true);
       }
     } else {
       if (shouldBeCheckedNSupp) {
         input.prop('checked', true);
       }
     }

     // Add an event listener to the checkbox
     input.on('change', function() {
       createCheckboxChangeHandler(familleOption);
     });
   }
 }





   var label = $('<label class="custom-control-label" for="' + option.id + '"> ' + option.nom_option + ' </label>');
   if(option.prix != null){
                 var price = $('<span> +' + option.prix + 'TND </span>');
   }else {
     var price = $('');
   }
 input.appendTo(customControl).css('margin-right', '7px');
                 label.appendTo(customControl);
                 customControl.appendTo(variationItem);
                 price.appendTo(variationItem);
                 variationIcon.appendTo(variationWrapper);
                 variationItem.appendTo(variationWrapper);


               }

               variationElement.appendTo(row);
             }
         var totalquantityInput = $('#totalquantity');
     totalquantityInput.val(productquantity);
             var customizeControls = $('.customize-controls');

            var quantityLabel = $('.qty');
 //var quantityLabel = $('<div class="qty"></div>');
 var subtractButton = $('.qty-subtract');
 //var quantityInput = $('<input type="number" name="totalquantity"  id="totalquantity" value="1">');
 var addButton  = $('.qty-add');





 var priceLabel = $('.customize-total');
     $('.final-price.custom-primary').text( productprix + 'TND' );


 addButton.off('click');
 subtractButton.off('click');

 //action
 addButton.on('click', function() {
   var totalquantityInput = $(this).siblings('input[name="totalquantity"]');
   var currentQuantity = parseInt(totalquantityInput.val());
   if (!isNaN(currentQuantity)) {
     totalquantityInput.val(currentQuantity + 1);
     updateTotalPrice();
   }
 });
 subtractButton.on('click', function() {
   var totalquantityInput = $(this).siblings('input[name="totalquantity"]');
   var currentQuantity = parseInt(totalquantityInput.val());
   if (!isNaN(currentQuantity) && (currentQuantity > 1)) {
     totalquantityInput.val(currentQuantity - 1);
     updateTotalPrice();
   }
 });

 $('.modal-body .order-item.btn-custom.btn-sm.shadow-none.customizeBtn').remove();

 if (productIdAdd !== undefined) {
 var addToCartBtn = $('<button type="submit"  class="order-item btn-custom btn-sm shadow-none customizeBtn" data-product-id="' + productId + '" data-product-name="' + response.product.nom_produit + '" data-product-image="' + response.product.url_image + '" data-product-price="' + response.product.prix + '">Ajouter au Parier <i class="fas fa-shopping-cart"></i></button>');
 }else {

 var addToCartBtn = $('<button type="submit" id="updatebtncart"  class="order-item btn-custom btn-sm shadow-none customizeBtn" data-product-id-edit="' + productId + '" data-product-name="' + response.product.nom_produit + '" data-product-image="' + response.product.url_image + '" data-product-item="' + productitem + '" data-product-price="' + response.product.prix + '">Mettre à jour <i class="fas fa-shopping-cart"></i></button>');
 }
 addToCartBtn.appendTo('.modal-body');

         // Create the "Cancel" button
         $('.modal-body .btn.btn-sm.shadow-none.cancelButton').remove();
         var cancelButton = $('<button type="button" class="btn btn-sm cancelButton" data-dismiss="modal"></button>');
 var cancelText = $('<span> Annuler</span>');
 var cancelIcon = $('<i class="fa fa-times-circle" style="font-size:18px;color:red"></i>');

 cancelButton.append(cancelIcon, cancelText);

 // Optionally, you can add a label or tooltip for accessibility
 cancelButton.attr('aria-label', 'Annuler');
 cancelButton.attr('title', 'Annuler');

 // Append the button to the modal header or any other desired location
 $('.modal-header').append(cancelButton);
         cancelButton.on('click', function() {

 // Close the modal when the button is clicked
 $('#customizeModal').modal('hide');
 });
         // Calculate and update the total price
             updateTotalPrice();

             // Show the modal
             $('#customizeModal').on('shown.bs.modal', function() {
          $('.customize-variation-wrapper input[type="radio"]:checked').each(function() {
     // Trigger the 'change' event for each initially checked radio button
     $(this).trigger('change');
   });

    $('.final-price.custom-primary').text(productprix);
   });

             $('#customizeModal').modal('show');
           },
           error: function(err) {
             console.log(err);
           }
         });

       });

      selectedOptions = [];

   selectedOptionsproduits = [];
          $(document).on('change', '.customize-variation-wrapper input[type="checkbox"], .customize-variation-wrapper input[type="radio"], .customize-variation-wrapper input[type="number"], .customize-variations #totalquantity', function() {
   // Clear the selectedOptions array to start fresh
   selectedOptionsproduits = [];
 // Loop through the selected options to store their details
 $('.customize-variation-wrapper input[type="checkbox"]:checked, .customize-variation-wrapper input[type="radio"]:checked').each(function() {
         var optionId = $(this).attr('id');
         var optionName = $(this).siblings('label').text();
         var optionPrice = parseFloat($(this).closest('.customize-variation-item').data('price'));
         var optionType = $(this).attr('type');
         var optionQuantity = 1;

         selectedOptionsproduits.push({
             id: optionId,
             name: optionName,
             price: optionPrice,
             type: optionType,
       Quantity: optionQuantity,
         });

     });

     $('.customize-variation-wrapper input[name="quantity"]').each(function() {


       var quantityInput = $(this);
   var optionItem = quantityInput.closest('.customize-variation-item');
   var optionPrice = parseFloat(optionItem.data('price'));

   var quantity = parseInt(quantityInput.val());

   if(quantity >= 1){
       var optionId = $(this).attr('id');
         var optionName = $(this).siblings('label').text();
         var optionPrice = parseFloat(optionItem.data('price'));
         var optionType = $(this).attr('type');
         var optionQuantity = quantity;

   // Push the selected option's details into the selectedOptions array

   selectedOptionsproduits.push({
             id: optionId,
             name: optionName,
             price: optionPrice,
             type: optionType,
             Quantity: optionQuantity,
         });
   }
     });


  // Handle quantity input if present
  var totalQuantity = parseInt($('#totalquantity').val());
     if (isNaN(totalQuantity) || totalQuantity < 1) {
         totalQuantity = 1;
     }
           // Calculate and update the total price
   updateTotalPrice();

       });


 function createCheckboxChangeHandler(familleOption) {
    return function() {
         // Get all checkboxes in this specific familleOption group
         var checkboxes = $('input[name="' + familleOption.id + '"]');

         // Get the number of checkboxes selected in this group
         var selectedCount = checkboxes.filter(':checked').length;
         if (familleOption.famille_option.nbre_choix != null && familleOption.famille_option.nbre_choix != 0)
         // Check if the selectedCount exceeds the maximum allowed for this group
     { if (selectedCount >= familleOption.famille_option.nbre_choix) {
             // Uncheck and disable checkboxes if the limit is exceeded
             checkboxes.filter(':not(:checked)').prop('disabled', true);
         } else {
             // Enable all checkboxes within the group if the limit is not exceeded
             checkboxes.prop('disabled', false);
         }}
     };
 }

       // Function to calculate and update the total price
       function updateTotalPrice() {
         var totalPrice = parseFloat(response.product.prix);
         console.log('Total Price:', totalPrice);

         // Loop through the selected options
         $('.customize-variation-wrapper input[type="checkbox"]:checked, .customize-variation-wrapper input[type="radio"]:checked').each(function() {

           var optionPrice = $(this).closest('.customize-variation-item').data('price');
               console.log('optionPrice:', optionPrice);

               if (optionPrice != null) {

     optionPrice = parseFloat(optionPrice);

 }else{
   optionPrice = 0;
 }
           totalPrice += optionPrice;
         });

      // Handle quantity for options
 var optionQuantity = 0;
 var optionQuantityPrice = 0;

 $('.customize-variation-wrapper input[name="quantity"]').each(function() {
   var quantityInput = $(this);
   var optionItem = quantityInput.closest('.customize-variation-item');
   var optionPrice = parseFloat(optionItem.data('price'));

   var quantity = parseInt(quantityInput.val());
   if (isNaN(quantity) || quantity < 1) {
     quantity = 0;
   }


   //optionQuantity += quantity;
   if (!isNaN(optionPrice))
          {
   optionQuantityPrice += optionPrice * quantity;
   }

   console.log('Option Quantity:', quantity);
 });
 totalPrice += optionQuantityPrice;


 console.log('Option Quantity Price:', optionQuantityPrice);

   // Handle quantity for total price
   var totalPriceQuantity ;
   var totalPriceQuantityInput = $('#totalquantity');
   if (totalPriceQuantityInput.length > 0) {
     totalPriceQuantity = parseInt(totalPriceQuantityInput.val());
     if (isNaN(totalPriceQuantity) || totalPriceQuantity < 1) {
       totalPriceQuantity = 1;
     }
   }




   // Update the total price based on the quantities
   totalPrice *= totalPriceQuantity;

   //var addToCartBtn = $('.order-item');
    // addToCartBtn.attr('data-product-price', totalPrice);
         // Update the total price display
         $('.total-price').html(totalPrice + 'TND');

         var priceTotal = $('.final-price.custom-primary');
         priceTotal.text(totalPrice.toFixed(3) + 'TND');
        //  disableBTN(parseFloat((priceTotal.text()).replace('TND','')));


       }

       $('.close-btn').on('click', function() {
     $('#customizeModal').modal('hide');

   });
    $('#customizeModal').on('hidden.bs.modal', function () {
     // Set the text of the element with class 'final-price.custom-primary' to an empty string
     $('.final-price.custom-primary').text("");
   });
 /*  function initializeTotalQuantity() {
     var totalquantityInput = $('#totalquantity');
     totalquantityInput.val(productquantity);
   }*/

  });














 $(document).ready(function() {
//    disableBTN(0);
  let nextId = 1;
 //  $('.btn-custom.btnconfirm').click(function(event) {
 //         event.preventDefault(); // Prevent the default action of the link
 //     });

   // Existing code...
   $(document).on('click', '.order-item.btn-custom.btn-sm.shadow-none.customizeBtn', function() {

      var productIdAdd = $(this).data('product-id');
     var productIdEdit = $(this).data('product-id-edit');

     var productIditem = $(this).data('product-item');
   if (productIdEdit !== undefined) {
      var productId = productIdEdit;
     } else {
      var productId = productIdAdd;
     }
   // Retrieve the selected customization options, product ID, name, and price
   var priceTotal = $('.final-price.custom-primary');
  // var productId = response.product.id;
   var productName = response.product.nom_produit;
   var productImage = response.product.url_image;
   var productPrice =  parseFloat(priceTotal.text().replace('TND', ''));
   var productUnityPrice = response.product.prix;
   var productQuantity = $('#totalquantity').val();
   var customizationOptions = getSelectedOptions(); // Implement the logic to retrieve the selected options
//    disableBTN(productPrice);
   // Construct the cart item data to send to the server

 if (productIdEdit !== undefined) {
    var cartItem = {

     id: productId,
   idItem: productIditem,
     name: productName,
     image: productImage,
     price: productPrice,
     unityPrice: productUnityPrice,
     quantity: productQuantity,
     options: customizationOptions };

       editCart(cartItem);
     selectedOptionsproduits = [];
     } else {
     function generateUUID() {
   return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
     var r = (Math.random() * 16) | 0,
       v = c === 'x' ? r : (r & 0x3) | 0x8;
     return v.toString(16);
   });
 }

 var productIditem = generateUUID();

      var cartItem = {
     id: productId,
   idItem: productIditem,
     name: productName,
     image: productImage,
     price: productPrice,
     unityPrice: productUnityPrice,
     quantity: productQuantity,
     options: customizationOptions
   };

      addToCart(cartItem);
       selectedOptionsproduits = [];
     }
     // Send a POST request to add the item to the cart
    // addToCart(cartItem);
     selectedOptions = [];
       // After adding the item, update the cart sidebar and cart item count in the header
      // updateCartSidebar();
    //   $.ajax({
    //     url: "/fetch-cart",
    //     type: 'GET',
    //     dataType: "json",
    //     success: function(data) {
    //         var cartItemCount = data.cartItemCount;
    //         $('#cartItemCount').text(cartItemCount + " element(s)");
    //         }
    // });
   });


   function addToCart(cartItem) {
     $.ajax({
       url: '{{ route("cart.add") }}',
       method: 'POST',
       data: {
         cartItem: cartItem,
         _token: '{{ csrf_token() }}'
       },
       success: function(response) {
         // Update the cart sidebar with the updated cart data
         updateCartSidebar();
         // Show a success message or update the cart icon, etc.

         //alert('Product added to cart successfully'); // You can use your preferred success message here
       $('#customizeModal').modal('hide'); // Close the customize modal
  toastr.success('Votre commande est ajoutée avec succès');

       },
       error: function(error) {
         // Handle the error response from the server
         console.error('Error adding product to cart:', error);

         // Show an error message or handle the error gracefully
       }

     });

   }
 function editCart(cartItem) {
   //console.log(cartItem);
   //console.log(parseFloat(cartItem['totalPrice']));

     $.ajax({
        headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
       url: '{{ route("cart.edit") }}',
       method: 'POST',
       data: {
         cartItem: cartItem,
       },
       success: function(response) {
         // Update the cart sidebar with the updated cart data
         updateCartSidebar();
         // Show a success message or update the cart icon, etc.

         //alert('Product added to cart successfully'); // You can use your preferred success message here
       $('#customizeModal').modal('hide'); // Close the customize modal
  toastr.success('Votre commande est modifieé avec succès');
       },
       error: function(error) {
         // Handle the error response from the server
         console.error('Error editing product :', error);
         // Show an error message or handle the error gracefully
       }
     });
   }

 function getSelectedOptions() {
   var optionsWithPrice = '';
   var optionsWithoutPrice = '';

   selectedOptionsproduits.forEach(function(option) {
     var optionName = option.name;
     var optionPrice = option.price;
     var optionType = option.type;
     var optionQuantity = option.Quantity;

     if (!isNaN(optionPrice) && optionPrice != 0) {
       if (optionType === 'number') {
         optionsWithPrice += optionQuantity + '×' + optionName + '(' + optionPrice + 'TND), ';
       } else {
         optionsWithPrice += optionName + '(' + optionPrice + 'TND), ';
       }
     } else {
       if (optionType === 'number') {
         optionsWithoutPrice += optionQuantity + '×' + optionName + ', ';
       } else {
         optionsWithoutPrice += optionName + ', ';
       }
     }
   });

   // Convert "supplémentaires" to uppercase
   var uppercaseSupplementaires = '/';
   var productNote = $('#userNote').val();

   // Combine the options
   var designation = optionsWithoutPrice.trim();
  /* if (designation !== '' && optionsWithPrice !== '') {
     designation += '\n' + uppercaseSupplementaires + '\n' + optionsWithPrice.trim();
   } else {
     designation += optionsWithPrice.trim();
   }*/

   designation += '\n' + uppercaseSupplementaires + '\n' + optionsWithPrice.trim();
   // Add the note to the designation
   if(productNote !== '' ){
   designation += '\nNote: ' + productNote;}
 selectedOptionsproduits = [];
   return designation;
 }


 });
 function updateCartSidebar() {

   $('body').addClass('disable-interaction');
     // Make an AJAX request to fetch the updated cart data
     $.ajax({
        headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
       url: '{{ route("cart.fetch") }}',
       method: 'GET',
       success: function(response) {
         $('body').removeClass('disable-interaction');
         //console.log('marwen :',response.totalPrice);
        //  disableBTN(parseFloat(response.totalPrice));




       // Update the cart sidebar with the updated cart items
     var cartSidebarScroll = $('.cart-sidebar-scroll');
     cartSidebarScroll.empty(); // Clear existing content
 //console.log('marwen ',response.totalPrice);
     // Loop through the cart items and add them to the sidebar
     $.each(response.cartItems, function(index, cartItem) {
     var itemHTML = '<div class="cart-sidebar-item">';
     itemHTML += '<div class="media">';
     itemHTML += '<a ><img loading="lazy" src="' + cartItem.image + '" alt="product"></a>';
     itemHTML += '<div class="media-body">';
     itemHTML += '<h5><a href="menu-item-v1.html" title="' + cartItem.name + '">' + cartItem.name + '</a></h5>';
     if(cartItem.unityPrice==0){
        itemHTML += '<span>' + cartItem.quantity + '</span>';
     }else{
        itemHTML += '<span>' + cartItem.quantity + 'x ' + cartItem.unityPrice + 'TND</span>';
     }

     itemHTML += '</div></div>';
     itemHTML += '<div class="cart-sidebar-item-meta">';

     // Check if cartItem.options is a non-empty string
     if (typeof cartItem.options === 'string' && cartItem.options.trim() !== '') {
         itemHTML += '<span>' + cartItem.options + '</span>';
     }

     itemHTML += '</div>';
     itemHTML += '<div class="cart-sidebar-price">';
     itemHTML += cartItem.price + 'TND';
     itemHTML += '</div>';
    itemHTML += '<div class="customizeBtnedit" data-bs-toggle="modal" data-bs-target="#customizeModal" ' +
     'data-product-id-edit="' + cartItem.id + '" ' +
      'data-product-name="' + cartItem.name + '" ' +
     'data-product-image="' + cartItem.image + '" ' +
     'data-product-unityprice="' + cartItem.unityPrice + '" ' +
      'data-product-price="' + cartItem.price + '" ' +
      'data-product-qauntity="' + cartItem.quantity + '" ' +
       'data-product-item="' + cartItem.idItem + '" ' +

     'data-product-options=\'' + JSON.stringify(cartItem.options) + '\'>';

 // Add the button icon
 itemHTML += '<i class="fas fa-edit"></i>';

 // Close the customizeBtn div element
 itemHTML += '</div>';
   itemHTML += '<div class="remove-btn" data-item-id="' + cartItem.id + '">';
   itemHTML +=  '<i class="fas fa-times"></i>';
   itemHTML += '<span></span>';






   itemHTML += '</div></div>';

     cartSidebarScroll.append(itemHTML);
 });
      // Update the cart item count in the header
      var cartItemCount = $('.cart-item-count');
     cartItemCount.text(response.cartItemCount);

     var totalPriceElement = $('.cart-sidebar-footer span');
 totalPriceElement.text(response.totalPrice + 'TND');
//  disableBTN(parseFloat(response.totalPrice));
         // Show the cart sidebar
         $('#cartSidebarWrapper&').addClass('active');
       },
       error: function(error) {
         // Handle the error response from the server
         console.error('Error fetching cart data:', error);
         $('body').removeClass('disable-interaction');
         // Show an error message or handle the error gracefully
       }
     });
   }

   function disableBTN(tp){
     var mincmd = parseFloat($('#mincmd').val());
     if (tp==0){


       var totprice= parseFloat(($('.cart-sidebar-footer span').text()).replace('TND',''));
       if(totprice>mincmd){
         $('.btn-custom.btnconfirm').removeAttr('disabled');
         $('.alert.alert-danger.d-flex.align-items-center').hide();
         $('#alert-msg-mincmd').hide();
       }
       else{
        $('#alert-msg-mincmd').show();
         $('.btn-custom.btnconfirm').attr('disabled','disabled');
       }
     }else{
       if(tp>mincmd){
         $('.btn-custom.btnconfirm').removeAttr('disabled');
         $('.alert.alert-danger.d-flex.align-items-center').hide();
         $('#alert-msg-mincmd').hide();

       }else{
        $('#alert-msg-mincmd').show();

           $('.btn-custom.btnconfirm').attr('disabled','disabled');

       }

     }


   }
   </script>

    <script>
 $(document).ready(function() {
     // Hide all product containers initially
     $('.product-container').hide();

     // Handle click event on category images
     $('.ct-menu-category-item').on('click', function(e) {
         e.preventDefault();

         // Remove active class from all category items
         $('.ct-menu-category-item').removeClass('active');

         // Add active class to the clicked category item
         $(this).addClass('active');

         // Hide all product containers
         $('.product-container').hide();

         // Show products corresponding to the clicked category
         var categoryId = $(this).data('filter').replace('.', '');
         if(categoryId!="*"){
            $('.' + categoryId).show();

         }
     });
 });
 ;
// Set a flag in sessionStorage when the app is closed
// window.addEventListener('pagehide', function() {
//   sessionStorage.setItem('appClosed', 'true');
// });

// // Check the flag when the app is opened again
// window.addEventListener('pageshow', function() {
//   if (sessionStorage.getItem('appClosed') === 'true') {
//     // Delete cookies
//     document.cookie.split(';').forEach(function(c) {
//       document.cookie = c.replace(/^ +/, '').replace(/=.*/, '=;expires=' + new Date().toUTCString() + ';path=/');
//     });
//     // Remove the flag
//     sessionStorage.removeItem('appClosed');
//   }
// });
 </script>
   <!-- Newsletter End -->

   @include('client.layouts.footer_client')
   {{-- @endif --}}
   {{-- @endif --}}
</body>
</html>
