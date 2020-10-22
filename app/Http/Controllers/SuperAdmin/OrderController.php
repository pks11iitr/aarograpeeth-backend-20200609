<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\BookingSlot;
use App\Models\HomeBookingSlots;
use App\Models\Order;
use App\Models\TimeSlot;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    public function index(Request $request){

		$orders=Order::with(['details.entity', 'customer', 'details.clinic'])
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

        return view('admin.order.index', compact('orders'));

    }
     public function product(Request $request){

		$orders=Order::with(['details.entity', 'customer', 'details.clinic'])
            ->where('status','!=', 'pending')
            ->whereHas('details', function($details){
                $details->where('entity_type', 'App\Models\Product');
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
         //var_dump($orders->toArray());die();
        return view('admin.order.product', compact('orders'));

    }

    public function details(Request $request, $id){
        $order=Order::with(['details.entity', 'customer'])->where('status', '!=', 'pending')->find($id);
        return view('admin.order.details', compact('order'));
    }

    public function productdetails(Request $request, $id){
        $order=Order::with(['details.entity', 'customer'])->where('status', '!=', 'pending')->find($id);
        return view('admin.order.productdetails', compact('order'));
    }

    public function changeStatus(Request $request, $id)
    {

        $status = $request->status;
        $order = Order::find($id);

        $order->status = $status;
        $order->save();

        return redirect()->back()->with('success', 'Order has been updated');
    }


    public function editTherapySession(Request $request){

        $type=$request->type;
        if($request->type=='clinic'){
            $booking=BookingSlot::with('clinic', 'therapy', 'timeslot')->findOrFail($request->id);
        }else if($request->type=='home'){
            $booking=HomeBookingSlots::with( 'therapy', 'timeslot')->findOrFail($request->id);
        }else{
            return redirect()->back()->with('error', 'Invalid Request');
        }

        return view('admin.order.booking', compact('booking', 'type'));
    }


    public function updateTherapySession(Request $request){



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

            $slot=HomeBookingSlots::find($request->slot_id);

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
