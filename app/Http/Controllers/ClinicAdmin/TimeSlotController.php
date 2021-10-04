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

    public function deactivate(Request $request){

    }

    public function add(Request $request){

    }


    public function store(Request $request){

    }
}
