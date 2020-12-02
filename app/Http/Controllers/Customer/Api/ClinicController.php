<?php

namespace App\Http\Controllers\Customer\Api;

use App\Models\Clinic;
use App\Models\Therapy;
use App\Models\TimeSlot;
use App\Models\Traits\Active;
use Cassandra\Time;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ClinicController extends Controller
{

    use Active;

    public function index(Request $request){

        if(!empty($request->lat) && !empty($request->lang)){

            $haversine = "(6371 * acos(cos(radians($request->lat))
                     * cos(radians(clinics.lat))
                     * cos(radians(clinics.lang)
                     - radians($request->lang))
                     + sin(radians($request->lat))
                     * sin(radians(clinics.lat))))";

            $clinics=Clinic::active()
                ->with(['commentscount', 'avgreviews'])
                ->select('clinics.*', DB::raw("$haversine as distance"))
                ->orderBy('distance', 'asc')
                ->get();
        }else{
            $clinics=Clinic::active()
                ->with(['commentscount', 'avgreviews'])
                ->get();
        }

        return [
            'status'=>'success',
            'data'=>[
                'clinics'=>$clinics
            ]
        ];
    }


    public function details(Request $request, $id){
        $clinic=Clinic::active()->with(['gallery', 'commentscount', 'avgreviews','therapies'=>function($therapies){
            $therapies->where('therapies.isactive', true)->where('clinic_therapies.isactive', true);
        }])->where('id', $id)->first();
        if($clinic)
            return [
                'status'=>'success',
                'data'=>[
                    'clinic'=>$clinic
                ]
            ];

        return [
            'status'=>'failed',
            'data'=>[

            ],
            'message'=>'No clinic found'
        ];
    }


    /*
     * In-Clinic Therapy Details
     */
    public function clinicTherapyDetails(Request $request, $clinicid, $therapyid){
        $timings=[];
        $dates=[];
        $clinic=Clinic::active()->with(['therapies'=>function($therapies) use ($therapyid){
            $therapies->where('therapies.isactive', true)->where('therapies.id', $therapyid)->where('clinic_therapies.isactive', true)->with(['commentscount', 'avgreviews','gallery']);
        }])->find($clinicid);
        if(!$clinic)
            return [
                'status'=>'failed',
                'message'=>'No clinic found'
            ];

        $date=date('Y-m-d');

        for($i=1; $i<=7;$i++){
            $datesarray[]=$date;
            $date=date('Y-m-d', strtotime('+1 days', strtotime($date)));
        }

        $slotscount=TimeSlot::getDatewiseSlotsCount($clinic->id, $datesarray);

        $date=date('Y-m-d');

        for($i=1; $i<=7;$i++){
            $dates[]=[
                'text'=>($i==1)?'Today':($i==2?'Tomorrow':date('d F', strtotime($date))),
                'text2'=>($i==1)?'':($i==2?'':date('D', strtotime($date))),
                'value'=>$date,
                'slots'=>($slotscount[$date]??0),
                'slots_text'=>($slotscount[$date]??0>0)?($slotscount[$date].' slots available'):'No Slots Available'
            ];
            $date=date('Y-m-d', strtotime('+1 days', strtotime($date)));
        }
        $date=date('Y-m-d h:i:s');
        for($i=9; $i<=17;$i++){
            $timings[]=[
                'text'=>date('h:i A', strtotime($date)),
                'value'=>date('H:i', strtotime($date))
            ];
            $date=date('Y-m-d H:i:s', strtotime('+1 hours', strtotime($date)));
        }

        $display_text=[

            'automatic'=>'One session per day will be booked at selected time based on availablity',
            'cutom'=>'You can select any number of slot on any day based on availability',

        ];

        return [
            'status'=>'success',
            'data'=>[
                'clinic'=>$clinic,
                'dates'=>$dates,
                'timings'=>$timings,
                'display_text'=>$display_text
            ]
        ];
    }



    /*
     * Get available time slots of clinic on given date
     */
    public function getAvailableSlots(Request $request, $clinic_id, $therapy_id){
        $date=$request->date??date('Y-m-d');
        $selected_date=$date;
        $today=date('Y-m-d');

        $clinic=Clinic::with(['therapies'=>function($therapies) use($therapy_id){
            $therapies->where('therapies.isactive', true)->where('therapies.id', $therapy_id)->where('clinic_therapies.isactive', true);
        }])->find($clinic_id);

        if(!$clinic || empty($clinic->therapies->toArray()))
            return [
                'status'=>'failed',
                'message'=>'No clinic found'
            ];

        $timeslots=TimeSlot::getTimeSlots($clinic, $date);

        for($i=1; $i<=7;$i++){
            $dates[]=[
                'text'=>($i==1)?'Today':($i==2?'Tomorrow':date('d F', strtotime($today))),
                'text2'=>($i==1)?'':($i==2?'':date('D', strtotime($today))),
                'value'=>$today,
            ];
            $today=date('Y-m-d', strtotime('+1 days', strtotime($today)));
        }

        $timeslots=[
            $timeslots['grade_1_slots'],
            $timeslots['grade_2_slots'],
            $timeslots['grade_3_slots'],
            $timeslots['grade_4_slots'],
        ];

        return [
            'status'=>'success',
            'data'=>compact('timeslots','dates', 'selected_date')
        ];
    }
}
