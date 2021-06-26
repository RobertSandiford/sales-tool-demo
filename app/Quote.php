<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    //
    //public $timestamps = false;

    public function quoteProducts() {
        return $this->hasMany('\App\QuoteProduct');
    }

    public function Owner() {
        return $this->belongsTo('\App\User', 'owner');
    }
}
