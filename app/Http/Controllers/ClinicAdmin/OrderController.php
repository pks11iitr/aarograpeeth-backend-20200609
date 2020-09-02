<?php

namespace App\Http\Controllers\ClinicAdmin;

use App\Models\Clinic;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\TherapiestWork;
use App\Models\Therapist;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    public function index(Request $request){
        $user=auth()->user();
        $clinic =Clinic::where('user_id',$user->id)->first();
        $orders = Order::whereHas('details', function ($details) use ($clinic) {
            $details->where('clinic_id', $clinic->id);
            })->get();

        return view('clinicadmin.order.view',['orders'=>$orders]);
    }

    public function details(Request $request, $id){
        $user=auth()->user();
        $clinic =Clinic::where('user_id',$user->id)->first();
        $order = Order::with(['bookingSlots','homebookingslots'])
            ->whereHas('details', function ($details) use ($clinic) {
            $details->where('clinic_id', $clinic->id);
        })->find($id);

        //var_dump($order->toArray());die();
        return view('clinicadmin.order.details',['order'=>$order]);
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
}
