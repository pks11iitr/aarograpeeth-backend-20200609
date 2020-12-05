<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use App\Models\Traits\Active;
use App\Models\Traits\DocumentUploadTrait;
use App\Models\Traits\ReviewTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Therapist extends Authenticatable implements JWTSubject
{
    use DocumentUploadTrait, Active,ReviewTrait;

	protected $table='therapists';

	protected $fillable=['name','email','mobile','password', 'image','address','city','state','isactive','clinic_id', 'status'];

    public function locations(){
        return $this->hasMany('App\Models\TherapistLocations', 'therapist_id')->orderBy('id', 'desc');
    }


    public function therapies(){
        return $this->belongsToMany('App\Models\Therapy', 'therapist_therapies', 'therapist_id', 'therapy_id')->withPivot('therapy_id', 'therapist_id', 'therapist_grade', 'id', 'isactive');
    }

    public function clinic(){
        return $this->belongsTo('App\Models\Clinic','clinic_id');
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function getImageAttribute($value){
        if($value)
            return Storage::url($value);
        return null;
    }


    public function bookings(){
        return $this->hasMany('App\Models\HomeBookingSlots', 'assigned_therapist');
    }


    // excludes therapist having pending bookings for the date
    public static function getAvailableHomeTherapist($therapy_id,$slot_id,$lat=null,$lang=null){

        if(!$slot_id)
            $date=date('Y-m-d');
        else{
            $daily_booking_slot=DailyBookingsSlots::find($slot_id);
            $date=$daily_booking_slot->date;
        }

        $haversine = "(6371 * acos(cos(radians($lat))
                     * cos(radians(therapists.last_lat))
                     * cos(radians(therapists.last_lang)
                     - radians($lang))
                     + sin(radians($lat))
                     * sin(radians(therapists.last_lat))))";

        $therapist=Therapist::active()
            ->whereHas('therapies', function($therapies) use($therapy_id){
                $therapies->where('therapies.id', $therapy_id);
            })
            ->whereDoesntHave('bookings', function($bookings) use($slot_id){
                $bookings->where('slot_id', $slot_id);
            })
            ->whereDoesntHave('bookings', function($bookings) use($date){
                $bookings->where('date', $date)
                    ->where('home_booking_slots.status', 'pending');
            })
            ->where(DB::raw("TRUNCATE($haversine,2)"), '<', env('THERAPIST_CIRCLE_LENGTH'))
            ->where(function($therapist){
                $therapist->whereNull('from_date')
                    ->orWhere('from_date','>', date('Y-m-d H:i:s'));
            })
            ->where(function($therapist) use($daily_booking_slot){
                $therapist->whereNull('therapists.from_date')
                    ->orWhere('therapists.from_date','>', isset($daily_booking_slot)?($daily_booking_slot->date.' '.$daily_booking_slot->internal_start_time):date('Y-m-d').'23:59:59')
                    ->orWhere('therapists.to_date','<',
                        isset($daily_booking_slot)?($daily_booking_slot->date.' '.$daily_booking_slot->internal_start_time):date('Y-m-d').'00:00:00');
            })
            ->select('id', 'name')
            ->get();//die;

        return $therapist;
    }

}
