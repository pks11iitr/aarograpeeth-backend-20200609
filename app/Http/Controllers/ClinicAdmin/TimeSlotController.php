<?php

namespace App\Http\Controllers\ClinicAdmin;

use App\Models\Clinic;
use App\Models\TimeSlot;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TimeSlotController extends Controller
{
    public function index(){

        $user=auth()->user();
        $clinic =Clinic::where('user_id',$user->id)
            ->firstOrFail();

        $timeslots = TimeSlot::where('clinic_id', $clinic->id)
            ->orderBy('id', 'desc')
            ->paginate(20);

        return view('clinicadmin.timeslots.index', compact('timeslots'));
    }

    public function deactivate(Request $request, $id){
        $user=auth()->user();
        $clinic =Clinic::where('user_id',$user->id)
            ->firstOrFail();
        $timeslot = TimeSlot::where('clinic_id', $clinic->id)->findOrFail($id);

        $timeslot->isactive=$request->status==1?0:1;
        $timeslot->save();

        return redirect()->back()->with('success','Status has been updated');
    }

    public function addForm(Request $request){
        $user=auth()->user();
        $clinic =Clinic::where('user_id',$user->id)
            ->firstOrFail();
        return view('clinicadmin.timeslots.add', compact('clinic'));
    }


    public function store(Request $request){
        $request->validate([
            'date'=>'required|array',
            'time'=>'required|array',
            'duration'=>'required|array',
            'grade_1'=>'required|array',
            'grade_2'=>'required|array',
            'grade_3'=>'required|array',
            'grade_4'=>'required|array',
        ]);

        $user=auth()->user();
        $clinic =Clinic::where('user_id',$user->id)
            ->firstOrFail();

        foreach($request->date as $key=>$date){

            if(!empty($date) && !empty($request->time[$key]) && !empty($request->duration[$key]) && !empty($request->grade_1[$key]) && !empty($request->grade_2[$key]) && !empty($request->grade_3[$key]) && !empty($request->grade_4[$key]) )
            TimeSlot::create([
                'clinic_id'=>$clinic->id,
                'date'=>$date,
                'start_time'=>date('h:i A', strtotime($request->time[$key].':00')),
                'internal_start_time'=>$request->time[$key].':00',
                'duration'=>$request->duration[$key],
                'grade_1'=>$request->grade_1[$key],
                'grade_2'=>$request->grade_2[$key],
                'grade_3'=>$request->grade_3[$key],
                'grade_4'=>$request->grade_4[$key],
                'isactive'=>1

            ]);
        }

        return redirect()->back()->with('success', 'Timeslot has been added');
    }


    public function repeat(){

    }
}
