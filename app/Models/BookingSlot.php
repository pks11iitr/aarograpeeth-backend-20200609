<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingSlot extends Model
{
    protected $table='bookings_slots';

    protected $fillable=['order_id', 'clinic_id', 'therapy_id', 'slot_id', 'status', 'grade', 'price', 'assigned_therapist', 'date', 'time', 'therapist_status', 'start_time', 'end_time', 'is_confirmed', 'is_paid'];


    public static function createAutomaticSchedule($order, $grade, $cost, $slot, $num_sessions, $status){
        //var_dump($slot->toArray());die;

        $booked_slots=BookingSlot::with('timeslot')
            ->whereHas('timeslot', function($timeslots) use($slot){
                    $timeslots->where('date', '>=', $slot->date);
             })
            ->where('is_confirmed',true)
            ->where('grade', $grade)
            ->get();
        $bookings=[];
        foreach($booked_slots as $bs){
            if(!isset($bookings[$bs->timeslot->date.$bs->timeslot->internal_start_time]))
                $bookings[$bs->timeslot->date.$bs->timeslot->internal_start_time]=[
                    'g1'=>0,
                    'g2'=>0,
                    'g3'=>0,
                    'g4'=>0,
                ];
            $bookings[$bs->timeslot->date.$bs->timeslot->internal_start_time]['g'.$bs->grade]+=1;
        }
        //var_dump($bookings);
        $slots=TimeSlot::active()->
            where('clinic_id',$slot->clinic_id)
            ->where('date', '>=',  $slot->date)
            ->where('start_time', $slot->start_time)
            ->where(function($slots) use($grade){
                switch($grade){
                    case 1:
                        $slots->where('grade_1','>', 0);
                        break;
                    case 2:
                        $slots->where('grade_2','>', 0);
                        break;
                    case 3:
                        $slots->where('grade_3','>', 0);
                        break;
                    case 4:
                        $slots->where('grade_4','>', 0);
                        break;
                }
            })
            ->orderBy('date', 'asc')
            ->limit(200)
            ->get();

        //var_dump($slots->toArray());die('3131');

        $i=0;
        $alloted=0;
        while($i<$num_sessions && isset($slots[$i])){
            if(!isset($bookings[$slots[$i]->date.$slots[$i]->internal_start_time])){
                BookingSlot::create([
                    'order_id'=>$order->id,
                    'clinic_id'=>$order->details[0]->clinic_id,
                    'therapy_id'=>$order->details[0]->entity_id,
                    'slot_id'=>$slots[$i]->id,
                    'grade'=>$grade,
                    'status'=>$status,
                    'price'=>$cost,
                    'date'=>$slots[$i]->date,
                    'time'=>$slot->internal_start_time
                ]);
                $alloted+=1;
            }
            else{
                $grade1='grade_'.$grade;
                //var_dump($bookings[$slots[$i]->date]);die;
                if($slots[$i]->$grade1 > $bookings[$slots[$i]->date.$slots[$i]->internal_start_time]['g'.$grade]){
                    BookingSlot::create([
                        'order_id'=>$order->id,
                        'clinic_id'=>$order->details[0]->clinic_id,
                        'therapy_id'=>$order->details[0]->entity_id,
                        'slot_id'=>$slots[$i]->id,
                        'grade'=>$grade,
                        'status'=>$status,
                        'date'=>$slots[$i]->date,
                        'time'=>$slot->internal_start_time
                    ]);
                    $alloted+=1;
                }
            }
            $i++;
        }
        //var_dump($i);die;
        return $alloted==$num_sessions;
    }

    public function timeslot(){
        return $this->belongsTo('App\Models\TimeSlot', 'slot_id');
    }

    public function order(){
        return $this->belongsTo('App\Models\Order', 'order_id');
    }

    public function review(){
        return $this->hasOne('App\Models\Review', 'session_id')->where('reviews.entity_type', 'App\Models\User');
    }

    public function assignedTo(){
        return $this->belongsTo('App\Models\User', 'assigned_therapist');
    }

    public function clinic(){
        return $this->belongsTo('App\Models\Clinic', 'clinic_id');
    }

    public function therapy(){
        return $this->belongsTo('App\Models\Therapy', 'therapy_id');
    }

    public function diseases(){
        return $this->belongsToMany('App\Models\Disease', 'customer_disease', 'therapiest_work_id', 'disease_id')->where('type', 'clinic');
    }

    public function painpoints(){
        return $this->belongsToMany('App\Models\PainPoint', 'customer_point_pain','therapiest_work_id', 'pain_point_id')->where('type', 'clinic');
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

    public function customerResults(){
        if($this->customer_result==1)
            return 'No Relief';
        else if($this->customer_result==2)
            return 'Relief';
        else if($this->customer_result==3)
            return 'Cured';
        else if($this->customer_result==4)
            return 'Problem Increased';
        else
            return '';
    }


}
