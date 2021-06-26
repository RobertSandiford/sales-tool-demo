<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuoteProductSetting extends Model
{
    //
    public $timestamps = false;

    public function quoteProduct() {
        return $this->belongsTo('\App\QuoteProduct');
    }

}
