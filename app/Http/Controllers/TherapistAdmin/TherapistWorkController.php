<?php

namespace App\Http\Controllers\TherapistAdmin;

use App\Models\BookingSlot;
use App\Models\CustomerDisease;
use App\Models\CustomerPainpoint;
use App\Models\DiagnosePoint;
use App\Models\Disease;
use App\Models\DiseasewiseTreatment;
use App\Models\HomeBookingSlots;
use App\Models\MainDisease;
use App\Models\PainPoint;
use App\Models\ReasonDisease;
use App\Models\TherapiestWork;
use App\Models\Treatment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TherapistWorkController extends Controller
{
    public function index(Request $request){
        $user=auth()->user();
        $sessions=BookingSlot::with([ 'therapy', 'timeslot', 'order'])
            ->where('assigned_therapist', $user->id)
            ->orderBy('id', 'desc')
            ->whereIn('status', ['pending','confirmed'])
            ->where('status','!=', 'cancelled')
            ->paginate(10);
        return view('therapistadmin.therapistwork.view',compact('sessions'));
    }

    public function past(Request $request){
        $user=auth()->user();
        $sessions=BookingSlot::with([ 'therapy', 'timeslot', 'order'])
            ->where('assigned_therapist', $user->id)
            ->orderBy('id', 'desc')
            ->where('status', 'completed')
            ->where('status','!=', 'cancelled')
            ->paginate(10);
        return view('therapistadmin.therapistwork.view',compact('sessions'));
    }

    public function details(Request $request,$id){

        $user=auth()->user();
        $treatment_list=[];

        $openbooking =BookingSlot::with(['clinic','assignedTo', 'review', 'therapy', 'diseases', 'painpoints', 'timeslot', 'order', 'mainDiseases', 'reasonDiseases','diagnose', 'treatmentsGiven'])

            ->where('assigned_therapist', $user->id)
            ->find($id);

        if(!$openbooking)
            return redirect()->back()->with('error', 'Your are not assigned to this session');

        $main_diseases=MainDisease::active()->get();
        $reason_diseases=ReasonDisease::active()->get();
        $ignore_diseases=Disease::active()->get();
        $all_pain_points = PainPoint::active()->get();
        $dianose_points=DiagnosePoint::active()->get();


        //get suggested treatments

        //main disease ids

        if(count($openbooking->mainDiseases->toArray())){
            $mdids=[];
            foreach($openbooking->mainDiseases as $md){
                $mdids[]=$md->id;
            }

            //reason_disease_ids, indexed by main disease
            $rdids=[];
            foreach($openbooking->reasonDiseases as $rd){
                if(!isset($rdids[$rd->pivot->disease_id]))
                    $rdids[$rd->pivot->disease_id]=[];
                $rdids[$rd->pivot->disease_id][]=$rd->id;
            }

            //pain points
            $ppids=[];
            foreach($openbooking->painPoints as $pp){
                $ppids[]=$pp->id;
            }

            //diseases to ignore treatments
            $igids=[];
            foreach($openbooking->diseases as $igd){
                $igids[]=$igd->id;
            }

            // all treatments for main diseases
            $disease_treatments=DiseasewiseTreatment::active()
                ->with(['mainDisease', 'reasonDiseases', 'painPoints', 'ignoreWhenDiseases'])
                ->whereIn('main_disease_id', $mdids)
                ->get();


            // selection of treatments on basis of reason_disease, pain_point, ignore_disease
            $disease_treatment_list=[];
            foreach($disease_treatments as $dt){

                //skip treatment if ignore disease found
                $flag=false;
                foreach($dt->ignoreWhenDiseases as $iwd){
                    if(in_array($iwd->id, $igids))
                        $flag=true;
                }
                if($flag)
                    continue;
                //skip treatment ends

                // set main disease
                if(!isset($disease_treatment_list[$dt->main_disease_id]))
                    $disease_treatment_list[$dt->main_disease_id]=[
                        'main_disease'=>$dt->mainDisease->name??'',
                        'treatments'=>[]
                    ];

                // set treatment after filtering by reason disease & painpoints
                $reasondiseases='';
                if(!$dt->reasonDiseases->toArray()){
                    $treatment_for_reason_selected=true;
                }else{
                    $treatment_for_reason_selected=false;
                    if(!$rdids[$dt->main_disease_id])
                        $treatment_for_reason_selected=true;
                    else {
                        foreach ($dt->reasonDiseases as $rd) {
                            if (in_array($rd->id, $rdids[$dt->main_disease_id])) {
                                $reasondiseases = $reasondiseases . $rd->name . ',';
                                $treatment_for_reason_selected = true;
                            }

                        }
                    }
                }

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
                if($treatment_for_painpoint_selected && $treatment_for_reason_selected)
                    $disease_treatment_list[$dt->main_disease_id]['treatments'][]=[
                        'reason_disease'=>$reasondiseases,
                        'painpoint'=>$painpoints,
                        'treatment'=>$dt->only('id','description','exercise', 'dont_exercise', 'diet', 'recommended_days', 'action_when_pain_increase')
                    ];




            }
            foreach($disease_treatment_list as $key=>$val)
                $treatment_list[]=$val;
        }


        // suggested treatment ends


        //$treatments=Treatment::active()->get();

        $selected_pain_points=CustomerPainpoint::where('therapiest_work_id', $id)
            ->get();
        $selected_diseases=CustomerDisease::where('therapiest_work_id', $id)
            ->get();

//        echo '<pre>';
//        print_r($treatment_list);die;

        return view('therapistadmin.therapistwork.details',[
            'openbooking'=>$openbooking,
            'painpoints'=>$all_pain_points,
            'diseases'=>$ignore_diseases,
            'selected_pain_points'=>$selected_pain_points,
            'selected_diseases'=>$selected_diseases,
            'treatments'=>$treatment_list,
            'main_diseases'=>$main_diseases,
            'reason_diseases'=>$reason_diseases,
            'diagnose_points'=>$dianose_points
            //'suggested_treatments'=>$treatment_list,
        ]);
    }


    public function updateDiagnose(Request $request, $id){
       $request->validate([
           'main_diseases'=>'required|array',
           'main_diseases.*'=>'integer',
           'pain_points'=>'required|array',
           'pain_points.*'=>'integer',
           'ignore_diseases'=>'array',
           'ignore_diseases.*'=>'integer'
       ]);

       //echo '<pre>';
       //print_r($request->all());die;

       $user=auth()->user();

        $session=BookingSlot::where('assigned_therapist', $user->id)
            //->where('status', '!=', 'completed')
            ->where('status','!=', 'cancelled')
            ->findOrFail($id);
        if($session->status=='completed')
            return redirect()->back()->with('error', 'Completed Therapy Cannot Be updated');

        $session->mainDiseases()->detach();
        $session->reasonDiseases()->detach();

       $mids=[];
       foreach($request->main_diseases as $md)
           if(!in_array($md, $mids))
               $mids[]=$md;

        $rids=[];
        foreach($request->reason_diseases as $md=>$rd){
            if(!in_array($md, $mids))
                $mids[]=$md;
            foreach($rd as $r){
                $session->reasonDiseases()->attach([$r=>['disease_id'=>$md]]);
            }
        }

        if($mids)
            $session->mainDiseases()->attach($mids);

       CustomerPainpoint::where('therapiest_work_id', $id)->where('type', 'clinic')->delete();

       foreach($request->pain_points as $point){
           CustomerPainpoint::create([
               'therapiest_work_id'=>$session->id,
               'pain_point_id'=>$point,
               'type'=>'clinic'
           ]);
       }

        CustomerDisease::where('therapiest_work_id', $id)->where('type', 'clinic')->delete();
        if(!empty($request->ignore_diseases)){
            foreach($request->ignore_diseases as $igd){
                CustomerDisease::create([
                    'therapiest_work_id'=>$session->id,
                    'disease_id'=>$igd,
                    'type'=>'clinic'
                ]);
            }
        }

        $session->status='confirmed';
        $session->save();

       return redirect()->back()->with('success', 'Customer Has Been Diagnosed. Please select treatment and start therapy');

    }

    public function startTherapy(Request $request, $id){

        $request->validate([
            'treatments'=>'required|array',
            'treatments.*'=>'required|integer',
        ]);

        $user=auth()->user();

        $session=BookingSlot::where('assigned_therapist', $user->id)
            //->where('status', '!=', 'completed')
            ->where('status','!=', 'cancelled')
            ->findOrFail($id);

        if($session->status=='completed')
            return redirect()->back()->with('error', 'Completed Therapy Cannot Be updated');

        $session->treatmentsGiven()->detach();

        $session->treatmentsGiven()->attach($request->treatments);
        $session->start_time=date('Y-m-d H:i:s');
        $session->save();


        return redirect()->back()->with('success', 'Treatment has been selected. Please start therapy');

    }

    public function addCustomerDiagnose(Request $request, $id){

        $request->validate([
            'before_treatment'=>'required|array',
            'after_treatment'=>'required|array'
        ]);
        //echo '<pre>';
        //print_r($request->all());die;
        $user=auth()->user();

        $session=BookingSlot::where('assigned_therapist', $user->id)
            //->where('status', '!=', 'completed')
            ->where('status','!=', 'cancelled')
            ->findOrFail($id);

        if($session->status=='completed')
            return redirect()->back()->with('error', 'Completed Therapy Cannot Be updated');

        $diagnose=[];
        foreach($request->before_treatment as $key=>$val){
            if(!isset($diagnose[$key]))
                $diagnose[$key]=[];

            $diagnose[$key]['before_value']=$val;
        }
        foreach($request->after_treatment as $key=>$val){
            if(!isset($diagnose[$key]))
                $diagnose[$key]=[];

            $diagnose[$key]['after_value']=$val;
        }

        $session->diagnose()->detach();

        $session->diagnose()->attach($diagnose);

        return redirect()->back()->with('success', 'Patient Diagnose Has Been Updated');


    }

    public function completeTherapy(Request $request, $id){
        $request->validate([
            'comments'=>'required',
            //'rating'=>'required|array',
            //'rating.*'=>'required|integer|min:1|max:5',
            'result'=>'required|in:1,2,3,4'
        ]);

        $user=auth()->user();

        $session=BookingSlot::with(['order'])
            ->where('assigned_therapist', $user->id)
            //->where('status', '!=', 'completed')
            ->where('status','!=', 'cancelled')
            ->findOrFail($id);
        if($session->status=='completed')
            return redirect()->back()->with('error', 'Completed Therapy Cannot Be updated');


//        foreach($request->rating as $key=>$value){
//            CustomerPainpoint::updateOrCreate([
//                'therapiest_work_id'=>$id,
//                'pain_point_id'=>$key
//            ],['related_rating'=>$value]);
//        }

        $session->end_time=date('Y-m-d H:i:s');
        $session->message=$request->comments;
        $session->therapist_result=$request->result;
        $session->status='completed';
        $session->save();

        $count=BookingSlot::where('status', ['pending', 'confirmed'])
            ->where('order_id', $session->order_id)
            ->count();
        if($count==0){
            $session->order->status='cancelled';
            $session->order->save();
        }

        return redirect()->back()->with('success', 'Therapy Session Has Been Completed');

    }
}
