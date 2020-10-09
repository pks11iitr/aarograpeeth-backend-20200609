<?php

namespace App\Http\Controllers\Therapist\Api;

use App\Models\Therapist;
use App\Models\TherapistLocations;
use App\Models\TherapistTherapy;
use App\Models\Therapy;
use App\Models\Disease;
use App\Models\PainPoint;
use App\Models\Treatment;
use App\Models\CustomerPainpoint;
use App\Models\CustomerDisease;
use App\Models\UpdateAvalibility;
use App\Models\TherapiestWork;
use App\Models\HomeBookingSlots;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TherapiestOrderController extends Controller
{

    public function openbooking1(Request $request){
        $user=$request->user;
        $order=[];
        $openbooking=TherapiestWork::with('therapieswork.therapiesorder.details.entity')->where('therapist_id', $user->id)->where('status','Pending')->get();
        if($openbooking) {
            foreach ($openbooking as $item) {
                $order[]=array(
                    'status'=>$item->status,
                    'display_time'=>$item->therapieswork->display_time,
                    'time'=>$item->therapieswork->time,
                    'created_at'=>$item->therapieswork->created_at,
                    'refid'=>$item->therapieswork->therapiesorder->refid,
                    'therapy_name'=>$item->therapieswork->therapiesorder->details[0]->entity->name,
		'image'=>$item->therapieswork->therapiesorder->details[0]->entity->image,
                    'id'=>$item->id
                );
            }
            return [
                'status' => 'success',
                'data' =>compact('order'),
            ];

        }
        return [
            'status'=>'failed',
            'message'=>'No Therapy Found'
        ];


    }

    public function openbooking(Request $request){
        $user=$request->user;
        $order=[];
        $openbooking=HomeBookingSlots::with(['therapy','timeslot', 'order'])
            ->where('assigned_therapist', $user->id)
            ->where('therapist_status','Pending')
            ->get();
        if($openbooking) {
            foreach ($openbooking as $item) {
                $order[]=array(
                    'status'=>$item->therapist_status,
                    'display_time'=>$item->timeslot->display_time??$item->time,
                    'time'=>$item->time,
                    'created_at'=>$item->created_at,
                    'refid'=>$item->order->refid,
                    'therapy_name'=>$item->therapy->name,
                    'image'=>$item->therapy->image,
                    'id'=>$item->id
                );
            }
            return [
                'status' => 'success',
                'data' =>compact('order'),
            ];

        }
        return [
            'status'=>'failed',
            'message'=>'No Therapy Found'
        ];


    }

    public function openbookingdetails1(Request $request,$id){
        $user=$request->user;
        $userlat=$request->lat;
        //$userlat="28.618528";
        $userlang=$request->lang;
       // $userlang="77.372627";

        $openbookingdetails=TherapiestWork::with('therapieswork.therapiesorder.details.entity')->find($id);
        //instant timing
        if($openbookingdetails) {
            if($openbookingdetails->therapieswork->therapiesorder->is_instant==0){
              $timing=  $openbookingdetails->therapieswork->therapiesorder->booking_date." ".$openbookingdetails->therapieswork->therapiesorder->booking_time;
            }else{
                $timing='Instant';
            }
           // distance calculate
            $lat=  $openbookingdetails->therapieswork->therapiesorder->lat;
            $lang= $openbookingdetails->therapieswork->therapiesorder->lang;
            $delta_lat = $lat - $userlat ;
            $delta_lon = $lang - $userlang ;

            $earth_radius = 6372.795477598;

            $alpha    = $delta_lat/2;
            $beta     = $delta_lon/2;
            $a        = sin(deg2rad($alpha)) * sin(deg2rad($alpha)) + cos(deg2rad($userlat)) * cos(deg2rad($lat)) * sin(deg2rad($beta)) * sin(deg2rad($beta)) ;
            $c        = asin(min(1, sqrt($a)));
            $distance = 2*$earth_radius * $c;
            $distance = round($distance, 4);
            //button show
            if($openbookingdetails->status=='Pending'){
                $buttonshow="start_journey";
            }elseif($openbookingdetails->status=='Confirmed'){
                $buttonshow="start_therapies";
            }else{
                $buttonshow="hide";
            }
            return [
                'status' => 'success',
                'booking_status'=>$openbookingdetails->status,
                'total_cost'=>$openbookingdetails->therapieswork->therapiesorder->total_cost,
                'schedule_type'=>$openbookingdetails->therapieswork->therapiesorder->schedule_type,
                'name'=>$openbookingdetails->therapieswork->therapiesorder->name,
                'mobile'=>$openbookingdetails->therapieswork->therapiesorder->mobile,
                'address'=>$openbookingdetails->therapieswork->therapiesorder->address,
                'distance_away'=>$distance,
                'timing'=>$timing,
                'buttonshow'=>$buttonshow,
                'therapy_name'=>$openbookingdetails->therapieswork->therapiesorder->details[0]->entity->name,
                'image'=>$openbookingdetails->therapieswork->therapiesorder->details[0]->entity->image,
                'id'=>$id
                /*'data' =>$openbookingdetails,*/
            ];

        }
        return [
            'status'=>'failed',
            'message'=>'No Therapy Found'
        ];


    }
    public function openbookingdetails(Request $request,$id){
        $user=$request->user;
        $userlat=$request->lat;
        //$userlat="28.618528";
        $userlang=$request->lang;
       // $userlang="77.372627";

        $openbookingdetails=HomeBookingSlots::with(['therapy','timeslot', 'order'])
            ->find($id);
        //instant timing
        if($openbookingdetails) {
            if($openbookingdetails->is_instant==0){
              $timing=  ($openbookingdetails->timeslot->date??$openbookingdetails->date)." ".($openbookingdetails->timeslot->start_time??$openbookingdetails->time);
            }else{
                $timing='Instant';
            }
           // distance calculate
            $lat=  $openbookingdetails->order->lat;
            $lang= $openbookingdetails->order->lang;
            $delta_lat = $lat - $userlat ;
            $delta_lon = $lang - $userlang ;

            $earth_radius = 6372.795477598;

            $alpha    = $delta_lat/2;
            $beta     = $delta_lon/2;
            $a        = sin(deg2rad($alpha)) * sin(deg2rad($alpha)) + cos(deg2rad($userlat)) * cos(deg2rad($lat)) * sin(deg2rad($beta)) * sin(deg2rad($beta)) ;
            $c        = asin(min(1, sqrt($a)));
            $distance = 2*$earth_radius * $c;
            $distance = round($distance, 4);
            //button show
            if($openbookingdetails->therapist_status=='Pending'){
                $buttonshow="start_journey";
            }elseif($openbookingdetails->therapist_status=='Confirmed'){
                $buttonshow="start_therapies";
            }else{
                $buttonshow="hide";
            }
            return [
                'status' => 'success',
                'booking_status'=>$openbookingdetails->therapist_status,
                'total_cost'=>$openbookingdetails->price,
                'schedule_type'=>$openbookingdetails->order->schedule_type,
                'name'=>$openbookingdetails->order->name,
                'mobile'=>$openbookingdetails->order->mobile,
                'address'=>$openbookingdetails->order->address,
                'distance_away'=>$distance,
                'timing'=>$timing,
                'buttonshow'=>$buttonshow,
                'therapy_name'=>$openbookingdetails->therapy->entity->name,
                'image'=>$openbookingdetails->therapy->image,
                'id'=>$id
                /*'data' =>$openbookingdetails,*/
            ];

        }
        return [
            'status'=>'failed',
            'message'=>'No Therapy Found'
        ];


    }
    public function journey_started(Request $request, $id){
        $user=$request->user;
        $updatejourney=HomeBookingSlots::find($id);
        if($updatejourney->therapist_status=='Pending'){
            $updatejourney->therapist_status='Confirmed';
            $updatejourney->save();
        }elseif($updatejourney->therapist_status=='Confirmed'){
            $updatejourney->therapist_status='Started';
            $updatejourney->start_time=date("Y-m-d H:i:s");
            $updatejourney->save();
        }
        if($updatejourney){

            return [
                'status'=>'success',
                'message' => 'status updated'
            ];
        }else {
            return [
                'status' => 'failed',
                'message' => 'No update Found'
            ];

        }
    }
    public function diseasepoint(Request $request){

        $disease=Disease::active()->get();
        $painpoint=PainPoint::active()->get();
        if($disease->count()>0 or $painpoint->count()>0){

            return [
                'status'=>'success',
                'data' => compact('painpoint', 'disease')
            ];
        }else {
            return [
                'status' => 'failed',
                'message' => 'No update Found'
            ];

        }
    }

    public function send_diesase_point(Request $request,$id){

        $request->validate([
            'painpoint_id'=>'required',
            'disease_id'=>'required',
        ]);

        $arrpainpoint_id = explode(",", $request->painpoint_id);
       foreach($arrpainpoint_id as $key=>$painpoint_id) {
           CustomerPainpoint::create([
               'therapiest_work_id' => $id,
               'pain_point_id' => $painpoint_id

           ]);

       }
        $arrdisease_id= explode(",", $request->disease_id);
        foreach($arrdisease_id as $key=>$disease_id) {
            CustomerDisease::create([
                'therapiest_work_id' => $id,
                'disease_id' => $disease_id
            ]);
        }
        $updatejourney=HomeBookingSlots::find($id);
        if($updatejourney->therapist_status=='Started'){
            $updatejourney->therapist_status='Diagnosed';
            $updatejourney->save();
        }
         if($updatejourney) {
             return [
                 'status' => 'success'
             ];
         }else{
             return [
                 'status' => 'failed'
             ];
         }
    }
    public function treatmentlist(Request $request){

        $treatment=Treatment::active()->get();
        if($treatment->count()>0 ){

            return [
                'status'=>'success',
                'data' => $treatment
            ];
        }else {
            return [
                'status' => 'failed',
                'message' => 'No update Found'
            ];

        }
    }

    public function treatmentsuggestation(Request $request,$id){

        $request->validate([
            'treatment_id'=>'required',
        ]);
        $updatejourney=HomeBookingSlots::find($id);
        if($updatejourney->therapist_status=='Diagnosed'){
            $updatejourney->therapist_status='TreatmentSelected';
            $updatejourney->treatment_id=$request->treatment_id;
            $updatejourney->save();
        }

        if($updatejourney) {
            return [
                'status' => 'success'
            ];
        }else{
            return [
                'status' => 'failed'
            ];
        }
    }

    public function pain_point_relif(Request $request,$id){

        $pain_point_relif=CustomerPainpoint::where('therapiest_work_id',$id)->with('painpoint')->get();
        if($pain_point_relif->count()>0 ){
            foreach ($pain_point_relif as $item) {
                $painpoint[] = array(
                    'id' => $item->painpoint->id,
                    'name' => $item->painpoint->name
                );
            }
            return [
                'status'=>'success',
               'data'=>$painpoint,
            ];
        }else {
            return [
                'status' => 'failed',
                'message' => 'No update Found'
            ];

        }
    }

    public function pain_relief_update_rating(Request $request,$id){

      $request->validate([
          'message'=>'required',
          'end_time'=>'required',
        //  'painpoint_id'=>'required',
      ]);

        $therapiestwork=HomeBookingSlots::find($id);
        if($therapiestwork->count()>0 ){
           $therapiestwork->message=$request->message;
           $therapiestwork->end_time=date('Y-m-d H:i:s');
           $therapiestwork->therapist_status='Completed';
           $therapiestwork->status='completed';
          $therapiestwork->save();
          // $arrdisease_id= explode(",", $request->painpoint_id);
          // $arrdisease_id= explode(",", $request->disease_id);
          //    foreach ($pain_point_relif as $item) {
          //        $painpoint[] = array(
          //           'id' => $item->painpoint->id,
          //            'name' => $item->painpoint->name
          //     );

            return [
                'status'=>'success',
            ];
        }else {
            return [
                'status' => 'failed',
                'message' => 'No update Found'
            ];

        }
    }

}
