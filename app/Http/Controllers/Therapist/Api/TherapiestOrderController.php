<?php

namespace App\Http\Controllers\Therapist\Api;

use App\Models\DiagnosePoint;
use App\Models\DiseasewiseTreatment;
use App\Models\MainDisease;
use App\Models\ReasonDisease;
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

//    public function openbooking1(Request $request){
//        $user=$request->user;
//        $order=[];
//        $openbooking=TherapiestWork::with('therapieswork.therapiesorder.details.entity')
//            ->where('therapist_id', $user->id)
//            ->where('status','Pending')
//            ->get();
//        if($openbooking) {
//            foreach ($openbooking as $item) {
//                $order[]=array(
//                    'status'=>$item->status,
//                    'display_time'=>$item->therapieswork->display_time,
//                    'time'=>$item->therapieswork->time,
//                    'created_at'=>$item->therapieswork->created_at,
//                    'refid'=>$item->therapieswork->therapiesorder->refid,
//                    'therapy_name'=>$item->therapieswork->therapiesorder->details[0]->entity->name,
//		'image'=>$item->therapieswork->therapiesorder->details[0]->entity->image,
//                    'id'=>$item->id
//                );
//            }
//            return [
//                'status' => 'success',
//                'data' =>compact('order'),
//            ];
//
//        }
//        return [
//            'status'=>'failed',
//            'message'=>'No Therapy Found'
//        ];
//
//
//    }

    public function openbooking(Request $request){
        $user=$request->user;
        $order=[];
        $openbooking=HomeBookingSlots::with(['therapy','timeslot', 'order'])
            ->where('assigned_therapist', $user->id)
            ->where('status','!=', 'completed')
            ->where('status','!=', 'cancelled')
            ->get();
        if($openbooking) {
            $i=0;
            foreach ($openbooking as $item) {
                if(in_array($item->therapist_status, ['Pending', 'Confirmed'])){
                    $open_screen='details';
                }else if($item->therapist_status=='Started'){
                    $open_screen='pain-disease';
                }else if($item->therapist_status=='Diagnosed'){
                    $open_screen='treatment-list';
                }else if($item->therapist_status=='TreatmentSelected'){
                    $open_screen='customer-feedback';
                }else{

                    $i++;
                    continue;
                    //$i++;
                }
                $order[]=array(
                    'status'=>$openbooking[$i]->therapist_status,
                    'display_time'=>$openbooking[$i]->timeslot->display_time??date('h:iA', strtotime($openbooking[$i]->time)),
                    'time'=>date('h:iA', strtotime($openbooking[$i]->time)),
                    'created_at'=>date('d/m/Y h:iA', strtotime($openbooking[$i]->created_at)),
                    'date'=>$openbooking[$i]->date,
                    'refid'=>$openbooking[$i]->order->refid??'',
                    'therapy_name'=>$openbooking[$i]->therapy->name??'',
                    'image'=>$openbooking[$i]->therapy->image??'',
                    'id'=>$openbooking[$i]->id,
                    'open_screen'=>$open_screen,
                    'lat'=>$openbooking[$i]->order->lat??'',
                    'lang'=>$openbooking[$i]->order->lang??''
                );

                $i++;
            }
            $user=$user->only('name', 'image');
            return [
                'status' => 'success',
                'data' =>compact('order','user'),
            ];

        }
        return [
            'status'=>'failed',
            'message'=>'No Therapy Found'
        ];


    }

    public function completedbookings(Request $request){
        $user=$request->user;
        $order=[];
        $openbooking=HomeBookingSlots::with(['therapy','timeslot', 'order'])
            ->where('assigned_therapist', $user->id)
            ->where('status', 'completed')
            ->where('status','!=', 'cancelled')
            ->get();
        if($openbooking) {
            foreach ($openbooking as $item) {

                $order[]=array(
                    'status'=>$item->therapist_status,
                    'display_time'=>$item->timeslot->display_time??date('h:iA', strtotime($item->time)),
                    'time'=>date('h:iA', strtotime($item->time)),
                    'created_at'=>date('d/m/Y h:iA', strtotime($item->created_at)),
                    'date'=>$item->date,
                    'refid'=>$item->order->refid??'',
                    'therapy_name'=>$item->therapy->name??'',
                    'image'=>$item->therapy->image??'',
                    'id'=>$item->id,
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


//    public function openbookingdetails1(Request $request,$id){
//        $user=$request->user;
//        $userlat=$request->lat;
//        //$userlat="28.618528";
//        $userlang=$request->lang;
//       // $userlang="77.372627";
//
//        $openbookingdetails=TherapiestWork::with('therapieswork.therapiesorder.details.entity')->find($id);
//        //instant timing
//        if($openbookingdetails) {
//            if($openbookingdetails->therapieswork->therapiesorder->is_instant==0){
//              $timing=  $openbookingdetails->therapieswork->therapiesorder->booking_date." ".$openbookingdetails->therapieswork->therapiesorder->booking_time;
//            }else{
//                $timing='Instant';
//            }
//           // distance calculate
//            $lat=  $openbookingdetails->therapieswork->therapiesorder->lat;
//            $lang= $openbookingdetails->therapieswork->therapiesorder->lang;
//            $delta_lat = $lat - $userlat ;
//            $delta_lon = $lang - $userlang ;
//
//            $earth_radius = 6372.795477598;
//
//            $alpha    = $delta_lat/2;
//            $beta     = $delta_lon/2;
//            $a        = sin(deg2rad($alpha)) * sin(deg2rad($alpha)) + cos(deg2rad($userlat)) * cos(deg2rad($lat)) * sin(deg2rad($beta)) * sin(deg2rad($beta)) ;
//            $c        = asin(min(1, sqrt($a)));
//            $distance = 2*$earth_radius * $c;
//            $distance = round($distance, 4);
//            //button show
//            if($openbookingdetails->status=='Pending'){
//                $buttonshow="start_journey";
//            }elseif($openbookingdetails->status=='Confirmed'){
//                $buttonshow="start_therapies";
//            }else{
//                $buttonshow="hide";
//            }
//            return [
//                'status' => 'success',
//                'booking_status'=>$openbookingdetails->status,
//                'total_cost'=>$openbookingdetails->therapieswork->therapiesorder->total_cost,
//                'schedule_type'=>$openbookingdetails->therapieswork->therapiesorder->schedule_type,
//                'name'=>$openbookingdetails->therapieswork->therapiesorder->name,
//                'mobile'=>$openbookingdetails->therapieswork->therapiesorder->mobile,
//                'address'=>$openbookingdetails->therapieswork->therapiesorder->address,
//                'distance_away'=>$distance,
//                'timing'=>$timing,
//                'buttonshow'=>$buttonshow,
//                'therapy_name'=>$openbookingdetails->therapieswork->therapiesorder->details[0]->entity->name,
//                'image'=>$openbookingdetails->therapieswork->therapiesorder->details[0]->entity->image,
//                'id'=>$id
//                /*'data' =>$openbookingdetails,*/
//            ];
//
//        }
//        return [
//            'status'=>'failed',
//            'message'=>'No Therapy Found'
//        ];
//
//
//    }
    public function openbookingdetails(Request $request,$id){
        $user=$request->user;
        $userlat=$request->lat;
        //$userlat="28.618528";
        $userlang=$request->lang;
       // $userlang="77.372627";

        $openbookingdetails=HomeBookingSlots::with(['therapy','timeslot', 'order'])
            ->where('status', '!=', 'completed')
            ->where('status','!=', 'cancelled')
            ->where('assigned_therapist', $user->id)
            ->find($id);

        if(!$openbookingdetails)
            return [
                'status'=>'failed',
                'message'=>'No Therapy Found'
            ];

        //instant timing
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
                'therapy_name'=>$openbookingdetails->therapy->name,
                'image'=>$openbookingdetails->therapy->image,
                'id'=>$id,
                'lat'=>$openbookingdetails->order->lat??'',
                'lang'=>$openbookingdetails->order->lang??'',
                /*'data' =>$openbookingdetails,*/
            ];
    }

    public function journey_started(Request $request, $id){
        $user=$request->user;
        $updatejourney=HomeBookingSlots::where('assigned_therapist', $user->id)
            ->where('status', '!=', 'completed')
            ->where('status','!=', 'cancelled')
            ->find($id);

        if(!$updatejourney)
            return [
                'status' => 'failed',
                'message' => 'No update Found'
            ];

        if($updatejourney->therapist_status=='Pending'){
            $updatejourney->therapist_status='Confirmed';
            $updatejourney->status='confirmed';
            $updatejourney->save();
        }elseif($updatejourney->therapist_status=='Confirmed'){
            if($updatejourney->verification_code!=$request->code){
                return [
                    'status'=>'failed',
                    'message'=>'Please enter correct verification code'
                ];
            }
            $updatejourney->therapist_status='Started';
            $updatejourney->start_time=date("Y-m-d H:i:s");
            $updatejourney->save();
        }

        return [
            'status'=>'success',
            'message' => 'Booking has been updated'
        ];

    }

    public function mainDiseaseList(Request $request, $id){

        $user=$request->user;

        $home_booking_slot=HomeBookingSlots::where('assigned_therapist', $user->id)
            ->with('mainDiseases')
            ->where('status', '!=', 'completed')
            ->where('status','!=', 'cancelled')
            ->find($id);

        $selected_diseases = $home_booking_slot->mainDiseases->map(function($element){
            return $element->id;
        });

        $main_diseases=MainDisease::active()
            //->with('reasons')
            ->select('name', 'id')->orderBY('name', 'asc')->get();
//        $reason_diseases=ReasonDisease::active()->select('name', 'id')->orderBY('name', 'asc')->get();

        foreach($main_diseases as $m){
            //$m->reason_disease=$reason_diseases;
            $m->is_selected=0;
            if(in_array($m->id, $selected_diseases->toArray()))
                $m->is_selected=1;
            $m->reason_disease=[];
        }


        //$customer_diseases=$home_booking_slot->reasonDiseases;
        $customer_diseases=[];

        $selected_diseases=[];

//        foreach($customer_diseases as $sds){
//            if(!isset($selected_diseases[$sds->pivot->disease_id]))
//                $selected_diseases[$sds->pivot->disease_id]=[];
//            $selected_diseases[$sds->pivot->disease_id][]=$sds->id;
//        }

        return [
            'status'=>'success',
            'data'=>compact('main_diseases')
        ];
    }


    public function addMainDiseases(Request $request, $id){

        $user=$request->user;
        //echo $user->id;die;
        $request->validate([
            'diseases'=>'array|required',
        ]);

        $home_booking_slot=HomeBookingSlots::where('assigned_therapist', $user->id)
            ->where('status', '!=', 'completed')
            ->where('status','!=', 'cancelled')
            ->find($id);

        //remove old data
        $home_booking_slot->mainDiseases()->detach();
        //$home_booking_slot->reasonDiseases()->detach();

        //add new data
        foreach($request->diseases as $disease=>$reason_diseases){
            $home_booking_slot->mainDiseases()->attach($disease);
//            if(!empty($reason_diseases)){
//                $reason_diseases=array_unique(explode(',',$reason_diseases));
//                if(!empty($reason_diseases))
//                    foreach($reason_diseases as $rid)
//                        $home_booking_slot->reasonDiseases()->attach([$rid=>['disease_id'=>$disease]]);
//            }
        }

        return [
            'status'=>'success',
            'message'=>'Diseases have been added'
        ];
    }

    public function diagnoseListBeforeTreatment(Request $request, $id){
        $user=$request->user;

        $home_booking_slot=HomeBookingSlots::where('assigned_therapist', $user->id)
            ->where('status', '!=', 'completed')
            ->where('status','!=', 'cancelled')
            ->findOrFail($id);

        $diagnose_points=DiagnosePoint::active()->select('id','name','type')->get();

        return [
            'status'=>'success',
            'data'=>compact('diagnose_points')
        ];

    }

    public function addDiagnoseBeforeTreatment(Request $request, $id){

        $user=$request->user;

        $request->validate([
            'before_treatment'=>'required|array',
            'after_treatment'=>'required|array'
        ]);

        $home_booking_slot=HomeBookingSlots::where('assigned_therapist', $user->id)
            ->where('status', '!=', 'completed')
            ->where('status','!=', 'cancelled')
            ->find($id);

        $home_booking_slot->diagnose()->detach();

        $diagnose_points=[];

        foreach($request->before_treatment as $dp=>$value){
            if(!isset($diagnose_points[$dp]))
                $diagnose_points[$dp]=[];
            $diagnose_points[$dp]['before_value']=$value;
        }

        foreach($request->after_treatment as $dp=>$value){
            if(!isset($diagnose_points[$dp]))
                $diagnose_points[$dp]=[];
            $diagnose_points[$dp]['after_value']=$value;
        }

        if(!empty($diagnose_points))
            $home_booking_slot->diagnose()->attach($diagnose_points);

        return [
            'status'=>'success',
            'message'=>'Diagnose has been updated'
        ];

    }

    public function suggestedTreatments(Request $request, $id){
        $user=$request->user;
        $treatment_list=[];
        $home_booking_slot=HomeBookingSlots::with(['mainDiseases', 'painpoints'])
            ->where('assigned_therapist', $user->id)
            ->where('status', '!=', 'completed')
            ->where('status','!=', 'cancelled')
            ->find($id);

        //main disease ids
        $mdids=[];
        foreach($home_booking_slot->mainDiseases as $md){
            $mdids[]=$md->id;
        }

        //pain points
        $ppids=[];
        foreach($home_booking_slot->painPoints as $pp){
            $ppids[]=$pp->id;
        }

        // all treatments for main diseases
        $disease_treatments=DiseasewiseTreatment::active()
            ->with(['mainDisease', 'painPoints'])
            ->whereIn('main_disease_id', $mdids)
            ->get();


        // selection of treatments on basis of reason_disease, pain_point, ignore_disease
        $disease_treatment_list=[];
        foreach($disease_treatments as $dt){

            // set main disease
            if(!isset($disease_treatment_list[$dt->main_disease_id]))
                $disease_treatment_list[$dt->main_disease_id]=[
                    'main_disease'=>$dt->mainDisease->name??'',
                    'recommended_days'=>$dt->mainDisease->recommended_days??'',
                    'treatments'=>[]
                ];


            $painpoints='';
            if(!$dt->painPoints->toArray()){
                $treatment_for_painpoint_selected=true;
            }else{
                $treatment_for_painpoint_selected=false;
                if(!$ppids)
                    $treatment_for_painpoint_selected=true;
                else{
                    foreach($dt->painPoints as $pp){
                        if(in_array($pp->id, $ppids)){
                            $painpoints=$painpoints.$pp->name.',';
                            $treatment_for_painpoint_selected=true;
                        }
                    }
                }
            }
            //var_dump($treatment_for_painpoint_selected);die;
            if($treatment_for_painpoint_selected)
                $disease_treatment_list[$dt->main_disease_id]['treatments'][]=[
                    'reason_disease'=>[],
                    'painpoint'=>$painpoints,
                    'treatment'=>$dt->only('id','title', 'description','precautions', 'exercise', 'diet')
                ];
        }

        foreach($disease_treatment_list as $key=>$val)
            $treatment_list[]=$val;

        return [

            'status'=>'success',
            'data'=>compact('treatment_list')

        ];
    }


    public function chooseTreatments(Request $request, $id){
        $user=$request->user;

        $request->validate([
            'treatments'=>'required|array',
            'treatments.*'=>'required|integer'
        ]);

        $home_booking_slot=HomeBookingSlots::where('assigned_therapist', $user->id)
            ->where('status', '!=', 'completed')
            ->where('status','!=', 'cancelled')
            ->find($id);

        $home_booking_slot->treatmentsGiven()->detach();

        $home_booking_slot->treatmentsGiven()->attach($request->treatments);

        return [
            'status'=>'success',
            'message'=>'Treatments Have Been Updated'
        ];

    }




//    public function diseasepoint(Request $request){
//
//        $disease=Disease::active()->get();
//        $painpoint=PainPoint::active()->get();
//        if($disease->count()>0 or $painpoint->count()>0){
//
//            return [
//                'status'=>'success',
//                'data' => compact('painpoint', 'disease')
//            ];
//        }else {
//            return [
//                'status' => 'failed',
//                'message' => 'No update Found'
//            ];
//
//        }
//    }
//
//    public function send_diesase_point(Request $request,$id){
//
//        $request->validate([
//            'painpoint_id'=>'required',
//            //'disease_id'=>'required',
//        ]);
//
//        $user=$request->user;
//
//        $updatejourney=HomeBookingSlots::where('status', '!=', 'completed')
//            ->where('status','!=', 'cancelled')
//            ->where('assigned_therapist', $user->id)
//            ->find($id);
//
//        if(!$updatejourney)
//            return [
//                'status' => 'failed',
//                'message' => 'No update Found'
//            ];
//
//        $arrpainpoint_id = explode(",", $request->painpoint_id);
//
//        CustomerPainpoint::where('therapiest_work_id', $id)->where('type', 'therapy')->delete();
//
//       foreach($arrpainpoint_id as $key=>$painpoint_id) {
//           CustomerPainpoint::create([
//               'therapiest_work_id' => $id,
//               'pain_point_id' => $painpoint_id,
//               'type'=>'therapy'
//           ]);
//
//       }
//
//        CustomerDisease::where('therapiest_work_id', $id)->where('type', 'therapy')->delete();
//
//       if(!empty($request->disease_id)){
//           $arrdisease_id= explode(",", $request->disease_id);
//
//
//
//           if(!empty($arrdisease_id)){
//               foreach($arrdisease_id as $disease_id) {
//                   if(is_numeric($disease_id)){
//                       CustomerDisease::create([
//                           'therapiest_work_id' => $id,
//                           'disease_id' => trim($disease_id),
//                           'type'=>'therapy'
//                       ]);
//                   }
//               }
//           }
//       }
//
//
//
//        //if($updatejourney->therapist_status=='Started'){
//        $updatejourney->therapist_status='Diagnosed';
//        $updatejourney->save();
//        //}
//
//         return [
//             'status' => 'success'
//         ];
//
//    }


//    public function treatmentlist(Request $request){
//
//        $treatment=Treatment::active()->get();
//        if($treatment->count()>0 ){
//
//            return [
//                'status'=>'success',
//                'data' => $treatment
//            ];
//        }else {
//            return [
//                'status' => 'failed',
//                'message' => 'No update Found'
//            ];
//
//        }
//    }

//    public function treatmentsuggestation(Request $request,$id){
//
//        $request->validate([
//            'treatment_id'=>'required',
//        ]);
//
//        $user=$request->user;
//
//        $updatejourney=HomeBookingSlots::where('assigned_therapist', $user->id)
//            ->where('status', '!=', 'completed')
//            ->where('status','!=', 'cancelled')
//            ->find($id);
//
//        if(!$updatejourney)
//            return [
//                'status' => 'failed',
//                'message' => 'No record Found'
//            ];
//
//
//        //if($updatejourney->therapist_status=='Diagnosed'){
//        $updatejourney->therapist_status='TreatmentSelected';
//        $updatejourney->treatment_id=$request->treatment_id;
//        $updatejourney->save();
//        //}
//
//
//        return [
//            'status' => 'success',
//            'message'=>'Treatment has been selected'
//        ];
//
//    }

    public function pain_point_relif(Request $request, $id){

        $user=$request->user;

        $session=HomeBookingSlots::where('assigned_therapist', $user->id)
            ->where('status', '!=', 'completed')
            ->where('status','!=', 'cancelled')
            ->find($id);

        if(!$session){
            return [
                'status' => 'failed',
                'message' => 'No Session Found'
            ];
        }

        $pain_point_relif=CustomerPainpoint::where('therapiest_work_id',$id)
            ->with('painpoint')
            ->get();

        if($pain_point_relif->count()>0 ){
            foreach ($pain_point_relif as $item) {
                $painpoint[] = array(
                    'id' => $item->painpoint->id??'',
                    'name' => $item->painpoint->name??''
                );
            }
            return [
                'status'=>'success',
               'data'=>$painpoint,
                'start_time'=>date('h:iA', strtotime($session->start_time)),
                'end_time'=>date('h:iA')
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
          //'end_time'=>'required',
            //'painpoints'=>'required|array',
            //'painpoints.*'=>'required|integer|min:1|max:5',
            'result'=>'required|integer|in:1,2,3,4'
        ]);

        $user=$request->user;

        $therapiestwork=HomeBookingSlots::with('order')
        ->where('assigned_therapist', $user->id)
            ->where('status', '!=', 'completed')
            ->where('status','!=', 'cancelled')
            ->find($id);

        if(!$therapiestwork)
            return [
                'status' => 'failed',
                'message' => 'No Record Found'
            ];

//        $painpoints=[];
//        foreach($request->painpoints as $key=>$val){
//            $painpoints[$key]=[
//                    'related_rating'=>$val,
//                    'type'=>'therapy'
//            ];
//        }
//
//        $therapiestwork->painpoints()->sync($painpoints);


       $therapiestwork->message=$request->message;
       $therapiestwork->end_time=date('Y-m-d H:i:s');
       $therapiestwork->therapist_status='Completed';
       $therapiestwork->status='completed';
       $therapiestwork->therapist_result=$request->result;
       $therapiestwork->save();

        $count=HomeBookingSlots::whereIn('status', ['pending', 'confirmed'])
            ->where('order_id', $therapiestwork->order_id)
            ->count();
        if($count==0){
            $therapiestwork->order->status='completed';
            $therapiestwork->order->save();
        }

        return [
            'status'=>'success',
            'id'=>$therapiestwork->id
        ];

    }

    public function completedbookingdetails(Request $request, $id){

        $user=$request->user;

        $openbookingdetails=HomeBookingSlots::with(['therapy','timeslot', 'order', 'diseases', 'painpoints', 'treatment'])
            ->where('status',  'completed')
            ->where('assigned_therapist', $user->id)
            ->find($id);

        if(!$openbookingdetails)
            return [
                'status'=>'failed',
                'message'=>'No Therapy Found'
            ];

        //instant timing
        if($openbookingdetails->is_instant==0){
            $timing=  ($openbookingdetails->timeslot->date??$openbookingdetails->date)." ".($openbookingdetails->timeslot->start_time??$openbookingdetails->time);
        }else{
            $timing=$openbookingdetails->date.' '.'Instant Booking';
        }
        // distance calculate

        return [
            'status' => 'success',
            'booking_status'=>$openbookingdetails->therapist_status,
            'total_cost'=>$openbookingdetails->price,
            //'schedule_type'=>$openbookingdetails->order->schedule_type,
            'name'=>$openbookingdetails->order->name,
            'mobile'=>$openbookingdetails->order->mobile,
            'address'=>$openbookingdetails->order->address,
            //'distance_away'=>$distance,
            'timing'=>$timing,
            'therapy_name'=>$openbookingdetails->therapy->name,
            'image'=>$openbookingdetails->therapy->image,
            'id'=>$id,
            'comments'=>$openbookingdetails->message??'',
            'diseases'=>$openbookingdetails->diseases,
            'painpoints'=>$openbookingdetails->painpoints,
            'treatment'=>$openbookingdetails->treatment,
            'show_feedback_button'=>empty($openbookingdetails->feedback_from_therapist)?1:0,
            'feedback_from_therapist'=>$openbookingdetails->feedback_from_therapist??''
            /*'data' =>$openbookingdetails,*/
        ];

    }


    public function completedbookingdetails2(Request $request, $id){

        $user=$request->user;

        $openbookingdetails=HomeBookingSlots::with(['therapy','timeslot', 'order', 'diseases', 'painpoints', 'mainDiseases', 'treatmentsGiven'])
            ->where('status',  'completed')
            ->where('status','!=', 'cancelled')
            ->where('assigned_therapist', $user->id)
            ->find($id);

        $painpoints = $openbookingdetails->painPoints->map(function($elem){
            return $elem->name;
        })->toarray();

        if(!$openbookingdetails)
            return [
                'status'=>'failed',
                'message'=>'No Therapy Found'
            ];

        //instant timing
        if($openbookingdetails->is_instant==0){
            $timing=  ($openbookingdetails->timeslot->date??$openbookingdetails->date)." ".($openbookingdetails->timeslot->start_time??$openbookingdetails->time);
        }else{
            $timing=$openbookingdetails->date.' '.'Instant Booking';
        }

        $main_diseases=[];
        foreach($openbookingdetails->mainDiseases as $md){
            $main_diseases[$md->id]=[
                'name'=>$md->name,
                'reason_diseases'=>''
            ];
        }

//        foreach($openbookingdetails->reasonDiseases as $rd){
//            if(isset($main_diseases[$rd->pivot->disease_id])) {
//                $main_diseases[$rd->pivot->disease_id]['reason_diseases']=$main_diseases[$rd->pivot->disease_id]['reason_diseases'].$rd->name.', ';
//            }
//        }

        $main_diseases1=[];
        foreach($main_diseases as $d){
            $main_diseases1[]=$d;
        }


        $treatments=[];
        foreach($openbookingdetails->treatmentsGiven as $t){
            $treatments[]=['name'=>$t->description];
        }

//        $diagnose=[];
//        foreach($openbookingdetails->diagnose as $dg){
//            $diagnose[]=[
//                'name'=>$dg->name,
//                'before'=>$dg->pivot->before_value??'',
//                'after'=>$dg->pivot->after_value??''
//                ];
//        }

        return [
            'status' => 'success',
            'booking_status'=>$openbookingdetails->therapist_status,
            'total_cost'=>$openbookingdetails->price,
            //'schedule_type'=>$openbookingdetails->order->schedule_type,
            'name'=>$openbookingdetails->order->name,
            'mobile'=>$openbookingdetails->order->mobile,
            'address'=>$openbookingdetails->order->address,
            //'distance_away'=>$distance,
            'timing'=>$timing,
            'therapy_name'=>$openbookingdetails->therapy->name,
            'image'=>$openbookingdetails->therapy->image,
            'id'=>$id,
            'comments'=>$openbookingdetails->message??'',
            'diseases'=>$openbookingdetails->diseases,
            'painpoints'=>implode(', ', $painpoints),
            'treatment'=>$treatments,
            'show_feedback_button'=>empty($openbookingdetails->feedback_from_therapist)?1:0,
            'feedback_from_therapist'=>$openbookingdetails->feedback_from_therapist??'',
            'main_diseases'=>$main_diseases1,
            //'diagnose'=>$diagnose,
            'therapy_result'=>$openbookingdetails->results()
            /*'data' =>$openbookingdetails,*/
        ];

    }


    public function postCustomerReview(Request $request, $id){

        $request->validate([
            'feedback'=>'required'
        ]);

        $user=$request->user;
        $session=HomeBookingSlots::where('assigned_therapist', $user->id)
            ->whereNull('feedback_from_therapist')
            ->find($id);
        if(!$session)
            return [
                'status'=>'failed',
                'message'=>'No Session Found'
            ];

        $session->feedback_from_therapist=$request->feedback;
        $session->save();

        return [
            'status'=>'success',
            'message'=>'Feedback Has Been Submitted'
        ];



    }


    public function painPointList(Request $request, $id){
        $user=$request->user;

        $home_booking_slot=HomeBookingSlots::where('assigned_therapist', $user->id)
            ->with('painPoints')
            ->where('status', '!=', 'completed')
            ->where('status','!=', 'cancelled')
            ->find($id);

        $selected_points = $home_booking_slot->painPoints->map(function($element){
            return $element->id;
        });

        $pain_points=PainPoint::active()
            //->with('reasons')
            ->select('name', 'id')->orderBY('name', 'asc')->get();
//        $reason_diseases=ReasonDisease::active()->select('name', 'id')->orderBY('name', 'asc')->get();

        foreach($pain_points as $m){
            //$m->reason_disease=$reason_diseases;
            $m->is_selected=0;
            if(in_array($m->id, $selected_points->toArray()))
                $m->is_selected=1;
            //$m->reason_disease=[];
        }


        //$customer_diseases=$home_booking_slot->reasonDiseases;
        $customer_diseases=[];

        $selected_diseases=[];

//        foreach($customer_diseases as $sds){
//            if(!isset($selected_diseases[$sds->pivot->disease_id]))
//                $selected_diseases[$sds->pivot->disease_id]=[];
//            $selected_diseases[$sds->pivot->disease_id][]=$sds->id;
//        }

        return [
            'status'=>'success',
            'data'=>compact('pain_points')
        ];
    }

    public function updatePainPointList(Request $request, $id){

        $user=$request->user;
        if(is_array($request->pain_points)){

            $home_booking_slot=HomeBookingSlots::where('assigned_therapist', $user->id)
                ->where('status', '!=', 'completed')
                ->where('status','!=', 'cancelled')
                ->find($id);

            //remove old data
            $home_booking_slot->painPoints()->detach();
            //$home_booking_slot->reasonDiseases()->detach();

            //add new data
            foreach($request->pain_points as $disease){
                $home_booking_slot->painPoints()->attach($disease);
//            if(!empty($reason_diseases)){
//                $reason_diseases=array_unique(explode(',',$reason_diseases));
//                if(!empty($reason_diseases))
//                    foreach($reason_diseases as $rid)
//                        $home_booking_slot->reasonDiseases()->attach([$rid=>['disease_id'=>$disease]]);
//            }
            }

        }


        return [
            'status'=>'success',
            'message'=>'Pain Points have been added'
        ];
    }


    public function otherDiseasesList(Request $request, $id){
        $user=$request->user;

        $home_booking_slot=HomeBookingSlots::where('assigned_therapist', $user->id)
            ->with('diseases')
            ->where('status', '!=', 'completed')
            ->where('status','!=', 'cancelled')
            ->find($id);

        $selected_points = $home_booking_slot->diseases->map(function($element){
            return $element->id;
        });

        $diseases=Disease::active()
            //->with('reasons')
            ->select('name', 'id')->orderBY('name', 'asc')->get();
//        $reason_diseases=ReasonDisease::active()->select('name', 'id')->orderBY('name', 'asc')->get();

        foreach($diseases as $m){
            //$m->reason_disease=$reason_diseases;
            $m->is_selected=0;
            if(in_array($m->id, $selected_points->toArray()))
                $m->is_selected=1;
            //$m->reason_disease=[];
        }


        //$customer_diseases=$home_booking_slot->reasonDiseases;
        $customer_diseases=[];

        $selected_diseases=[];

//        foreach($customer_diseases as $sds){
//            if(!isset($selected_diseases[$sds->pivot->disease_id]))
//                $selected_diseases[$sds->pivot->disease_id]=[];
//            $selected_diseases[$sds->pivot->disease_id][]=$sds->id;
//        }

        return [
            'status'=>'success',
            'data'=>compact('diseases')
        ];
    }


    public function updateOtherDiseases(Request $request, $id){
        $user=$request->user;
        if(is_array($request->diseases)){

            $home_booking_slot=HomeBookingSlots::where('assigned_therapist', $user->id)
                ->where('status', '!=', 'completed')
                ->where('status','!=', 'cancelled')
                ->find($id);

            //remove old data
            $home_booking_slot->diseases()->detach();
            //$home_booking_slot->reasonDiseases()->detach();

            //add new data
            foreach($request->diseases as $disease){
                $home_booking_slot->diseases()->attach($disease);
//            if(!empty($reason_diseases)){
//                $reason_diseases=array_unique(explode(',',$reason_diseases));
//                if(!empty($reason_diseases))
//                    foreach($reason_diseases as $rid)
//                        $home_booking_slot->reasonDiseases()->attach([$rid=>['disease_id'=>$disease]]);
//            }
            }

        }

        return [
            'status'=>'success',
            'message'=>'Diseases have been added'
        ];
    }


    public function getPatientDetails(Request $request, $id){
        $user=$request->user;

        $home_booking_slot=HomeBookingSlots::where('assigned_therapist', $user->id)
            ->with('diseases')
            ->where('status', '!=', 'completed')
            ->where('status','!=', 'cancelled')
            ->find($id);

        if(!$home_booking_slot)
            return [
                'status'=>'failed',
                'message'=>'No Therapy Found'
            ];

        $completed_slot=HomeBookingSlots::where('assigned_therapist', $user->id)
            ->with('diseases')
            ->where('status', 'completed')
            ->where('order_id', $home_booking_slot->order_id)
            ->orderBy('id', 'desc')
            ->first();

        $data = [
            'age'=>$home_booking_slot->age??($completed_slot->age??''),
            'gender'=>$home_booking_slot->gender??($completed_slot->gender??''),
            'occupation'=>$home_booking_slot->occupation??($completed_slot->occupation??''),
            'height'=>$home_booking_slot->height??($completed_slot->height??''),
            'weight'=>$home_booking_slot->weight??($completed_slot->weight??''),
            'pulse'=>$home_booking_slot->pulse??($completed_slot->pulse??''),
            'temperature'=>$home_booking_slot->temperature??($completed_slot->temperature??''),
            'sugar'=>$home_booking_slot->sugar??($completed_slot->sugar??''),
            'blood_pressure'=>$home_booking_slot->blood_pressure??($completed_slot->blood_pressure??''),
            'blood_group'=>$home_booking_slot->blood_group??($completed_slot->blood_group??''),
        ];

        return [
            'status'=>'success',
            'data'=>$data
        ];

    }


    public function updatePatientDetails(Request $request, $id){
        $user=$request->user;

        $home_booking_slot=HomeBookingSlots::where('assigned_therapist', $user->id)
            ->with('diseases')
            ->where('status', '!=', 'completed')
            ->where('status','!=', 'cancelled')
            ->find($id);

        if(!$home_booking_slot)
            return [
                'status'=>'failed',
                'message'=>'No Therapy Found'
            ];

        $home_booking_slot->update($request->only('age',
            'gender',
            'occupation',
            'height',
            'weight',
            'pulse',
            'temperature',
            'sugar',
            'blood_pressure',
            'blood_group'));

        return [
            'status'=>'success',
            'message'=>'Details have been submitted'
        ];
    }

}
