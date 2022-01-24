<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class products extends Model
{
    protected $guarded=[];

    public function section()
    {
        return $this->belongsTo('App\sections');
    }

    public function product(){
        return $this->hasMany('App\invoices');
    }

}
