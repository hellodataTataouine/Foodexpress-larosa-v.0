 <style>
        /* Style for the container that holds the content and the fixed button */
        .container {
            position: relative;
        }

        /* Style for the fixed button */
        .fixed-button {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: #02463a;
            color: #fff;
            text-align: center;
            padding: 10px;
			border-radius: 25px;
			text-transform: uppercase;
			line-height: 24px;
			color: rgba(255,255,255,var(--tw-text-opacity));
            z-index: 999; /* Set a high z-index value to ensure it overlays other content */
			cursor: pointer;
        }

    </style>

<div class="fixed-button">
	<!--<i class="fas fa-shopping-cart">

	</i>   <span style="color: white; font-weight: bold;">Voir Mon Panier</span> -->
	 <li class="cart-dropdown-wrapper cart-trigger" id="fixed-button">
               <span style="color: white; font-weight: bold;">Ma Commande</span>
              <i class="flaticon-shopping-bag" ></i>

            </li>
</div>
