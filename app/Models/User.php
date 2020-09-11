<?php

namespace App\Models;

use App\Models\Traits\DocumentUploadTrait;
use App\Models\Traits\ReviewTrait;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Storage;
use Kodeine\Acl\Traits\HasRole;
class User extends Authenticatable
{
    use Notifiable,HasRole,ReviewTrait, DocumentUploadTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password','mobile','status','image', 'clinic_id', 'isactive', 'city', 'address', 'state'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function bookings(){
        return $this->hasMany('App\Models\BookingSlot', 'assigned_therapist');
    }

    public function getImageAttribute($value){
        if($value)
            return Storage::url($value);
        return null;
    }

}
