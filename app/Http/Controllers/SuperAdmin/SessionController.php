<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\BookingSlot;
use App\Models\DailyBookingsSlots;
use App\Models\HomeBookingSlots;
use App\Models\TimeSlot;
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

    public function editTherapistSession(Request $request){

        $type=$request->type;
        if($request->type=='clinic'){
            $booking=BookingSlot::with('clinic', 'therapy', 'timeslot')
                ->findOrFail($request->id);
        }else if($request->type=='home'){
            $booking=HomeBookingSlots::with( 'therapy', 'timeslot')
                ->findOrFail($request->id);
        }else{
            return redirect()->back()->with('error', 'Invalid Request');
        }

        return view('admin.sessions.booking', compact('booking', 'type'));
    }


    public function updateTherapistSession(Request $request){

        if($request->type=='clinic'){

            $request->validate([
                'id'=>'required|integer',
                'slot_id'=>'required|integer',
                'therapist_id'=>'required|integer',
                'status'=>'required|in:pending,confirmed,cancelled,completed',
                'type'=>'required|in:clinic,home'
            ]);

            $slot=TimeSlot::find($request->slot_id);

            $booking=BookingSlot::with('clinic', 'therapy', 'timeslot')
                ->findOrFail($request->id);

            $booking->update(array_merge($request->only( 'status', 'slot_id'),['assigned_therapist'=>$request->therapist_id, 'date'=>$slot->date, 'time'=>$slot->internal_start_time]));

        }else if($request->type=='home'){

            $request->validate([
                'id'=>'required|integer',
                'therapist_id'=>'required|integer',
                'status'=>'required|in:pending,confirmed,cancelled,completed',
                'type'=>'required|in:clinic,home'
            ]);
            //var_dump($request->slot_id);die();
            $slot=DailyBookingsSlots::find($request->slot_id);
  //var_dump($slot);die();
            $booking=HomeBookingSlots::findOrFail($request->id);
            if(empty($request->slot_id)){
                $booking->update(array_merge($request->only( 'status'),['assigned_therapist'=>$request->therapist_id]));
            }else{
                $booking->update(array_merge($request->only( 'status', 'slot_id'),['assigned_therapist'=>$request->therapist_id, 'date'=>$slot->date, 'time'=>$slot->internal_start_time]));
            }

        }

        return redirect()->back()->with('success', 'Booking Has Been Updated');
    }

}
