<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DailyBookingsSlots extends Model
{
    protected $table='daily_time_slots';

    protected $fillable=['start_time','date','duration','internal_start_time', 'isactive'];

    public static function getTimeSlots($therapy,$date){

        $timeslots=DailyBookingsSlots::orderBy('internal_start_time', 'asc')
            ->where('date',$date)
            ->where(DB::raw('concat(date, " ", internal_start_time)'), '>', date('Y-m-d H:i:s'))
            ->get();

        $startdate=date('Y-m-d', strtotime($date));
        for($i=1; $i<=7;$i++){
            $dates[]=[
                'text'=>($i==1)?'Today':($i==2?'Tomorrow':date('d F', strtotime($date))),
                'text2'=>($i==1)?'':($i==2?'':date('D')),
                'value'=>$date
            ];
            $startdate=date('Y-m-d', strtotime('+1 days', strtotime($date, strtotime($startdate))));
        }

        $grade_1_slots = [
            'morning' => [],
            'afternoon' => [],
            'evening' => [],
        ];
        $grade_2_slots = [
            'morning' => [],
            'afternoon' => [],
            'evening' => [],
        ];
        $grade_3_slots = [
            'morning' => [],
            'afternoon' => [],
            'evening' => [],
        ];
        $grade_4_slots = [
            'morning' => [],
            'afternoon' => [],
            'evening' => [],
        ];

        foreach($timeslots as $ts){
            if ($ts->internal_start_time < '12:00:00') {
                $grade_1_slots['morning'][] = [
                    'id'=>$ts->id,
                    'display'=>$ts->start_time,
                    'is_active'=>1
                ];
                $grade_2_slots['morning'][] = [
                    'id'=>$ts->id,
                    'display'=>$ts->start_time,
                    'is_active'=>1
                ];
                $grade_3_slots['morning'][] = [
                    'id'=>$ts->id,
                    'display'=>$ts->start_time,
                    'is_active'=>1
                ];
                $grade_4_slots['morning'][] = [
                    'id'=>$ts->id,
                    'display'=>$ts->start_time,
                    'is_active'=>1
                ];

            } else if ($ts->internal_start_time < '17:00:00') {
                $grade_1_slots['afternoon'][] = [
                    'id'=>$ts->id,
                    'display'=>$ts->start_time,
                    'is_active'=>1
                ];
                $grade_2_slots['afternoon'][] = [
                    'id'=>$ts->id,
                    'display'=>$ts->start_time,
                    'is_active'=>1
                ];
                $grade_3_slots['afternoon'][] = [
                    'id'=>$ts->id,
                    'display'=>$ts->start_time,
                    'is_active'=>1
                ];
                $grade_4_slots['afternoon'][] = [
                    'id'=>$ts->id,
                    'display'=>$ts->start_time,
                    'is_active'=>1
                ];

            } else {
                $grade_1_slots['evening'][] = [
                    'id'=>$ts->id,
                    'display'=>$ts->start_time,
                    'is_active'=>1
                ];
                $grade_2_slots['evening'][] = [
                    'id'=>$ts->id,
                    'display'=>$ts->start_time,
                    'is_active'=>1
                ];
                $grade_3_slots['evening'][] = [
                    'id'=>$ts->id,
                    'display'=>$ts->start_time,
                    'is_active'=>1
                ];
                $grade_4_slots['evening'][] = [
                    'id'=>$ts->id,
                    'display'=>$ts->start_time,
                    'is_active'=>1
                ];
            }
        }


        $grade_4_slots['name'] = 'Silver';
        $grade_4_slots['price'] = 'Rs. ' . $therapy->grade4_price ?? 0;
        $grade_4_slots['count'] = null;
        $grade_4_slots['timeslots'] = [];
        $grade_4_slots['timeslots'][]=[
            'slot_group'=>'morning',
            'available_slots'=>$grade_4_slots['morning']
        ];
        $grade_4_slots['timeslots'][]=[
            'slot_group'=>'afternoon',
            'available_slots'=>$grade_4_slots['afternoon']
        ];
        $grade_4_slots['timeslots'][]=[
            'slot_group'=>'evening',
            'available_slots'=>$grade_4_slots['evening']
        ];
        unset($grade_4_slots['morning']);
        unset($grade_4_slots['afternoon']);
        unset($grade_4_slots['evening']);

        $grade_3_slots['name'] = 'Gold';
        $grade_3_slots['price'] = 'Rs. ' . $therapy->grade3_price ?? 0;
        $grade_3_slots['count'] = null;
        $grade_3_slots['timeslots'] = [];
        $grade_3_slots['timeslots'][]=[
            'slot_group'=>'morning',
            'available_slots'=>$grade_3_slots['morning']
        ];
        $grade_3_slots['timeslots'][]=[
            'slot_group'=>'afternoon',
            'available_slots'=>$grade_3_slots['afternoon']
        ];
        $grade_3_slots['timeslots'][]=[
            'slot_group'=>'evening',
            'available_slots'=>$grade_3_slots['evening']
        ];
        unset($grade_3_slots['morning']);
        unset($grade_3_slots['afternoon']);
        unset($grade_3_slots['evening']);


        $grade_2_slots['name'] = 'Platinum';
        $grade_2_slots['price'] = 'Rs. ' . $therapy->grade2_price ?? 0;
        $grade_2_slots['count'] = null;
        $grade_2_slots['timeslots'] = [];
        $grade_2_slots['timeslots'][]=[
            'slot_group'=>'morning',
            'available_slots'=>$grade_2_slots['morning']
        ];
        $grade_2_slots['timeslots'][]=[
            'slot_group'=>'afternoon',
            'available_slots'=>$grade_2_slots['afternoon']
        ];
        $grade_2_slots['timeslots'][]=[
            'slot_group'=>'evening',
            'available_slots'=>$grade_2_slots['evening']
        ];
        unset($grade_2_slots['morning']);
        unset($grade_2_slots['afternoon']);
        unset($grade_2_slots['evening']);

        $grade_1_slots['name'] = 'Diamond';
        $grade_1_slots['price'] = 'Rs. ' . $therapy->grade1_price ?? 0;
        $grade_1_slots['count'] = null;
        $grade_1_slots['timeslots'] = [];
        $grade_1_slots['timeslots'][]=[
            'slot_group'=>'morning',
            'available_slots'=>$grade_1_slots['morning']
        ];
        $grade_1_slots['timeslots'][]=[
            'slot_group'=>'afternoon',
            'available_slots'=>$grade_1_slots['afternoon']
        ];
        $grade_1_slots['timeslots'][]=[
            'slot_group'=>'evening',
            'available_slots'=>$grade_1_slots['evening']
        ];
        unset($grade_1_slots['morning']);
        unset($grade_1_slots['afternoon']);
        unset($grade_1_slots['evening']);

        return compact('grade_1_slots', 'grade_2_slots', 'grade_3_slots', 'grade_4_slots');

    }

    public static function getRescheduleTimeSlots($therapy,$date,$booking){
        $timeslots=DailyBookingsSlots::orderBy('internal_start_time', 'asc')
            ->where('date',$date)
            ->where(DB::raw('concat(date, " ", internal_start_time)'), '>', date('Y-m-d H:i:s'))
            ->get();

        $startdate=date('Y-m-d', strtotime($date));
        for($i=1; $i<=7;$i++){
            $dates[]=[
                'text'=>($i==1)?'Today':($i==2?'Tomorrow':date('d F', strtotime($date))),
                'text2'=>($i==1)?'':($i==2?'':date('D')),
                'value'=>$date
            ];
            $startdate=date('Y-m-d', strtotime('+1 days', strtotime($date, strtotime($startdate))));
        }

        $grade_1_slots = [
            'morning' => [],
            'afternoon' => [],
            'evening' => [],
        ];
        $grade_2_slots = [
            'morning' => [],
            'afternoon' => [],
            'evening' => [],
        ];
        $grade_3_slots = [
            'morning' => [],
            'afternoon' => [],
            'evening' => [],
        ];
        $grade_4_slots = [
            'morning' => [],
            'afternoon' => [],
            'evening' => [],
        ];

        foreach($timeslots as $ts){
            if ($ts->internal_start_time < '12:00:00') {
                $grade_1_slots['morning'][] = [
                    'id'=>$ts->id,
                    'display'=>$ts->start_time,
                    'is_active'=>1
                ];
                $grade_2_slots['morning'][] = [
                    'id'=>$ts->id,
                    'display'=>$ts->start_time,
                    'is_active'=>1
                ];
                $grade_3_slots['morning'][] = [
                    'id'=>$ts->id,
                    'display'=>$ts->start_time,
                    'is_active'=>1
                ];
                $grade_4_slots['morning'][] = [
                    'id'=>$ts->id,
                    'display'=>$ts->start_time,
                    'is_active'=>1
                ];

            } else if ($ts->internal_start_time < '17:00:00') {
                $grade_1_slots['afternoon'][] = [
                    'id'=>$ts->id,
                    'display'=>$ts->start_time,
                    'is_active'=>1
                ];
                $grade_2_slots['afternoon'][] = [
                    'id'=>$ts->id,
                    'display'=>$ts->start_time,
                    'is_active'=>1
                ];
                $grade_3_slots['afternoon'][] = [
                    'id'=>$ts->id,
                    'display'=>$ts->start_time,
                    'is_active'=>1
                ];
                $grade_4_slots['afternoon'][] = [
                    'id'=>$ts->id,
                    'display'=>$ts->start_time,
                    'is_active'=>1
                ];

            } else {
                $grade_1_slots['evening'][] = [
                    'id'=>$ts->id,
                    'display'=>$ts->start_time,
                    'is_active'=>1
                ];
                $grade_2_slots['evening'][] = [
                    'id'=>$ts->id,
                    'display'=>$ts->start_time,
                    'is_active'=>1
                ];
                $grade_3_slots['evening'][] = [
                    'id'=>$ts->id,
                    'display'=>$ts->start_time,
                    'is_active'=>1
                ];
                $grade_4_slots['evening'][] = [
                    'id'=>$ts->id,
                    'display'=>$ts->start_time,
                    'is_active'=>1
                ];
            }
        }

        switch($booking->grade){
            case '1':return $grade_1_slots;
            case '2':return $grade_2_slots;
            case '3':return $grade_3_slots;
            case '4':return $grade_4_slots;
        }
    }


    public static function getTimeSlotsForAdmin($therapy, $date, $grade){

        $timeslots=DailyBookingsSlots::orderBy('internal_start_time', 'asc')
            ->where('date',$date)
            ->where(DB::raw('concat(date, " ", internal_start_time)'), '>', date('Y-m-d H:i:s'))
            ->get();

        $slots=[];

        foreach($timeslots as $ts){
                $slots[] = [
                    'id'=>$ts->id,
                    'display'=>$ts->start_time,
                    'is_active'=>1,
                    'date'=>$ts->date
                ];
        }

        return $slots;

    }


}
