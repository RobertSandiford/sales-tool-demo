<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

include "functions.php";

class Settings extends BaseController
{
    //use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    function data() {

        if ( ! authCheck(['manager']) ) {

            return json_encode(['success' => false, 'authorised' => false]);

        } else {

            $requestBody = file_get_contents('php://input');
            $data = json_decode($requestBody);

            $query = \App\Setting::where('name', '!=', '')->where('category', '!=', '')->orderBy('id', 'ASC');
            $settings = $query->get();

            $settingsToReturn = [];
            foreach ($settings as $setting) {
                $settingsToReturn[$setting->category][$setting->id] = $setting;
            }

            $r = [
                'success' => true,
                'data' => $settingsToReturn
            ];
            return json_encode( $r );

        }

    }

    function update() {

        if ( ! authCheck(['manager']) ) {

            return json_encode(['success' => false, 'authorised' => false]);

        } else {

            $requestBody = file_get_contents('php://input');
            $data = json_decode($requestBody);

            foreach ($data as $categoryName => $category) {
                foreach ($category as $settingId => $value) {
                    
                    \App\Setting::where('id',$settingId)->update( ['value' => $value] );

                }
            }
            
            return json_encode(['success' => true]);
        }

    }
}
