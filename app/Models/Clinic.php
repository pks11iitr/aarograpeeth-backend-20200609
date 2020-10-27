<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use App\Models\Traits\DocumentUploadTrait;
use Illuminate\Support\Facades\Storage;

class Clinic extends Model
{
    use DocumentUploadTrait;

    protected $table = 'clinics';

    protected $fillable=['name','description','address','city','state','contact','lat','lang','image','isactive'];

    protected $hidden = ['created_at','deleted_at','updated_at'];

    public function getImageAttribute($value){
        if($value)
            return Storage::url($value);
        return null;
    }

    public function therapies(){
        return $this->belongsToMany('App\Models\Therapy', 'clinic_therapies', 'clinic_id', 'therapy_id')->withPivot('grade1_price', 'grade2_price','grade3_price','grade4_price', 'grade1_original_price','grade2_original_price','grade3_original_price','grade4_original_price');
    }

    public function getGrade1OriginalPriceAttribute($value){
        if($this->pivot->grade1_original_price==null)
            return '';
        return $value;
    }
    public function getGrade2OriginalPriceAttribute($value){
        if($value==null)
            return '';
        return $value;
    }
    public function getGrade3OriginalPriceAttribute($value){
        if($value==null)
            return '';
        return $value;
    }
    public function getGrade4OriginalPriceAttribute($value){
        if($value==null)
            return '';
        return $value;
    }

    public function therapist(){
        return $this->hasMany('App\Models\User', 'clinic_id');
    }


    public function getAvailableTherapist($time_slot){
        return $this->therapist()->where('users.isactive', true)->whereDoesntHave('bookings', function($bookings) use ($time_slot){
            $bookings->where('slot_id', $time_slot)
                ->where('bookings_slots.status', 'pending');
        })->select('users.name', 'users.id')->get();
    }
}
