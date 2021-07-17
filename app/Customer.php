<?php

namespace App;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model implements HasMedia
{
    use HasMediaTrait;
    protected $fillable=[
        'fname','lname','phone','identity','photo',
         ];
    //
}
