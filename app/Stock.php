<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model 
{
  protected $fillable=['seasonID','cropsID','quantity','price','status'];
    //
}
