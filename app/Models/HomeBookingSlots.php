<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeBookingSlots extends Model
{
    protected $table='home_booking_slots';

    protected $fillable=['order_id', 'grade', 'date', 'time', 'display_time', 'status', 'slot_id', 'is_instant', 'assigned_therapist', 'therapy_id', 'therapist_status', 'start_time', 'end_time', 'price', 'verification_code', 'is_confirmed', 'is_paid'];

    public function assignedTo(){
        return $this->belongsTo('App\Models\Therapist', 'assigned_therapist');
    }

    public function timeslot(){
        return $this->belongsTo('App\Models\DailyBookingsSlots', 'slot_id');
    }

    public function therapy(){
        return $this->belongsTo('App\Models\Therapy', 'therapy_id');
    }


    public static function createAutomaticSchedule($order, $grade, $cost, $slot, $num_sessions, $status='pending'){

        $alloted=0;

        //var_dump($bookings);
        $slots=DailyBookingsSlots::where('date', '>=',  $slot->date)
            ->where('start_time', $slot->start_time)
            ->orderBy('date', 'asc')
            ->limit(200)
            ->get();

        if(count($slots) < $alloted)
            return false;

        $i=0;
        while($i<$num_sessions && isset($slots[$i])){
            $code=rand(0,9).''.rand(0,9).''.rand(0,9).''.rand(0,9).''.rand(0,9).''.rand(0,9);
            HomeBookingSlots::create([
                'order_id'=>$order->id,
                'slot_id'=>$slots[$i]->id,
                'grade'=>$grade,
                'status'=>$status,
                'date'=>$slots[$i]->date,
                'time'=>$slots[$i]->internal_start_time,
                'therapy_id'=>$order->details[0]->entity_id,
                'price'=>$cost,
                'verification_code'=>$code
            ]);
            $alloted++;
            $i++;
        }

        return $alloted==$num_sessions;

    }


    public function therapiesorder(){
        return $this->belongsTo('App\Models\Order', 'order_id');
    }

    public function order(){
        return $this->belongsTo('App\Models\Order', 'order_id');
    }

    public function review(){
        return $this->hasOne('App\Models\Review', 'session_id')->where('reviews.entity_type', 'App\Models\Therapist');
    }

    public function diseases(){
        return $this->belongsToMany('App\Models\Disease', 'customer_disease', 'therapiest_work_id', 'disease_id')
            ->withPivot('type')
            ->where('type', 'therapy');
    }

    public function painpoints(){
        return $this->belongsToMany('App\Models\PainPoint', 'Customer_point_pain','therapiest_work_id', 'pain_point_id')
            ->withPivot(['related_rating', 'type'])
            ->where('type', 'therapy');
    }

    public function treatment(){
        return $this->belongsTo('App\Models\Treatment', 'treatment_id');
    }

    public function mainDiseases(){
        return $this->morphToMany('App\Models\MainDisease', 'entity', 'customer_main_diseases', 'entity_id', 'disease_id');
    }

    public function reasonDiseases(){
        return $this->morphToMany('App\Models\ReasonDisease', 'entity', 'customer_reason_diseases', 'entity_id', 'reason_disease_id')->withPivot(['disease_id']);
    }

    public function diagnose(){
        return $this->morphToMany('App\Models\DiagnosePoint', 'entity', 'customer_diagnose_points', 'entity_id', 'diagnose_point_id')->withPivot(['before_value', 'after_value']);
    }

    public function treatmentsGiven(){
        return $this->morphToMany('App\Models\DiseasewiseTreatment', 'entity', 'selected_treatments', 'entity_id', 'treatment_id');
    }

    public function results(){
        if($this->therapist_result==1)
            return 'No Relief';
        else if($this->therapist_result==2)
            return 'Relief';
        else if($this->therapist_result==3)
            return 'Cured';
        else if($this->therapist_result==4)
            return 'Problem Increased';
        else
            return '';
    }


}
