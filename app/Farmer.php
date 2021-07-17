<?php

namespace App;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\Models\Media;
use Tymon\JWTAuth\Contracts\JWTSubject;
class Farmer extends Model 
{
    use HasMediaTrait;
    protected $fillable=[
        'fname','lname','phone','identity','photo',
         ];
    // public function getJWTIdentifier()
    // {
    //     return $this->getKey();
    // }
    // public function getJWTCustomClaims()
    // {
    //     return [];
    // }
}
