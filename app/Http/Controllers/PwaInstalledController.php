<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PwaInstalledController extends Controller
{
    //
    public function check(){
        $url = 'https://dominos.foodexpress.site/'; // the URL you want to check
        $pwaManifest = json_decode(file_get_contents(public_path('manifest.json')), true);

        if (isset($pwaManifest['start_url']) && $pwaManifest['start_url'] === $url) {
            // The URL can be opened with your PWA
            echo 'The URL can be opened with your PWA';
        } elseif (isset($pwaManifest['scope']) && strpos($url, $pwaManifest['scope']) === 0) {
            // The URL is within the scope of your PWA
            echo 'The URL is within the scope of your PWA';
        } else {
            // The URL cannot be opened with your PWA
            echo 'The URL cannot be opened with your PWA';
        }
    }
}
