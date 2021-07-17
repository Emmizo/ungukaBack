<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Farm extends Model
{
    protected $fillable=[
        'photo','cropsType','UPI','location','plotsize',
         ];
    //
    public function farms(){
        return $this->hasMany(farm::class);
        }
        
        
}
