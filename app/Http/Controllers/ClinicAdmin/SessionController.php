<?php

namespace App\Http\Controllers\ClinicAdmin;

use App\Models\BookingSlot;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SessionController extends Controller
{
    public function index_session(Request $request, $session_type, $therapist_id=null){
        switch($session_type){
            case 'clinic-session':return $this->getClinicSessionList($request, $therapist_id);
            /*            case 'therapist-session':return $this->getTherapySessionList($request, $therapist_id);*/
        }
        return view('clinicadmin.session.index');
    }


    public function getClinicSessionList(Request $request, $therapist_id=null){
        if($therapist_id){
            $sessions=BookingSlot::with(['clinic','assignedTo', 'review', 'therapy', 'diseases', 'painpoints','treatment', 'timeslot', 'order'])->where('assigned_therapist', $therapist_id)
                ->where('is_confirmed', true)
                ->where('is_paid', true)
                ->orderBy('id', 'desc')
                ->paginate(10);
        }else{
            $sessions=BookingSlot::orderBy('id', 'desc')
                ->paginate(10);
        }

        return view('clinicadmin.session.index', compact('sessions'));

    }

    /* public function getTherapySessionList(Request $request, $therapist_id){
         if($therapist_id){
             $sessions=HomeBookingSlots::with(['assignedTo', 'review', 'therapy', 'diseases', 'painpoints','treatment', 'timeslot', 'order'])->where('assigned_therapist', $therapist_id)
                 ->orderBy('id', 'desc')->paginate(10);
         }else{
             $sessions=HomeBookingSlots::orderBy('id', 'desc')->paginate(10);
         }
         return view('clinicadmin.therapist.index', compact('sessions'));
     }*/

    public function details(Request $request, $session_type, $id){

        switch($session_type){
            case 'clinic-session':return $this->getClinicSessionDetails($request, $id);
            /*case 'therapist-session':return $this->getTherapySessionDetails($request, $id);*/
        }

        return redirect()->back();

    }

    private function getClinicSessionDetails($request, $id){

        $user=auth()->user();

        $session=BookingSlot::with(['clinic','assignedTo', 'review', 'therapy', 'diseases', 'painpoints','treatment', 'timeslot', 'order'])->findOrFail($id);
        if($session->clinic->user_id!=$user->id){
            abort(404);
        }

        return view('clinicadmin.session.details', compact('session'));

    }

    /*private function getTherapySessionDetails($request, $id){
        $session=HomeBookingSlots::with(['assignedTo', 'review', 'therapy', 'diseases', 'painpoints','treatment', 'timeslot', 'order'])->findOrFail($id);
        //dd($session->painpoints);
        return view('clinicadmin.therapist.details', compact('session'));
    }*/



}
