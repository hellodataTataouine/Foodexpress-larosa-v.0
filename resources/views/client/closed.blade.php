<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
     integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
     crossorigin=""/>
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<link rel="stylesheet" href="assetsClients/css/home.css">
<link rel="stylesheet" href="{{ asset('vendors/mdi/css/materialdesignicons.min.css') }}">
<style>
  .disabled {
    pointer-events:none;
    opacity:0.6;
}
section.disabled {
    pointer-events: none;
    filter: grayscale(100%);
    opacity: 0.5;
}
.status {
    position: relative;
    z-index: 1001;
    filter: none;
    opacity: 1;
    pointer-events: auto;
    color: black;
    background-color: white; /* Optional: to ensure the text is readable */
    padding: 10px;
    border-radius: 5px;
}


</style>
<header>
    <div class="logo">
        <img src="images/img/meryem.png" alt="Logo">
    </div>

</header>

<section>
  <nav>
    <br>
    <div class="status">
        @php
             $restaurant_id = env('Restaurant_id');
             $horaire = DB::table('horaires')->where('client_id', $restaurant_id)->get();
             $livraisons = Db::table('livraison_restaurant')->where('restaurant_id',$restaurant_id)->get();
        @endphp
      @if ($horaire)
        @foreach ($horaire as $item)
          @php
              $tnow = date('H:i:s');
          @endphp
          @if ($tnow>= $item->heure_ouverture && $tnow<= $item->heure_fermeture && $status==1)
              <p>Status :<span style="color: green;">En service</span></p>
          @else
          <p class="special-p">Status : Actuellement, nos portes sont <span style="color: red;">fermées</span></p>
          <p>Nous sommes fermés aujourd'hui. Merci de revenir un autre jour.</p>
          @endif
          <input type="hidden" id="heure_ouverture" value="{{ $item->heure_ouverture }}">
          <input type="hidden" id="heure_fermeture" value="{{ $item->heure_fermeture }}">
        @endforeach

      @endif



  </div><br>

    <div id="cartDetailsAndDeliveryMethod" class="cartDetailAndDeliveryMethode disabled">

      @if (count($livraisons) > 0)
        <h6>Choisissez la méthode de livraison</h6>

      @if ($horaire)
        @foreach ($horaire as $item)
          {{Cookie::queue('heure_ouverture',$item->heure_ouverture)}}
          {{Cookie::queue('heure_fermeture',$item->heure_fermeture)}}
          <input type="hidden" id="heure_ouverture" value="{{ $item->heure_ouverture }}">
          <input type="hidden" id="heure_fermeture" value="{{ $item->heure_fermeture }}">
        @endforeach

      @endif

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

                            @if (!($item->heure_ouverture < $tnow  && $item->heure_fermeture > $tminus))
                              <li class="delivery-method-btn disabled"  data-method="{{ $livraisonType->methode }}" data-id="{{ $livraisonType->id }}">
                                <i class="fas fa-truck"></i>
                                <i class="far fa-clock"></i>
                                <span>{{ $livraisonType->methode }}</span>
                              </li>

                            @else
                                <li class="delivery-method-btn"  data-method="{{ $livraisonType->methode }}" data-id="{{ $livraisonType->id }}">
                                  <i class="fas fa-truck"></i>
                                  <i class="far fa-clock"></i>
                                  <span>{{ $livraisonType->methode }}</span>
                                </li>
                            @endif
                        @endforeach
                    @endif
                    @endif

              @endif

            @endif
          @endforeach
          {{-- @if(!request()->is('client/login'))
          @if(!is_null($client->reservation_table) && $client->reservation_table == 1) --}}
            {{-- <a href="{{url('/reservationtables')}}" >Réserver une table </a> --}}
            {{-- <li class="delivery-method-btn" onclick="reserver()">
              <i class="glyphicon glyphicon-cutlery"></i>
              <i class="far fa-clock"></i>
              <span>Réserver une table</span>
            </li>
          @endif
        @endif --}}
        </ul>




                <div id="ccdelivery-time-container">
                  <div class="form-group">
                    <label for="date">Date</label>
                    <input type="date" class="form-control" id="date" name="date" required>
                    {{-- @error('date')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror --}}
                  </div>

                    <div id="delivery-time-container">
                      <label for="delivery_time">Choisissez l'heure :</label>
                      <select id="delivery_time" name="delivery_time" class="form-control">
                        <option value="" disabled selected>Sélectionnez une heure</option>
                      </select>
                    </div>
                    <br>
                    <form method="POST" action="#" id="formcc">
                      @csrf
                    <button type="button" class="btn-custom primary" id="btnsubmitccmode">Valider</button>
                    </form>
                </div>
</div>
      @endif
      <div class="livraisonContent disabled" id="livraisonContent" style="display:none;">

                      @if ($horaire)

                          @php
                              $tnow = date('H:i:s');
                              $tminus = date('H:i:s', strtotime(' -15 minutes'));
                          @endphp
                          @foreach ($horaire as $item)

                              @if ($item->heure_ouverture < $tnow  && $item->heure_fermeture > $tminus)

                                    <br><br>
                                    <form method="POST" action="#" id="form">
                                      @csrf

                                        <div class="icon-input-container address-container">
                                            <!-- Geolocation icon -->
                                            <i class="mdi mdi-crosshairs-gps geolocation-icon"  onclick="geolocation()" style="font-size: xx-large;"></i>
                                            <!-- Input type text -->
                                            <input type="text" class="form-control" name="address-input" id="address-input"  onkeydown="autocompleteInput();changecodepostalstate()" placeholder="Entrez une ville, un code postale...">

                                        </div>
                                        <br>
                                        <div style="display: flex;">
                                          <input type="text" class="form-control" name="ville" id="ville" placeholder="Ville">&nbsp;
                                          <input type="text" class="form-control" name="road" id="road" placeholder="Rue">&nbsp;
                                          <input type="text" class="form-control" name="postal_code"  id="postal_code" placeholder="Code Postal">
                                      </div>
                                        <br>
                                        {{-- @if (session('showAlert'))
                                            <script>
                                              $(document).ready(function(){
                                                $('#showalert').show();
                                              });


                                            </script>
                                        @else
                                            <script>
                                              $(document).ready(function(){
                                                $('#showalert').hide();
                                              });
                                            </script>
                                        @endif --}}

                                        {{-- <div class="alert alert-warning" id="showalert" role="alert" >
                                          Désolés, l'adresse fournie est hors de notre zone de service.
                                        </div> --}}

                                        <button type="button" class="btn-custom primary" id="submitpostalcode">Valider</button>

                                    </form>
                                    <br>

                              @endif
                          @endforeach

                      @endif



                    </div>
                    <br><br>






                    <p class="p-footer-closed">Hello Data &copy; 2024 Tous Droits réservés</p>
                </nav>

  <article>
    <div  class="disabled" id="map" style="height: 100%; width: 100%;"></div>
  </article>
</section>

<!-- Leaflet JavaScript -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
     integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
     crossorigin=""></script>
     <script src="https://cdn.jsdelivr.net/npm/geolib@3.3.4/lib/index.min.js"></script>

<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
<script src="https://cdn.jsdelivr.net/npm/leaflet@1.7.1/dist/leaflet.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
{{-- <script src="assetsClients/js/plugins/jquery-3.4.1.min.js"></script> --}}
<script src="assetsClients/js/home.js"></script>

