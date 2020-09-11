<?php

namespace App\Http\Controllers\TherapistAdmin;

use App\Models\CustomerDisease;
use App\Models\CustomerPainpoint;
use App\Models\Disease;
use App\Models\PainPoint;
use App\Models\TherapiestWork;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TherapistWorkController extends Controller
{
    public function index(Request $request){
        $openbookings =TherapiestWork::with('therapieswork.therapiesorder.details.entity')->where('status', '!=','Pending')->get();
        return view('therapistadmin.therapistwork.view',['openbookings'=>$openbookings]);
    }

    public function past(Request $request){
        $pastbookings =TherapiestWork::with('therapieswork.therapiesorder.details.entity')->where('status', 'Confirmed')->get();

        return view('therapistadmin.therapistwork.past',['pastbookings'=>$pastbookings]);
    }

    public function details(Request $request,$id){
        $openbooking =TherapiestWork::with('therapieswork.therapiesorder.details.entity')->where('status', '!=','Pending')->find($id);
        $painpoints = PainPoint::active()->get();
        $diseases =Disease::active()->get();
        return view('therapistadmin.therapistwork.details',['openbooking'=>$openbooking,'painpoints'=>$painpoints,'diseases'=>$diseases]);
    }

    public function detailstore(Request $request,$id){
        $request->validate([
            'pain_point_id'=>'required',
            'disease_id'=>'required'
        ]);

        if($painpoint=CustomerPainpoint::create([
            'therapiest_work_id'=>$id,
            'pain_point_id'=>$request->pain_point_id,
        ]))
            if($disease=CustomerDisease::create([
                'therapiest_work_id'=>$id,
                'disease_id'=>$request->disease_id,
            ]))

            //var_dump($disease);die();
        {
            return redirect()->with('success', 'detailstore has been added');
        }
        return redirect()->back()->with('error', 'detailstore create failed');

    }
}
