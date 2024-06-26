<?php

namespace App\Http\Controllers\ClinicAdmin;

use App\Models\BookingSlot;
use App\Models\Clinic;
use App\Models\HomeBookingSlots;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\TherapiestWork;
use App\Models\Therapist;
use App\Models\TimeSlot;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{

    public function index(Request $request){
        $user=auth()->user();
        $clinic =Clinic::where('user_id',$user->id)->first();

        $orders=Order::with(['details.entity', 'customer', 'details.clinic'])
            ->whereHas('details', function ($details) use ($clinic) {
                $details->where('clinic_id', $clinic->id);
            })
            ->where('status','!=', 'pending')
            ->whereHas('details', function($details){
                $details->where('entity_type', 'App\Models\Therapy');
            })
            ->where(function($orders) use($request){
                $orders->where('name','LIKE','%'.$request->search.'%')
                    ->orWhere('mobile','LIKE','%'.$request->search.'%')
                    ->orWhere('email','LIKE','%'.$request->search.'%')
                    ->orWhere('refid','LIKE','%'.$request->search.'%');
            });

        if($request->fromdate)
            $orders=$orders->where('created_at', '>=', $request->fromdate.'00:00:00');

        if($request->todate)
            $orders=$orders->where('created_at', '<=', $request->todate.'23:59:50');

        if($request->status)
            $orders=$orders->where('status', $request->status);

        if($request->payment_status)
            $orders=$orders->where('payment_status', $request->payment_status);

        if($request->ordertype){
            $orders=$orders->orderBy('created_at', $request->ordertype);
        }else{
            $orders=$orders->orderBy('created_at', 'DESC');
        }


        $orders=$orders->paginate(10);

        // $orders=Order::with(['details.entity', 'customer', 'details.clinic'])->where('status', '!=', 'pending')->orderBy('id', 'desc')->paginate(20);

        return view('clinicadmin.order.view', compact('orders'));

    }

    public function details(Request $request, $id){
        $user=auth()->user();
        $clinic =Clinic::where('user_id',$user->id)->first();
        $order=Order::with(['details.entity', 'customer'])
            ->whereHas('details', function ($details) use ($clinic) {
                $details->where('clinic_id', $clinic->id);
            })
            ->where('status', '!=', 'pending')
            ->findOrFail($id);
        return view('clinicadmin.order.details', compact('order'));
    }


    public function edit(Request $request,$id){
        $user=auth()->user();
        $clinic =Clinic::where('user_id',$user->id)->first();
        $therapists =Therapist::get();

        $order = Order::with(['bookingSlots','homebookingslots'])
            ->whereHas('details', function ($details) use ($clinic) {
                $details->where('clinic_id', $clinic->id);
            })->find($id);
        //var_dump($order->bookingSlots);die();
        return view('clinicadmin.order.edit',['order'=>$order,'therapists'=>$therapists]);
    }

    public function store(Request $request){
        TherapiestWork::updateOrCreate(
            ['therapist_id' => $request->input('therapist_id'),'home_booking_id' => $request->input('home_id')],
            [
                'status' => $request->input('status'),
            ]);

        return redirect()->back()->with('success', 'teacher has been updated');

    }

    public function editClinicSession(Request $request){

        $type=$request->type;
        if($request->type=='clinic'){
            $booking=BookingSlot::with('clinic', 'therapy', 'timeslot')->findOrFail($request->id);
        }else if($request->type=='home'){
            $booking=HomeBookingSlots::with( 'therapy', 'timeslot')->findOrFail($request->id);
        }else{
            return redirect()->back()->with('error', 'Invalid Request');
        }

        return view('clinicadmin.order.booking', compact('booking', 'type'));
    }


    public function updateClinicSession(Request $request){

        if($request->type=='clinic'){

            $request->validate([
                'id'=>'required|integer',
                'slot_id'=>'required|integer',
                'therapist_id'=>'required|integer',
                'status'=>'required|in:pending,confirmed,cancelled,completed',
                'type'=>'required|in:clinic,home'
            ]);

            $slot=TimeSlot::find($$request->slot_id);

            $booking=BookingSlot::with('clinic', 'therapy', 'timeslot')->findOrFail($request->id);

            $booking->update(array_merge($request->only( 'status', 'slot_id'),['assigned_therapist'=>$request->therapist_id, 'date'=>$slot->date, 'time'=>$slot->internal_start_time]));

        }else if($request->type=='home'){

            $request->validate([
                'id'=>'required|integer',
                'therapist_id'=>'required|integer',
                'status'=>'required|in:pending,confirmed,cancelled,completed',
                'type'=>'required|in:clinic,home'
            ]);

            $slot=HomeBookingSlots::find($$request->slot_id);

            $booking=HomeBookingSlots::findOrFail($request->id);
            if(empty($request->slot_id)){
                $booking->update(array_merge($request->only( 'status'),['assigned_therapist'=>$request->therapist_id]));
            }else{
                $booking->update(array_merge($request->only( 'status', 'slot_id'),['assigned_therapist'=>$request->therapist_id, 'date'=>$slot->date, 'time'=>$slot->internal_start_time]));
            }

        }

        return redirect()->back()->with('success', 'Booking Has Been Updated');
    }

    public function getAvailableTherapistInClinic(Request $request){
        $clinic=Clinic::findOrFail($request->clinic_id);//die;
        return $clinic->getAvailableTherapist($request->slot_id);
    }


    public function getAvailableTimeSlots(Request $request){
        $clinic=Clinic::findOrFail($request->clinic_id);
        $slots=TimeSlot::getTimeSlotsForAdmin($clinic, $request->date, $request->grade);
        return $slots;

    }



}
