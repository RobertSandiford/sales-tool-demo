<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

include "functions.php";

class Quotes extends BaseController {
    //use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    function create() {

        if ( ! authCheck(['salesperson', 'manager']) ) {

            return json_encode(['success' => false, 'authorised' => false]);

        } else {

            $requestBody = file_get_contents('php://input');
            $data = json_decode($requestBody);

            // make quote
            $quote = new \App\Quote;
            $quote->owner = $_COOKIE['userid'];
            $quote->customer_name       = getPropertySafely($data, 'customer_name');
            $quote->address             = getPropertySafely($data, 'address');
            $quote->date                = (getPropertySafely($data, 'date')) ? getPropertySafely($data, 'date') . ' 00:00:00': '1970-01-01 00:00:00';
            $quote->appointment_number  = getPropertySafely($data, 'appointment_number');
            $quote->product_price       = getPropertySafely($data, 'total_product_price');
            $quote->installation_price  = getPropertySafely($data, 'total_installation_price');
            $quote->cost_price          = getPropertySafely($data, 'total_price');
            $quote->save();
            $quoteId = $quote->id;

            foreach($data->products as $productName => $product) {

                foreach ($product as $rowId => $row) {

                    // make quote product ("Item")
                    $quoteProduct = new \App\QuoteProduct;
                    $quoteProduct->quote_id = $quoteId;
                    $quoteProduct->product = $productName;
                    $quoteProduct->row = $rowId;
                    $quoteProduct->save();
                    $quoteProductId = $quoteProduct->id;

                    foreach ($row as $settingName => $value) {

                        // make quote product setting (could specify product to remove quote products table)
                        $qpSetting = new \App\QuoteProductSetting;
                        $qpSetting->quote_id = $quoteId;
                        $qpSetting->quote_product_id = $quoteProductId;
                        $qpSetting->setting = $settingName;
                        if ($settingName == "location" || $settingName == "description" ) {
                            $qpSetting->valueString = $value;
                        } else {
                            $qpSetting->value = $value;
                        }
                        $qpSetting->save();

                    }
                }
            }

            //die();
            
            return json_encode(['success' => true, 'id' => $quoteId]);
        }
    }

    function update() {

        if ( ! authCheck(['salesperson', 'manager']) ) {
            return json_encode(['success' => false, 'authorised' => false]);
        }

        $requestBody = file_get_contents('php://input');
        $data = json_decode($requestBody);

        $quote = \App\Quote::find($data->quoteId);

        $quote->customer_name       = getPropertySafely($data, 'customer_name');
        $quote->address             = getPropertySafely($data, 'address');
        $quote->date                = (getPropertySafely($data, 'date')) ?: '1970-01-01 00:00:00';
        $quote->appointment_number  = getPropertySafely($data, 'appointment_number');
        $quote->product_price       = getPropertySafely($data, 'total_product_price');
        $quote->installation_price  = getPropertySafely($data, 'total_installation_price');
        $quote->cost_price          = getPropertySafely($data, 'total_price');
        $quote->save();
        
        foreach($data->products as $productName => $product) {

            foreach ($product as $rowId => $row) {

                $quoteProduct = \App\QuoteProduct::where('quote_id', $data->quoteId)->where('product', $productName)->where('row', $rowId)->first();
                
                if ( ! $quoteProduct ) {
                   // make quote product (could be removed)
                    $quoteProduct = new \App\QuoteProduct;
                    $quoteProduct->quote_id = $data->quoteId;
                    $quoteProduct->product = $productName;
                    $quoteProduct->row = $rowId;
                    $quoteProduct->save();
                }

                $quoteProductId = $quoteProduct->id;


                foreach ($row as $settingName => $value) {
                    
                    // search for existing quote product setting
                    $qpSetting = \App\QuoteProductSetting::where('quote_product_id', $quoteProductId)->where('setting', $settingName)->first();

                    if ( $qpSetting ) {

                        // update the existing row
                        if ($settingName == "location" || $settingName == "description" ) {
                            $qpSetting->valueString = $value;
                        } else {
                            $qpSetting->value = ($value) ?: null;
                        }
                        $qpSetting->save();

                    } else {

                        // otherwise make new q p setting row
                        $qpSetting = new \App\QuoteProductSetting;
                        $qpSetting->quote_id = $data->quoteId;
                        $qpSetting->quote_product_id = $quoteProductId;
                        $qpSetting->setting = $settingName;
                        if ($settingName == "location" || $settingName == "description" ) {
                            $qpSetting->valueString = $value;
                        } else {
                            $qpSetting->value = $value;
                        }
                        $qpSetting->save();
                        
                    }

                }
            }
        }

        return json_encode(['success' => true]);
    }

    function updateNew() {

        if ( ! authCheck(['salesperson', 'manager']) ) {
            return json_encode(['success' => false, 'authorised' => false]);
        }

        $requestBody = file_get_contents('php://input');
        $data = json_decode($requestBody);

        if (isset($data->quoteId)) {
            return $this->update();
        } else {
            return $this->create();
        }
    }

    function delete() {

        if ( ! authCheck(['salesperson', 'manager']) ) {
            return json_encode(['success' => false, 'authorised' => false]);
        }

        $requestBody = file_get_contents('php://input');
        $data = json_decode($requestBody);

        $user = \App\User::find($data->userId);
        $user->delete();
        
        return json_encode(['success' => true]);
    }

    function savePricing() {

        if ( ! authCheck(['salesperson', 'manager']) ) {
            return json_encode(['success' => false, 'authorised' => false]);
        }

        $requestBody = file_get_contents('php://input');
        $data = json_decode($requestBody);

        // get quote
        $quote = \App\Quote::find($data->quoteId);

        $quote->finance = $data->finance;
        $quote->price = $data->price;
        $quote->pricing_complete = 1;

        $quote->save();
        
        return json_encode(['success' => true]);
    }

    function updatePricing() {

        if ( ! authCheck(['salesperson', 'manager']) ) {
            return json_encode(['success' => false, 'authorised' => false]);
        }

        $requestBody = file_get_contents('php://input');
        $data = json_decode($requestBody);

        // get quote
        $quote = \App\Quote::find($data->quoteId);

        $quote->finance = $data->finance;
        $quote->price   = $data->price;

        $quote->save();
        
        return json_encode(['success' => true]);
    }

    function saveFinancing() {

        if ( ! authCheck(['salesperson', 'manager']) ) {
            return json_encode(['success' => false, 'authorised' => false]);
        }

        $requestBody = file_get_contents('php://input');
        $data = json_decode($requestBody);

        // get quote
        $quote = \App\Quote::find($data->quoteId);

        $quote->finance_percentage      = $data->percentage;
        $quote->finance_type            = $data->type;
        $quote->finance_months          = $data->months;
        $quote->finance_rate            = $data->rate;
        $quote->finance_monthly         = $data->monthly;
        $quote->finance_total_finance   = $data->totalFinance;
        $quote->finance_total_price     = $data->total;
        $quote->financing_complete      = 1;

        $quote->save();
        
        return json_encode(['success' => true]);
    }

    function updateFinancing() {

        if ( ! authCheck(['salesperson', 'manager']) ) {
            return json_encode(['success' => false, 'authorised' => false]);
        }

        $requestBody = file_get_contents('php://input');
        $data = json_decode($requestBody);

        // get quote
        $quote = \App\Quote::find($data->quoteId);

        $quote->finance_percentage      = $data->percentage;
        $quote->finance_type            = $data->type;
        $quote->finance_months          = $data->months;
        $quote->finance_rate            = $data->rate;
        $quote->finance_monthly         = $data->monthly;
        $quote->finance_total_finance   = $data->totalFinance;
        $quote->finance_total_price     = $data->total;

        $quote->save();
        
        return json_encode(['success' => true]);
    }

    function sold() {

        if ( ! authCheck(['salesperson', 'manager']) ) {
            return json_encode(['success' => false, 'authorised' => false]);
        }

        $requestBody = file_get_contents('php://input');
        $data = json_decode($requestBody);

        // get quote
        $quote = \App\Quote::find($data->quoteId);

        $quote->finance_percentage      = $data->percentage;
        $quote->finance_type            = $data->type;
        $quote->finance_months          = $data->months;
        $quote->finance_rate            = $data->rate;
        $quote->finance_monthly         = $data->monthly;
        $quote->finance_total_finance   = $data->totalFinance;
        $quote->finance_total_price     = $data->total;
        $quote->financing_complete      = 1;
        $quote->sold                    = $data->sold;

        $quote->save();
        
        return json_encode(['success' => true, 'sold' => $quote->sold]);

    }

    // Upload offline quotes
    function upload() {
        
        if ( ! authCheck(['salesperson', 'manager']) ) {

            return json_encode(['success' => false, 'authorised' => false]);

        } else {
    
            $requestBody = file_get_contents('php://input');
            $data = json_decode($requestBody);

            $countQuotes = count((array)$data->quotes);
            $ids = [];

            if ( $countQuotes ) {

                foreach($data->quotes as $localQuoteId => $quoteData) {

                    $localQuoteId = (int) $localQuoteId;

                    // make quote
                    $quote = new \App\Quote;
                    $quote->owner = $_COOKIE['userid'];
                    $quote->local_quote_id          = $localQuoteId;

                    $quote->customer_name           = getPropertySafely($quoteData, 'customer_name');
                    $quote->address                 = getPropertySafely($quoteData, 'address');
                    $quote->date                    = (getPropertySafely($quoteData, 'date')) ? getPropertySafely($quoteData, 'date') . ' 00:00:00': '1970-01-01 00:00:00';
                    $quote->appointment_number      = getPropertySafely($quoteData, 'appointment_number');
                    $quote->product_price           = getPropertySafely($quoteData, 'product_price');
                    $quote->installation_price      = getPropertySafely($quoteData, 'installation_price');
                    $quote->cost_price              = getPropertySafely($quoteData, 'cost_price');

                    $quote->finance                 = getPropertySafely($quoteData, 'finance');
                    $quote->price                   = getPropertySafely($quoteData, 'price');
                    $quote->pricing_complete        = getPropertySafely($quoteData, 'pricing_complete') ?: 0;

                    $quote->finance_percentage      = getPropertySafely($quoteData, 'finance_percentage') ?: 0;
                    $quote->finance_type            = getPropertySafely($quoteData, 'finance_type');
                    $quote->finance_months          = getPropertySafely($quoteData, 'finance_months') ?: 0;
                    $quote->finance_rate            = getPropertySafely($quoteData, 'finance_rate') ?: 0;
                    $quote->finance_monthly         = getPropertySafely($quoteData, 'finance_monthly') ?: 0;
                    $quote->finance_total_finance   = getPropertySafely($quoteData, 'finance_total_finance') ?: 0;
                    $quote->finance_total_price     = getPropertySafely($quoteData, 'finance_total_price') ?: 0;
                    $quote->financing_complete      = getPropertySafely($quoteData, 'financing_complete') ?: 0;

                    $quote->sold                    = getPropertySafely($quoteData, 'sold') ?: 0;

                    $quote->save();
                    $quoteId = $quote->id;

                    $ids[$localQuoteId] = $quoteId;

                    foreach($quoteData->products as $productName => $product) {

                        foreach ($product as $rowId => $row) {

                            // make quote product ("Item")
                            $quoteProduct = new \App\QuoteProduct;
                            $quoteProduct->quote_id = $quoteId;
                            $quoteProduct->product = $productName;
                            $quoteProduct->row = $rowId;
                            $quoteProduct->save();
                            $quoteProductId = $quoteProduct->id;

                            foreach ($row as $settingName => $value) {

                                if ( ! in_array($settingName, ['quote_id', 'product']) ) {

                                    // make quote product setting (could specify product to remove quote products table)
                                    $qpSetting = new \App\QuoteProductSetting;
                                    $qpSetting->quote_id = $quoteId;
                                    $qpSetting->quote_product_id = $quoteProductId;
                                    $qpSetting->setting = $settingName;
                                    if ($settingName == "location") {
                                        $qpSetting->valueString = $value;
                                    } else {
                                        $qpSetting->value = $value;
                                    }
                                    $qpSetting->save();

                                }

                            }
                        }
                    }
                }

                return json_encode( [ 'success' => true, 'count' => $countQuotes, 'ids' => $ids ] );

            } else {

                return json_encode( [ 'success' => true, 'count' => $countQuotes, 'ids' => [] ] );

            }

        }

    }

}
