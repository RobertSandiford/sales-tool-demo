<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;

include "functions.php";

class QuotesFront extends BaseController
{


    // Basic (new) quote page
    function new() {
        $settings = \App\Setting::get();

        return view('app', ['data' => ['settings' => $settings] ]);
    }


    // View existing quote
    function view($quoteId) {
        $settings = \App\Setting::get();
        $quote = \App\Quote::with('quoteProducts.quoteProductSettings')->find($quoteId);

        return view('app', ['data' => ['settings' => $settings, 'quote' => $quote] ]);
    }


    // Pricing page
    function pricing($quoteId) {
        return $this->showQuote($quoteId);
    }

    // Offline pricing page
    function pricingLocal() {
        $settings = \App\Setting::get();

        return view('app', ['data' => ['settings' => $settings] ]);
    }


    // Financing page
    function financing($quoteId) {
        return $this->showQuote($quoteId);
    }

    // Offline financing page
    function financingLocal() {
        $settings = \App\Setting::get();

        return view('app', ['data' => ['settings' => $settings] ]);
    }


    // Load the settings and basic quote data. Not a route. Called by routes.
    function showQuote($quoteId) {
        $settings = \App\Setting::get();
        $quote = \App\Quote::find($quoteId);

        return view('app', ['data' => ['settings' => $settings, 'quote' => $quote] ]);
    }


    // Quotes page
    function list() {

        $quotes = \App\Quote::with('owner')->get();
        $users = \App\User::get();

        return view('app', ['data' => ['quotes' => $quotes, 'users' => $users] ]);
    }

    // Offline quotes page (just a holding page, not functional)
    function listOffline() {
        return view('app', ['message' => "You appear to be offline. You can't use this page unless you have an internet connect"]);
    }

    
}
