<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeBookingSlots extends Model
{
    protected $table='home_booking_slots';

    protected $fillable=['order_id', 'grade', 'date', 'time', 'display_time', 'status', 'slot_id', 'is_instant'];


    public function timeslot(){
        return $this->belongsTo('App\Models\DailyBookingsSlots', 'slot_id');
    }


    public static function createAutomaticSchedule($order, $grade, $slot, $num_sessions, $status='pending'){

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

            HomeBookingSlots::create([
                'order_id'=>$order->id,
                'slot_id'=>$slots[$i]->id,
                'grade'=>$grade,
                'status'=>$status,
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
        return $this->hasOne('App\Models\Review', 'session_id')->where('reviews.order_id', $this->order_id);
    }

}
