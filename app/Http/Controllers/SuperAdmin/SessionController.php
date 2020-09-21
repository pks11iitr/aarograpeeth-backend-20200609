<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\BookingSlot;
use App\Models\HomeBookingSlots;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SessionController extends Controller
{
    public function index(Request $request, $session_type, $therapist_id=null){
        switch($session_type){
            case 'clinic-session':return $this->getClinicSessionList($request, $therapist_id);
            case 'therapist-session':return $this->getTherapySessionList($request, $therapist_id);
        }
        return view('admin.session.index');
    }


    public function getClinicSessionList(Request $request, $therapist_id){
        if($therapist_id){
            $sessions=BookingSlot::with(['clinic','assignedTo', 'review', 'therapy', 'diseases', 'painpoints','treatment', 'timeslot', 'order'])->where('assigned_therapist', $therapist_id)
            ->orderBy('id', 'desc')->paginate(10);
        }else{
            $sessions=BookingSlot::orderBy('id', 'desc')->paginate(10);
        }

        return view('admin.sessions.index', compact('sessions'));

    }

    public function getTherapySessionList(Request $request, $therapist_id){
        if($therapist_id){
            $sessions=HomeBookingSlots::with(['assignedTo', 'review', 'therapy', 'diseases', 'painpoints','treatment', 'timeslot', 'order'])->where('assigned_therapist', $therapist_id)
                ->orderBy('id', 'desc')->paginate(10);
        }else{
            $sessions=HomeBookingSlots::orderBy('id', 'desc')->paginate(10);
        }
        return view('admin.sessions.index', compact('sessions'));
    }

    public function details(Request $request, $session_type, $id){

        switch($session_type){
            case 'clinic-session':return $this->getClinicSessionDetails($request, $id);
            case 'therapist-session':return $this->getTherapySessionDetails($request, $id);
        }

        return redirect()->back();

    }

    private function getClinicSessionDetails($request, $id){

        $session=BookingSlot::with(['clinic','assignedTo', 'review', 'therapy', 'diseases', 'painpoints','treatment', 'timeslot', 'order'])->findOrFail($id);

        return view('admin.sessions.details', compact('session'));

    }

    private function getTherapySessionDetails($request, $id){
        $session=HomeBookingSlots::with(['assignedTo', 'review', 'therapy', 'diseases', 'painpoints','treatment', 'timeslot', 'order'])->findOrFail($id);
        //dd($session->painpoints);
        return view('admin.sessions.details', compact('session'));
    }


}
