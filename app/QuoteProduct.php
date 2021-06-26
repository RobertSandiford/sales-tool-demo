<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuoteProduct extends Model
{
    //
    public $timestamps = false;

    public function quote() {
        return $this->belongsTo('\App\Quote');
    }

    public function quoteProductSettings() {
        return $this->hasMany('\App\QuoteProductSetting');
    }
}
