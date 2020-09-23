<?php

namespace App\Http\Controllers\TherapistAdmin;

use App\Models\BookingSlot;
use App\Models\CustomerDisease;
use App\Models\CustomerPainpoint;
use App\Models\Disease;
use App\Models\PainPoint;
use App\Models\TherapiestWork;
use App\Models\Treatment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TherapistWorkController extends Controller
{
    public function index(Request $request){
        $user=auth()->user();
        $sessions=BookingSlot::with([ 'therapy', 'timeslot', 'order'])->where('assigned_therapist', $user->id)
            ->orderBy('id', 'desc')
            ->where('status', 'pending')
            ->paginate(10);
        return view('therapistadmin.therapistwork.view',compact('sessions'));
    }

    public function past(Request $request){
        $user=auth()->user();
        $sessions=BookingSlot::with([ 'therapy', 'timeslot', 'order'])->where('assigned_therapist', $user->id)
            ->orderBy('id', 'desc')
            ->where('status', '!=', 'pending')
            ->paginate(10);
        return view('therapistadmin.therapistwork.view',compact('sessions'));
    }

    public function details(Request $request,$id){

        $user=auth()->user();

        $session = BookingSlot::where('assigned_therapist', $user->id)
            ->firstOrFail();

        $openbooking =BookingSlot::with(['clinic','assignedTo', 'review', 'therapy', 'diseases', 'painpoints','treatment', 'timeslot', 'order'])
            ->find($id);

        $painpoints = PainPoint::active()->get();

        $treatments=Treatment::active()->get();

        $selected_pain_points=CustomerPainpoint::where('therapiest_work_id', $id)->get();
        $selected_diseases=CustomerDisease::where('therapiest_work_id', $id)->get();
        $diseases =Disease::active()->get();
        return view('therapistadmin.therapistwork.details',['openbooking'=>$openbooking,'painpoints'=>$painpoints,'diseases'=>$diseases, 'selected_pain_points'=>$selected_pain_points, 'selected_diseases'=>$selected_diseases, 'treatments'=>$treatments]);
    }


    public function updateDiagnose(Request $request, $id){
       $request->validate([
           'pain_point_ids'=>'required|array',
           'pain_point_ids.*'=>'integer',
           'disease_ids'=>'required|array',
           'disease_ids.*'=>'integer'
       ]);

       $user=auth()->user();

       $session=BookingSlot::where('assigned_therapist', $user->id)
           ->where('status', '!=', 'completed')
           ->findOrFail($id);

        CustomerPainpoint::where('therapiest_work_id', $id)->delete();

       foreach($request->pain_point_ids as $point){
           CustomerPainpoint::create([
               'therapiest_work_id'=>$session->id,
               'pain_point_id'=>$point
           ]);
       }


        CustomerDisease::where('therapiest_work_id', $id)->delete();

        foreach($request->disease_ids as $point){
            CustomerDisease::create([
                'therapiest_work_id'=>$session->id,
                'disease_id'=>$point
            ]);
        }


       return redirect()->back()->with('success', 'Customer Has Been Diagnosed. Please select treatment and start therapy');

    }

    public function startTherapy(Request $request, $id){

        $request->validate([
            'treatment_id'=>'required|integer',
        ]);

        $user=auth()->user();

        $session=BookingSlot::where('assigned_therapist', $user->id)
            ->where('status', '!=', 'completed')
            ->findOrFail($id);

        $session->treatment_id=$request->treatment_id;
        $session->save();

        return redirect()->back()->with('success', 'Treatment has been selected. Please start therapy');

    }

    public function updateFeedback(Request $request, $id){

    }
}
