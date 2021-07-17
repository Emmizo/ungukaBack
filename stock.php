<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class stock extends Model
{
  protected $fillable=['seasonID','farmID','quantity','price','status'];
    //
}
