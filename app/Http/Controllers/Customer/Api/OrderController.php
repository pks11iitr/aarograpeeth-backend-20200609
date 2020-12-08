<?php

namespace App\Http\Controllers\Customer\Api;

use App\Models\BookingSlot;
use App\Models\Cart;
use App\Models\Clinic;
use App\Models\DailyBookingsSlots;
use App\Models\HomeBookingSlots;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderStatus;
use App\Models\Product;
use App\Models\RescheduleRequest;
use App\Models\Therapy;
use App\Models\TimeSlot;
use App\Models\Wallet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use PDF;

class OrderController extends Controller
{
    public function index(Request $request){
        $user=auth()->guard('customerapi')->user();
        if(!$user)
            return [
                'status'=>'failed',
                'message'=>'Please login to continue'
            ];
        $orders=Order::with(['details.entity','details.clinic'])
            ->where('status', '!=','pending')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $lists=[];

        foreach($orders as $order) {
            //echo $order->id.' ';
            $total = count($order->details);
            $lists[] = [
                'id' => $order->id,
                'title' => ($order->details[0]->entity->name ?? '') . ' ' . ($total > 1 ? 'and ' . ($total - 1) . ' more' : ''),
                'booking_id' => $order->refid,
                'datetime' => date('D d M,Y', strtotime($order->created_at)),
                'total_price' => $order->total_cost,
                'image' => $order->details[0]->entity->image ?? ''
            ];
        }
        return [
            'status'=>'success',
            'data'=>$lists
        ];

    }


    /*
     * Product Purchase Or Therapy Book Start
     */
    public function initiateOrder(Request $request){

        $user=auth()->guard('customerapi')->user();
        if(!$user)
            return [
                'status'=>'failed',
                'message'=>'Please login to continue'
            ];

        switch($request->type){
            case 'clinic':
                return $this->initiateClinicBooking($request);
            case 'therapy':
                return $this->initiateTherapyBooking($request);
            case 'product':
                return $this->initiateProductPurchase($request);
            default:
                return [
                    'status'=>'failed',
                    'message'=>'Invalid Operation Performed'
                ];
        }
    }

    private function initiateProductPurchase(Request $request){

        $cartitems=Cart::where('user_id', auth()->guard('customerapi')->user()->id)
            ->with(['product'])
            ->whereHas('product', function($product){
                $product->where('isactive', true);
            })->get();

        if(!$cartitems)
            return [
                'status'=>'failed',
                'message'=>'Cart is empty'
            ];

        ///$refid=env('MACHINE_ID').time();
        $total_cost=0;
        foreach($cartitems as $item) {
            $total_cost=$total_cost+($item->product->price??0)*$item->quantity;
        }
        $refid=env('MACHINE_ID').time();
        $order=Order::create([
            'user_id'=>auth()->guard('customerapi')->user()->id,
            'refid'=>$refid,
            'status'=>'pending',
            'total_cost'=>$total_cost,
        ]);

        OrderStatus::create([
            'order_id'=>$order->id,
            'current_status'=>$order->status
        ]);

        foreach($cartitems as $item){
            OrderDetail::create([
                'order_id'=>$order->id,
                'entity_type'=>'App\Models\Product',
                'entity_id'=>$item->product_id,
                'clinic_id'=>null,
                'cost'=>$item->product->price??0,
                'quantity'=>$item->quantity
            ]);
        }

        return [
            'status'=>'success',
            'data'=>[
                'order_id'=>$order->id
            ]
        ];

    }


    private function initiateTherapyBooking(Request $request){
        $request->validate([
            'therapy_id'=>'required|integer',
            'booking_type'=>'required|in:instant,schedule',
            //'num_sessions'=>'required_if:booking_type,schedule|integer',
            'grade'=>'required_if:booking_type,instant|integer|in:1,2,3,4',
            //'time'=>'required_if:booking_type,schedule|date_format:H:i',
            //'date'=>'required_if:booking_type,schedule|date_format:Y-m-d',
            'schedule_type'=>'required_if:booking_type,schedule|in:automatic,custom'
        ]);

        $therapy=Therapy::active()->find($request->therapy_id);

        if(!$therapy)
            return [
                'status'=>'failed',
                'message'=>'Invalid Operation Performed'
            ];

        if($request->booking_type=='schedule'){
            return $this->initiateTherapyScheduleBooking($request, $therapy);
        }else{
            return $this->initiateTherapyInstantBooking($request, $therapy);
        }

    }

    private function initiateTherapyScheduleBooking(Request $request, $therapy){

        $refid=env('MACHINE_ID').time();

        $order=Order::create([
            'user_id'=>auth()->guard('customerapi')->user()->id,
            'refid'=>$refid,
            'status'=>'pending',
            'total_cost'=>0,
            'is_instant'=>false,
            'schedule_type'=>$request->schedule_type,
            'order_place_state'=>'stage_1'
        ]);

        OrderStatus::create([
            'order_id'=>$order->id,
            'current_status'=>$order->status
        ]);
        OrderDetail::create([
            'order_id'=>$order->id,
            'entity_type'=>'App\Models\Therapy',
            'entity_id'=>$therapy->id,
            'clinic_id'=>null,
            'cost'=>0,
            'quantity'=>0,
            'grade'=>1
        ]);

        return [
            'status'=>'success',
            'data'=>[
                'order_id'=>$order->id
            ]
        ];
    }

    private function initiateTherapyInstantBooking(Request $request, $therapy){
        //return $clinic;
        $grade=$request->grade??1;
        $num_sessions=1;

        switch($grade){
            case 1:$cost=($therapy->grade1_price??0);
                break;
            case 2:$cost=($therapy->grade2_price??0);
                break;
            case 3:$cost=($therapy->grade3_price??0);
                break;
            case 4:$cost=($therapy->grade4_price??0);
                break;
        }

        $refid=env('MACHINE_ID').time();

        $order=Order::create([
            'user_id'=>auth()->guard('customerapi')->user()->id,
            'refid'=>$refid,
            'status'=>'pending',
            'total_cost'=>$cost*$num_sessions,
            'booking_date'=>($request->booking_type=='schedule')?$request->date:null,
            'booking_time'=>($request->booking_type=='schedule')?$request->time:null,
            'is_instant'=>($request->booking_type=='instant')?true:false
        ]);

        OrderStatus::create([
            'order_id'=>$order->id,
            'current_status'=>$order->status
        ]);
        OrderDetail::create([
            'order_id'=>$order->id,
            'entity_type'=>'App\Models\Therapy',
            'entity_id'=>$therapy->id,
            'clinic_id'=>null,
            'cost'=>$cost,
            'quantity'=>$num_sessions,
            'grade'=>$request->grade
        ]);
        $code=rand(0,9).''.rand(0,9).''.rand(0,9).''.rand(0,9).''.rand(0,9).''.rand(0,9);
        HomeBookingSlots::create([
            'order_id'=>$order->id,
            'date'=>date('Y-m-d'),
            'grade'=>$request->grade,
            'time'=>null,
            'status'=>'pending',
            'is_instant'=>true,
            'therapy_id'=>$therapy->id,
            'price'=>$cost,
            'verification_code'=>$code
        ]);

        return [
            'status'=>'success',
            'data'=>[
                'order_id'=>$order->id
            ]
        ];
    }

    private function initiateClinicBooking(Request $request){

        $request->validate([
            'clinic_id'=>'required|integer',
            'therapy_id'=>'required|integer',
            'schedule_type'=>'required|in:automatic,custom'
        ]);

        $clinic=Clinic::active()->with(['therapies'=>function($therapies)use($request){
            $therapies->where('therapies.isactive', true)->where('therapies.id', $request->therapy_id);
        }])->find($request->clinic_id);

        if(!$clinic || empty($clinic->therapies->toArray())){
            return [
                'status'=>'failed',
                'message'=>'Clinic Or Therapy No Longer Exists'
            ];
        }

        $refid=env('MACHINE_ID').time();
        $order=Order::create([
            'user_id'=>auth()->guard('customerapi')->user()->id,
            'refid'=>$refid,
            'status'=>'pending',
            'schedule_type'=>$request->schedule_type,
            'order_place_state'=>'stage_1'
            ]);
        OrderStatus::create([
            'order_id'=>$order->id,
            'current_status'=>$order->status
        ]);

        OrderDetail::create([
            'order_id'=>$order->id,
            'entity_type'=>'App\Models\Therapy',
            'entity_id'=>$clinic->therapies[0]->id,
            'clinic_id'=>$clinic->id,
            'cost'=>0,
            'quantity'=>0,
        ]);

        return [
            'status'=>'success',
            'data'=>[
                'order_id'=>$order->id
            ]
        ];
    }


    /*
     * Select Time Slots For Scheduled Bookings
     */

    public function setSchedule(Request $request, $order_id){

        $user=auth()->guard('customerapi')->user();
        if(!$user)
            return [
                'status'=>'failed',
                'message'=>'Please login to continue'
            ];

        $order=Order::with('details')
        ->where('user_id', $user->id)->find($order_id);

        if(!$order)
            return [
                'status'=>'failed',
                'message'=>'No Such Order Exists'
            ];


        if($order->status!='pending' || $order->details[0]->entity_type!='App\Models\Therapy')
            return [
                'status'=>'failed',
                'message'=>'Your Booking Cannot Be Updated'
            ];

        if($order->details[0]->clinic_id!=null){
            return $this->setScheduleForClinicTherapy($request, $order);
        }else if($order->is_instant==0){
            return $this->setScheduleForHomeTherapy($request, $order);
        }

    }

    private function setScheduleForClinicTherapy(Request $request, $order){

        $clinic=Clinic::active()->with(['therapies'=>function($therapies)use($order){
            $therapies->where('therapies.id', $order->details[0]->entity_id);
        }])->find($order->details[0]->clinic_id);


        if($order->schedule_type=='automatic'){
            $request->validate([
                'num_sessions'=>'required|integer|max:50',
                'slot'=>'required|integer',
                'grade'=>'required|in:1,2,3,4'
            ]);
            $slot=TimeSlot::find($request->slot);
            if(!$slot || $slot->date < date('Y-m-d'))
                return [
                    'status'=>'failed',
                    'message'=>'Invalid Operation'
                ];

            BookingSlot::where('order_id', $order->id)
                ->delete();

            //var_dump($clinic->toArray());die;
            switch($request->grade){
                case 1:$cost=($clinic->therapies[0]->pivot->grade1_price??0);
                    break;
                case 2:$cost=($clinic->therapies[0]->pivot->grade2_price??0);
                    break;
                case 3:$cost=($clinic->therapies[0]->pivot->grade3_price??0);
                    break;
                case 4:$cost=($clinic->therapies[0]->pivot->grade4_price??0);
                    break;
            }

            if(!BookingSlot::createAutomaticSchedule($order, $request->grade, $cost, $slot, $request->num_sessions, 'pending')){
                return [
                    'status'=>'failed',
                    'message'=>'Enough Slots Are Not Available'
                ];
            }

            $cost=$cost*$request->num_sessions;
            $order->total_cost=$cost;
            $order->order_place_state='stage_2';
            $order->save();

        }else if($order->schedule_type=='custom'){
            $request->validate([
                'slots'=>'required|array',
                'slots.*'=>'integer',
                'grade'=>'required|in:1,2,3,4'
            ]);

            $slots=TimeSlot::whereIn('id', $request->slots)->get();
            if(empty($slots->toArray()))
                return [
                    'status'=>'failed',
                    'message'=>'No Time Slot Selected'
                ];

            $alldateslots=TimeSlot::where('date', $slots[0]->date)
                ->select('id')
                ->get();

            $slotsarr=[];
            foreach($alldateslots as $s)
                $slotsarr[]=$s->id;
            if(count($slotsarr))
                BookingSlot::where('order_id', $order->id)
                    ->whereIn('slot_id', $slotsarr)->delete();

            $cost=0;

            switch($request->grade){
                case 1:$cost=$cost+($clinic->therapies[0]->pivot->grade1_price??0);
                    break;
                case 2:$cost=$cost+($clinic->therapies[0]->pivot->grade2_price??0);
                    break;
                case 3:$cost=$cost+($clinic->therapies[0]->pivot->grade3_price??0);
                    break;
                case 4:$cost=$cost+($clinic->therapies[0]->pivot->grade4_price??0);
                    break;
            }

            foreach($slots as $slot){

                BookingSlot::create([
                    'order_id'=>$order->id,
                    'clinic_id'=>$order->details[0]->clinic_id,
                    'therapy_id'=>$order->details[0]->entity_id,
                    'slot_id'=>$slot->id,
                    'grade'=>$request->grade,
                    'status'=>'pending',
                    'price'=>$cost,
                    'date'=>$slot->date,
                    'time'=>$slot->internal_start_time
                ]);

                //$order->total_cost=$order->total_cost+$cost;

            }


            $sessions=BookingSlot::where('order_id', $order->id)
                ->where('status', 'pending')
                ->get();

            $full_cost=0;
            foreach($sessions as $s)
                $full_cost=$full_cost+$s->price;


            //$full_cost=$cost*$count;
            $order->total_cost=$full_cost;
            $order->order_place_state='stage_2';
            $order->save();

        }else{
            return [
                'status'=>'failed',
                'message'=>'Invalid Request'
            ];
        }

        return [
            'status'=>'success',
            'message'=>'Therapy Timings have been Saved'
        ];
    }

    private function setScheduleForHomeTherapy(Request $request, $order){

        $therapy=Therapy::find($order->details[0]->entity_id);

        if($order->schedule_type=='automatic'){
            $request->validate([
                'num_sessions'=>'required|integer|max:50',
                'slot'=>'required|integer',
                'grade'=>'required|in:1,2,3,4'
            ]);
            $slot=DailyBookingsSlots::find($request->slot);
            //if(!$slot || $slot->date < date('Y-m-d'))
            if(!$slot)
                return [
                    'status'=>'failed',
                    'message'=>'Invalid Operation'
                ];

            HomeBookingSlots::where('order_id', $order->id)
                ->delete();

            $cost=0;
            switch($request->grade){
                case 1:
                    $cost=$therapy->grade1_price??0;
                    break;
                case 2:
                    $cost=$therapy->grade2_price??0;
                    break;
                case 3:
                    $cost=$therapy->grade3_price??0;
                    break;
                case 4:
                    $cost=$therapy->grade4_price??0;
                    break;
            }

            if(!HomeBookingSlots::createAutomaticSchedule($order, $request->grade, $cost, $slot, $request->num_sessions, 'pending')){
                return [
                    'status'=>'failed',
                    'message'=>'Enough Slots Are Not Available'
                ];
            }
            //var_dump($clinic->toArray());die;
//            switch($request->grade){
//                case 1:$cost=($therapy->grade1_price??0);
//                    break;
//                case 2:$cost=($therapy->grade2_price??0);
//                    break;
//                case 3:$cost=($therapy->grade3_price??0);
//                    break;
//                case 4:$cost=($therapy->grade4_price??0);
//                    break;
//            }

            $cost=$cost*$request->num_sessions;
            $order->total_cost=$cost;
            $order->order_place_state='stage_2';
            $order->save();

        }else if($order->schedule_type=='custom'){
            $request->validate([
                'slots'=>'required|array',
                'slots.*'=>'integer',
                'grade'=>'required|in:1,2,3,4'
            ]);

            $slots=DailyBookingsSlots::whereIn('id', $request->slots)->get();
            if(empty($slots->toArray()))
                return [
                    'status'=>'failed',
                    'message'=>'No Time Slot Selected'
                ];

            $alldateslots=HomeBookingSlots::where('date', $slots[0]->date)->select('id')->get();

            $slotsarr=[];
            foreach($alldateslots as $s)
                $slotsarr[]=$s->id;
            if(count($slotsarr))
                HomeBookingSlots::where('order_id', $order->id)
                    ->whereIn('slot_id', $slotsarr)->delete();

            $cost=0;
            switch($request->grade){
                case 1:
                    $cost=$therapy->grade1_price??0;
                    break;
                case 2:
                    $cost=$therapy->grade2_price??0;
                    break;
                case 3:
                    $cost=$therapy->grade3_price??0;
                    break;
                case 4:
                    $cost=$therapy->grade4_price??0;
                    break;
            }

            foreach($slots as $slot){
                $code=rand(0,9).''.rand(0,9).''.rand(0,9).''.rand(0,9).''.rand(0,9).''.rand(0,9);
                HomeBookingSlots::create([
                    'order_id'=>$order->id,
                    'slot_id'=>$slot->id,
                    'grade'=>$request->grade,
                    'status'=>'pending',
                    'date'=>$slot->date,
                    'time'=>$slot->internal_start_time,
                    'therapy_id'=>$therapy->id,
                    'price'=>$cost,
                    'verification_code'=>$code
                ]);

            }


            $sessions=HomeBookingSlots::where('order_id', $order->id)
                ->where('status', 'pending')
                ->get();

            $full_cost=0;
            foreach($sessions as $s)
                $full_cost=$full_cost+$s->price;

            //$order->total_cost=$order->total_cost+$cost;
            $order->total_cost=$full_cost;
            $order->order_place_state='stage_2';
            $order->save();

        }else{
            return [
                'status'=>'failed',
                'message'=>'Invalid Request'
            ];
        }

        return [
            'status'=>'success',
            'message'=>'Therapy Timings have been Saved'
        ];
    }



    /*
     * Display Scheduled For Therapy Bookings
     */
    public function displaySchedule(Request $request, $order_id){

        $show_add_more_slots=0;
        $user=auth()->guard('customerapi')->user();
        if(!$user)
            return [
                'status'=>'failed',
                'message'=>'Please login to continue'
            ];

        $order=Order::with('details')->where('user_id', $user->id)->find($order_id);

        if(!$order)
            return [
                'status'=>'success',
                'message'=>'Invalid Operation'
            ];

        if($order->details[0]->entity_type!='App\Models\Therapy')
            return [
                'status'=>'failed',
                'message'=>'Invalid Operation'
            ];
        $clinic_id=$order->details[0]->clinic_id;
        $therapy_id=$order->details[0]->entity_id;

        if($order->details[0]->clinic_id){
            $bookings=BookingSlot::with(['timeslot', 'review'])
                ->where('order_id', $order->id)
                ->orderBy('slot_id', 'asc')
                ->get();
        }else{
            $bookings=HomeBookingSlots::with(['timeslot', 'review'])
                ->where('order_id',$order->id)
                ->orderBy('slot_id', 'asc')
                ->get();
        }

        if($order->status=='pending' && $order->schedule_type=='custom'){
            $show_add_more_slots=1;
        }

        if($order->status=='pending'){
            $continue_text='Continue';
        }else {
            $continue_text='Close';
        }



        $schedules=[];

        foreach($bookings as $schedule){
            $grade=$schedule->grade==1?'Diamond':($schedule->grade==2?'Platinum':$schedule->grade==3?'Silver':$schedule->grade==4?'Gold':'');

            //die('dd');
            $schedules[]=[
                'show_delete'=> (in_array($order->status, ['pending'] ) && $schedule->is_instant==0)?1:0,
                'date'=>$schedule->timeslot->date??$schedule->date,
                'time'=>'1 Session at '.($schedule->timeslot->start_time??'Instant Booking'),
                'grade'=>$grade,
                'id'=>$schedule->id,
                'show_cancel'=>(in_array($schedule->status,['pending']) && in_array($order->status, ['confirmed']) && $schedule->is_instant==0)  ?1:0,
                'show_reschedule'=>(in_array($schedule->status,['pending']) && in_array($order->status, ['confirmed']) && $schedule->is_instant==0 )?1:0,
                'show_review'=>($schedule->status=='completed')?(!empty($schedule->review)?0:1):0,
                'verification_code'=>$schedule->verification_code,
                'show_details'=>($schedule->status=='completed')?1:0,
                'show_therapist'=>($schedule instanceof HomeBookingSlots && $schedule->status=='confirmed')?1:0,
            ];
        }

        $order_id=$order->id;
        return [
            'status'=>'success',
            'data'=>compact('schedules','clinic_id', 'therapy_id', 'order_id', 'show_add_more_slots','continue_text')
        ];

    }

//    public function bookingDetails(Request $request, $order_id, $booking_id){
//        $user=$request->user;
//
//        $order=Order::with('details')
//            ->where('user_id',$user->id)
//            ->find($order_id);
//
//        if(!$order)
//            return [
//                'status'=>'failed',
//                'message'=>'No such order found'
//            ];
//
//        if($order->details[0]->clinic_id){
//            $openbookingdetails=BookingSlot::with(['therapy','timeslot', 'diseases', 'painpoints', 'treatment'])
//                ->where('status',  'completed')
//                ->where('assigned_therapist', $user->id)
//                ->find($booking_id);
//        }else{
//            $openbookingdetails=HomeBookingSlots::with(['therapy','timeslot', 'diseases', 'painpoints', 'treatment'])
//                ->where('status',  'completed')
//                ->where('assigned_therapist', $user->id)
//                ->find($booking_id);
//        }
//
//        if(!$openbookingdetails)
//            return [
//                'status'=>'failed',
//                'message'=>'No Therapy Found'
//            ];
//
//        //instant timing
//        if($openbookingdetails->is_instant==0){
//            $timing=  ($openbookingdetails->timeslot->date??$openbookingdetails->date)." ".($openbookingdetails->timeslot->start_time??$openbookingdetails->time);
//        }else{
//            $timing=$openbookingdetails->date.' '.'Instant Booking';
//        }
//        // distance calculate
//
//        return [
//            'status' => 'success',
//            'booking_status'=>$openbookingdetails->therapist_status,
//            'total_cost'=>$openbookingdetails->price,
//            //'schedule_type'=>$openbookingdetails->order->schedule_type,
//            'name'=>$order->name,
//            'mobile'=>$order->mobile,
//            'address'=>$order->address,
//            //'distance_away'=>$distance,
//            'timing'=>$timing,
//            'therapy_name'=>$openbookingdetails->therapy->name,
//            'image'=>$openbookingdetails->therapy->image,
//            'id'=>$booking_id,
//            'order_id'=>$order_id,
//            'comments'=>$openbookingdetails->message??'',
//            'diseases'=>$openbookingdetails->diseases,
//            'painpoints'=>$openbookingdetails->painpoints,
//            'treatment'=>$openbookingdetails->treatment,
//            /*'data' =>$openbookingdetails,*/
//        ];
//    }

    public function bookingDetails(Request $request, $order_id, $booking_id){
        $user=$request->user;

        $order=Order::with('details')
            ->where('user_id',$user->id)
            ->find($order_id);

        if(!$order)
            return [
                'status'=>'failed',
                'message'=>'No such order found'
            ];

        if($order->details[0]->clinic_id){
            $openbookingdetails=BookingSlot::with(['therapy','timeslot', 'diseases', 'painpoints', 'mainDiseases', 'reasonDiseases','diagnose', 'treatmentsGiven'])
                ->where('status',  'completed')
                ->where('assigned_therapist', $user->id)
                ->find($booking_id);
        }else{
            $openbookingdetails=HomeBookingSlots::with(['therapy','timeslot', 'diseases', 'painpoints', 'mainDiseases', 'reasonDiseases','diagnose', 'treatmentsGiven'])
                ->where('status',  'completed')
                ->where('assigned_therapist', $user->id)
                ->find($booking_id);
        }

        if(!$openbookingdetails)
            return [
                'status'=>'failed',
                'message'=>'No Therapy Found'
            ];

        //instant timing
        if($openbookingdetails->is_instant==0){
            $timing=  ($openbookingdetails->timeslot->date??$openbookingdetails->date)." ".($openbookingdetails->timeslot->start_time??$openbookingdetails->time);
        }else{
            $timing=$openbookingdetails->date.' '.'Instant Booking';
        }

        $main_diseases=[];
        foreach($openbookingdetails->mainDiseases as $md){
            $main_diseases[$md->id]=[
                'name'=>$md->name,
                'reason_diseases'=>''
            ];
        }

        foreach($openbookingdetails->reasonDiseases as $rd){
            if(isset($main_diseases[$rd->pivot->disease_id])) {
                $main_diseases[$rd->pivot->disease_id]['reason_diseases']=$main_diseases[$rd->pivot->disease_id]['reason_diseases'].$rd->name.', ';
            }
        }

        $main_diseases1=[];
        foreach($main_diseases as $d){
            $main_diseases1[]=$d;
        }


        $treatments=[];
        foreach($openbookingdetails->treatmentsGiven as $t){
            $treatments[]=['name'=>$t->description];
        }

        $diagnose=[];
        foreach($openbookingdetails->diagnose as $dg){
            $diagnose[]=[
                'name'=>$dg->name,
                'before'=>$dg->pivot->before_value??'',
                'after'=>$dg->pivot->after_value??''
            ];
        }

        return [
            'status' => 'success',
            'booking_status'=>$openbookingdetails->therapist_status,
            'total_cost'=>$openbookingdetails->price,
            //'schedule_type'=>$openbookingdetails->order->schedule_type,
            'name'=>$order->name,
            'mobile'=>$order->mobile,
            'address'=>$order->address,
            //'distance_away'=>$distance,
            'timing'=>$timing,
            'therapy_name'=>$openbookingdetails->therapy->name,
            'image'=>$openbookingdetails->therapy->image,
            'id'=>$booking_id,
            'order_id'=>$order_id,
            'comments'=>$openbookingdetails->message??'',
            'diseases'=>$openbookingdetails->diseases,
            'painpoints'=>$openbookingdetails->painpoints,
            'treatment'=>$treatments,
            'show_feedback_button'=>empty($openbookingdetails->feedback_from_therapist)?1:0,
            'feedback_from_therapist'=>$openbookingdetails->feedback_from_therapist??'',
            'main_diseases'=>$main_diseases1,
            'diagnose'=>$diagnose,
            'therapy_result'=>$openbookingdetails->customerResults()
            /*'data' =>$openbookingdetails,*/
        ];
    }

    public function getTherapistLocation(Request $request, $order_id, $booking_id){
        $user=$request->user;

        $order=Order::with('details')
            ->where('user_id',$user->id)
            ->find($order_id);

        if(!$order)
            return [
                'status'=>'failed',
                'message'=>'No such order found'
            ];

        if($order->details[0]->clinic_id){
            $openbookingdetails=BookingSlot::with(['assignedTo'])
                ->where('status',  'completed')
                ->where('assigned_therapist', $user->id)
                ->find($booking_id);
        }else{
            $openbookingdetails=HomeBookingSlots::with(['assignedTo'])
                ->where('status',  'completed')
                ->where('assigned_therapist', $user->id)
                ->find($booking_id);
        }

        if(!$openbookingdetails)
            return [
                'status'=>'failed',
                'message'=>'No Therapy Found'
            ];

        return [
            'status'=>'success',
            'data'=>[
                'order'=>[
                    'lat'=>$order->lat,
                    'lang'=>$order->lang
                ],
                'therapist'=>[
                    'lat'=>$openbookingdetails->assignedTo->last_lat??null,
                    'lang'=>$openbookingdetails->assignedTo->last_lang??null
                ]
            ]
        ];

    }


    /*
     * Delete a session only for non confirmed orders
     */
    public function deleteBooking(Request $request, $order_id, $booking_id){

        $user=auth()->guard('customerapi')->user();
        if(!$user)
            return [
                'status'=>'failed',
                'message'=>'Please login to continue'
            ];

        $order=Order::with('details')
            ->where('user_id', $user->id)
            ->whereIn('status', ['pending'])
            ->find($order_id);

        if(!$order || $order->details[0]->entity_type!='App\Models\Therapy')
            return [
                'status'=>'failed',
                'message'=>'Invalid Request'
            ];

        if($order->details[0]->clinic_id){
            $booking=BookingSlot::find($booking_id);
            if(!in_array($booking->status, ['pending'])){
                return [
                    'status'=>'failed',
                    'message'=>'Booking Cannot Be Cancelled'
                ];
            }

//            $clinic=Clinic::active()->with(['therapies'=>function($therapies)use($order){
//                $therapies->where('therapies.id', $order->details[0]->entity_id);
//            }])->find($order->details[0]->clinic_id);
//
//            switch($booking->grade){
//                case 1:$cost=($clinic->therapies[0]->pivot->grade1_price??0);
//                    break;
//                case 2:$cost=($clinic->therapies[0]->pivot->grade2_price??0);
//                    break;
//                case 3:$cost=($clinic->therapies[0]->pivot->grade3_price??0);
//                    break;
//                case 4:$cost=($clinic->therapies[0]->pivot->grade4_price??0);
//                    break;
//            }
        }else{
            $booking=HomeBookingSlots::find($booking_id);
            if(!in_array($booking->status, ['pending'])){
                return [
                    'status'=>'failed',
                    'message'=>'Booking Cannot Be Cancelled'
                ];
            }

//            $therapy=Therapy::find($order->details[0]->entity_id);
//
//            switch($booking->grade){
//                case 1:$cost=($therapy->grade1_price??0);
//                    break;
//                case 2:$cost=($therapy->grade2_price??0);
//                    break;
//                case 3:$cost=($therapy->grade3_price??0);
//                    break;
//                case 4:$cost=($therapy->grade4_price??0);
//                    break;
//            }
        }

        $order->total_cost=$order->total_cost-$booking->price;
        $order->save();
        $booking->delete();

        return [
            'status'=>'success',
            'message'=>'Session Has Been Deleted'
        ];
    }


    public function addContactDetails(Request $request, $id){

        $request->validate([
           'name'=>'required|max:60|string',
           'email'=>'email',
           'mobile'=>'required|digits:10',
            'address'=>'string|max:100|nullable',
            'lat'=>'numeric',
            'lang'=>'numeric'
        ]);

        $user=auth()->guard('customerapi')->user();

        if(!$user)
            return [
                'status'=>'failed',
                'message'=>'Please login to continue'
            ];
        $order=Order::where('user_id', $user->id)->find($id);

        if(!$order)
            return [
                'status'=>'failed',
                'message'=>'Invalid Operation Performed'
            ];
        $request->merge(['order_details_completed'=>true]);
        if($order->update($request->only('name','email','address', 'mobile','lat', 'lang'))){
            return [
                'status'=>'success',
                'message'=>'Address has been updated'
            ];
        }

    }

    public function orderdetails(Request $request, $id){

        $show_cancel_product=0;
        $show_cancel=0;
        $show_reschedule=0;
        $show_time_slots_button=0;
        $show_download_invoice=0;


        $user=auth()->guard('customerapi')->user();
        if(!$user)
            return [
                'status'=>'failed',
                'message'=>'Please login to continue'
            ];
        $order=Order::with(['details.entity', 'details.clinic'])->where('user_id', $user->id)->find($id);


        if($order->details[0]->clinic_id??null){
            //clinic therapy
            $session=BookingSlot::where('order_id', $id)->first();
            $count=BookingSlot::where('order_id', $id)->count();
        }else{
            //home therapy
            $session=HomeBookingSlots::where('order_id', $id)->first();
            $count=HomeBookingSlots::where('order_id', $id)->count();
        }

        if(!$order)
            return [
                'status'=>'failed',
                'message'=>'Invalid Operation Performed'
            ];

//        if($order->status=='pending') {
//            $order->total_cost = Order::getTotal($order);
//            $order->save();
//        }


        //get reviews information
        $reviews=[];
        if($order->status=='completed'){
            $reviews=$order->reviews()->where('session_id', null)->get();
            foreach($reviews as $review){
                $reviews[$review->entity_id]=$review;
            }
        }

        if($order->is_instant==1){
            $verification_code=$order->homebookingslots[0]->verification_code??'';
        }

        if(!in_array($order->status, ['pending', 'cancelled'])){
            $show_download_invoice=1;
        }

        $itemdetails=[];
        foreach($order->details as $detail){
            if($detail->entity instanceof Therapy){

                //$order->booking_date='2020-08-31';
                //$order->booking_time='08:00 PM';

                $itemdetails[]=[
                    'name'=>($detail->entity->name??'')." ( Grade $detail->grade )",
                    'small'=>$count.(!empty($detail->clinic->name)?' sesions at '.$detail->clinic->name:' sessions'),
                    'price'=>$session->price??0,
                    'quantity'=>$detail->quantity,
                    'image'=>$detail->entity->image??'',
//                    'booking_date'=>($order->is_instant==1)?date('d/m/Y', strtotime($order->created_at)):null,
//                    'booking_time'=>($order->is_instant==1)?'Instant Booking':null,
                    'booking_date'=>null,
                    'booking_time'=>null,
                    'item_id'=>$detail->entity_id,
                    'show_review'=>in_array($order->status,['completed'])?(empty($order->details[0]->clinic_id)?(isset($reviews[$detail->entity_id])?0:1):0):0,
                    'show_clinic_review'=>in_array($order->status,['completed'])?(!empty($order->details[0]->clinic_id)?(isset($reviews[$detail->entity_id])?0:1):0):0,
                    'verification_code'=>($order->is_instant==1)?($verification_code??''):''
                ];
            }
            else{
                $itemdetails[]=[
                    'name'=>$detail->entity->name??'',
                    'small'=>$detail->entity->company??'',
                    'price'=>$detail->cost,
                    'quantity'=>$detail->quantity,
                    'image'=>$detail->entity->image??'',
                    'booking_date'=>$order->booking_date,
                    'booking_time'=>$order->booking_time,
                    'item_id'=>$detail->entity_id,
                    'show_review'=>in_array($order->status,['completed'])?(isset($reviews[$detail->entity_id])?0:1):0,
                    'show_clinic_review'=>0
                ];
            }
        }

        // options to be displayed
        if($order->status=='confirmed'){
            if($order->details[0]->entity instanceof Product){
                $show_cancel_product=1;
            }else{
                $show_cancel=1;
            }
            if($order->details[0]->entity instanceof Therapy  && $order->is_instant!=1){
                $show_reschedule=1;
            }

        }

        if($order->details[0]->entity instanceof Therapy){
            if($order->details[0]->clinic_id!=null){
                $show_time_slots_button=1;
            }else{
                if($order->is_instant==0){
                    $show_time_slots_button=1;
                }else{
                    if($order->status!='pending'){
                        $show_time_slots_button=1;
                    }
                }
            }
        }



        $date=date('Y-m-d');
        for($i=1; $i<=7;$i++){
            $dates[]=[
                'text'=>($i==1)?'Today':($i==2?'Tomorrow':date('d F', strtotime($date))),
                'text2'=>($i==1)?'':($i==2?'':date('D', strtotime($date))),
                'value'=>$date
            ];
            $date=date('Y-m-d', strtotime('+'.$i.' days', strtotime($date)));
        }
        $date=date('Y-m-d h:i:s');
        for($i=9; $i<=17;$i++){
            $timings[]=[
                'text'=>date('h:i A', strtotime($date)),
                'value'=>date('H:i', strtotime($date))
            ];
            $date=date('Y-m-d H:i:s', strtotime('+1 hours', strtotime($date)));
        }

        return [
            'status'=>'success',
            'data'=>[
                'orderdetails'=>$order->only('id', 'total_cost','refid', 'status','payment_mode', 'name', 'mobile', 'email', 'address','booking_date', 'booking_time','is_instant','status'),
                'itemdetails'=>$itemdetails,
                'balance'=>Wallet::balance($user->id),
                'points'=>Wallet::points($user->id),
                'show_cancel'=>$show_cancel??0,
                'show_reschedule'=>$show_reschedule??0,
                'show_cancel_product'=>$show_cancel_product??0,
                'dates'=>$dates,
                'timings'=>$timings,
                'show_time_slots_btn'=>$show_time_slots_button??0,
                'verification_code'=>$verification_code??'',
                'show_download_invoice'=>$show_download_invoice,
                'invoice_url'=>route('download.invoice', ['refid'=>$order->refid])
            ]
        ];
    }


    public function downloadInvoice(Request $request, $refid){
        $order = Order::where('refid', $refid)->first();

        if(!$order)
            return [
                'status'=>'failed',
                'message'=>'No Invoice Found'
            ];

        // var_dump($orders);die();
        $pdf = Order::generateInvoicePdfRaw($order->refid);

        return $pdf->download('invoice.pdf');
    }

    public function rescheduleOrder(Request $request, $id){

        $request->validate([
            'time'=>'required|date_format:H:i',
            'date'=>'required|date_format:Y-m-d',
        ]);

        $therapy_reschedule_status=[
            'confirmed', 'in-process'
        ];

        $user=auth()->guard('customerapi')->user();
        if(!$user)
            return [
                'status'=>'failed',
                'message'=>'Please login to continue'
            ];

        $order=Order::with(['details.entity', 'details.clinic'])->where('user_id', $user->id)->find($id);

        if(!$order)
            return [
                'status'=>'failed',
                'message'=>'Invalid Operation Performed'
            ];

        if(!in_array($order->status, $therapy_reschedule_status)){
            return [
                'status'=>'failed',
                'message'=>'Order cannot be rescheduled now'
            ];
        }

        if($order->details[0]->entity instanceof Therapy && $order->is_instant != 1){
            $order->booking_date=$request->date;
            $order->booking_time=$request->time;
            $order->save();
            return [
                'status'=>'success',
                'message'=>'Your booking has been rescheduled'
            ];
        }else{
            return [
                'status'=>'failed',
                'message'=>'Invalid operation performed'
            ];
        }

    }


    /*
     * Cancel Single Session
     */

    public function cancelBooking(Request $request, $id){

        $request->validate([
            'booking_id'=>'required|integer'
        ]);

        $user=auth()->guard('customerapi')->user();
        if(!$user)
            return [
                'status'=>'failed',
                'message'=>'Please login to continue'
            ];

        $order=Order::with(['details'])
            ->where('user_id', $user->id)->find($id);

        if(!$order)
            return [
                'status'=>'failed',
                'message'=>'Invalid Operation Performed'
            ];

        if($order->details[0]->entity instanceof Therapy){

            if($order->is_instant){
                return $this->cancelInstantTherapyBooking($request, $order);
            }else{
                if($order->details[0]->clinic_id){
                    return $this->cancelClinicTherapyBooking($request, $order);
                }else{
                    return $this->cancelHomeTherapyBooking($request, $order);
                }
            }
        }


        return [
            'status'=>'failed',
            'message'=>'Unrecognized Request'
        ];

    }

    private function cancelInstantTherapyBooking(Request $request, $order){

        $booking=HomeBookingSlots::where('order_id', $order->id)
            ->whereIn('status', ['pending'])
            ->find($request->booking_id);

        if(!$booking)
            return [
                'status'=>'failed',
                'message'=>'Booking Cannot Be Cancelled'
            ];

        $booking->status='cancelled';
        $booking->save();

        $order->status='cancelled';
        $order->save();


        Wallet::updatewallet($order->user_id, 'Booking Cancellation For Order ID: '.$order->refid, 'Credit', $booking->price, 'CASH', $order->refid);

        return [
            'status'=>'success',
            'message'=>'Your booking has been cancelled. Refund amount has been credited to wallet.'
        ];
    }

    private function cancelClinicTherapyBooking(Request $request, $order){

        $booking=BookingSlot::where('order_id', $order->id)
            ->whereIn('status', ['pending'])
            ->find($request->booking_id);

        if(!$booking)
            return [
                'status'=>'failed',
                'message'=>'Booking Cannot Be Cancelled'
            ];

        $booking->status='cancelled';
        $booking->save();

        $count=BookingSlot::where('status', '!=', 'cancelled')->count();
        if($count==0){
            $order->status='cancelled';
            $order->save();
        }

        if((strtotime($booking->date.' '.$booking->time) - strtotime('now')) > 60*60*24){
            // before 24 hours full refund
            Wallet::updatewallet($order->user_id, 'Booking Cancellation For Order ID: '.$order->refid, 'Credit', $booking->price, 'CASH', $order->refid);
        }else if((strtotime($booking->date.' '.$booking->time) - strtotime('now')) > 60*60*12){
            //before 12 hours
            Wallet::updatewallet($order->user_id, 'Booking Cancellation For Order ID: '.$order->refid, 'Credit', round($booking->price*90/100), 'CASH', $order->refid);
        }else if((strtotime($booking->date.' '.$booking->time) - strtotime('now')) > 60*60*6){
            //before 12 hours
            Wallet::updatewallet($order->user_id, 'Booking Cancellation For Order ID: '.$order->refid, 'Credit', round($booking->price*80/100), 'CASH', $order->refid);
        }else{
            return [
                'status'=>'failed',
                'message'=>'This booking cannot be cancelled now'
            ];
        }

        return [
            'status'=>'success',
            'message'=>'Your booking has been cancelled. Refund amount has been credited to wallet.'
        ];
    }

    private function cancelHomeTherapyBooking(Request $request, $order){

        $booking=HomeBookingSlots::where('order_id', $order->id)
            ->whereIn('status', ['pending'])
            ->find($request->booking_id);

        if(!$booking)
            return [
                'status'=>'failed',
                'message'=>'Order cannot be cancelled now'
            ];

        $booking->status='cancelled';
        $booking->save();

        $count=HomeBookingSlots::where('status', '!=', 'cancelled')->count();
        if($count==0){
            $order->status='cancelled';
            $order->save();
        }

        if((strtotime($booking->date.' '.$booking->time) - strtotime('now')) > 60*60*24){
            // before 24 hours full refund
            Wallet::updatewallet($order->user_id, 'Booking Cancellation For Order ID: '.$order->refid, 'Credit', $booking->price, 'CASH', $order->refid);
        }else if((strtotime($booking->date.' '.$booking->time) - strtotime('now')) > 60*60*12){
            //before 12 hours
            Wallet::updatewallet($order->user_id, 'Booking Cancellation For Order ID: '.$order->refid, 'Credit', round($booking->price*90/100), 'CASH', $order->refid);
        }else if((strtotime($booking->date.' '.$booking->time) - strtotime('now')) > 60*60*6){
            //before 12 hours
            Wallet::updatewallet($order->user_id, 'Booking Cancellation For Order ID: '.$order->refid, 'Credit', round($booking->price*80/100), 'CASH', $order->refid);
        }else{
            return [
                'status'=>'failed',
                'message'=>'This booking cannot be cancelled now'
            ];
        }

        return [
            'status'=>'success',
            'message'=>'Your booking has been cancelled. Refund process will be initiated shortly'
        ];
    }


    /*
     * Get Available Slots For Booking
     */

    public function getAvailableSlots(Request $request, $order_id){

        $user=$request->user;
        $order=Order::with(['details'])->where('user_id', $user->id)->find($order_id);

        if(!$order)
            return [
                'status'=>'failed',
                'message'=>'No Such Record Found'
            ];
        //dd($order);
        if($order->details[0]->entity_type=='App\Models\Therapy'){
            if($order->details[0]->clinic_id){
                return $this->getClinicAvailableSlots($order,$order->details[0]->clinic_id, $order->details[0]->entity_id, $request->date??date('Y-m-d'));
            }else{
                return $this->getTherapyAvailableSlots($order,$order->details[0]->entity_id, $request->date??date('Y-m-d'));
            }
        }

        return [
            'status'=>'failed',
            'message'=>'Unreconized Request'
        ];
    }

    private function getClinicAvailableSlots($order, $clinic_id, $therapy_id, $date){
        $date=date('Y-m-d', strtotime($date));
        $selected_date=$date;
        $today=date('Y-m-d');
        //var_dump($therapy_id);die;
        $clinic=Clinic::with(['therapies'=>function($therapies) use($therapy_id){
            $therapies->where('therapies.isactive', true)->where('therapies.id', $therapy_id)->where('clinic_therapies.isactive', true);
        }])->find($clinic_id);
        //dd($clinic);
        if(!$clinic || empty($clinic->therapies->toArray()))
            return [
                'status'=>'failed',
                'message'=>'No clinic found'
            ];

        $timeslots=TimeSlot::getTimeSlots($clinic, $date);

        for($i=1; $i<=7;$i++){
            $dates[]=[
                'text'=>($i==1)?'Today':($i==2?'Tomorrow':date('d F', strtotime($today))),
                'text2'=>($i==1)?'':($i==2?'':date('D', strtotime($today))),
                'value'=>$today,
            ];
            $today=date('Y-m-d', strtotime('+1 days', strtotime($today)));
        }

        $timeslots=[
            $timeslots['grade_1_slots'],
            $timeslots['grade_2_slots'],
            $timeslots['grade_3_slots'],
            $timeslots['grade_4_slots'],
        ];
        $order_id=$order->id;
        return [
            'status'=>'success',
            'data'=>compact('timeslots','dates', 'selected_date', 'order_id')
        ];
    }

    private function getTherapyAvailableSlots($order,$therapy_id, $date){
        $therapy=Therapy::active()->find($therapy_id);
        //dd($therapy);
        $timeslots=DailyBookingsSlots::getTimeSlots($therapy, $date);

        $selected_date=$date;

        $today=date('Y-m-d');

        for($i=1; $i<=7;$i++){
            $dates[]=[
                'text'=>($i==1)?'Today':($i==2?'Tomorrow':date('d F', strtotime($today))),
                'text2'=>($i==1)?'':($i==2?'':date('D', strtotime($today))),
                'value'=>$today,
            ];
            $today=date('Y-m-d', strtotime('+1 days', strtotime($today)));
        }


        $timeslots=[
            $timeslots['grade_1_slots'],
            $timeslots['grade_2_slots'],
            $timeslots['grade_3_slots'],
            $timeslots['grade_4_slots'],
        ];
        $order_id=$order->id;
        return [
            'status'=>'success',
            'data'=>compact('timeslots','dates', 'selected_date', 'order_id')
        ];

    }


    /*
     * Reschedule Functionality
     */
    public function getRescheduleSlots(Request $request, $order_id, $booking_id){

        $date=$request->date??date('Y-m-d');
        $selected_date=$date;
        $today=date('Y-m-d');

        $user=$request->user;

        $order=Order::with('details.clinic', 'details.entity')
            ->where('status', 'confirmed')
            ->where('user_id', $user->id)
            ->find($order_id);
        if(!$order)
            return [
                'status'=>'failed',
                'message'=>'No Such Record Found'
            ];

        if($order->details[0]->entity_type!='App\Models\Therapy')
            return [
                'status'=>'failed',
                'message'=>'Unrecognized Request'
            ];

        if($order->details[0]->clinic_id){
            $booking=BookingSlot::find($booking_id);
        }else{
            $booking=HomeBookingSlots::find($booking_id);
        }
        if(!$booking)
            return [
                'status'=>'failed',
                'message'=>'No Such Record Found'
            ];

        if($order->details[0]->clinic_id){
            $availableslots=TimeSlot::getRescheduleTimeSlots($order->details[0]->clinic, $date, $booking);
        }else{
            $availableslots=DailyBookingsSlots::getRescheduleTimeSlots($order->details[0]->entity, $date,$booking);
        }

        $timeslots=[
            $availableslots
        ];

        for($i=1; $i<=7;$i++){
            $dates[]=[
                'text'=>($i==1)?'Today':($i==2?'Tomorrow':date('d F', strtotime($today))),
                'text2'=>($i==1)?'':($i==2?'':date('D', strtotime($today))),
                'value'=>$today,
            ];
            $today=date('Y-m-d', strtotime('+1 days', strtotime($today)));
        }

        $order_id=$order->id;
        return [
            'status'=>'success',
            'data'=>compact('timeslots','dates', 'selected_date', 'order_id', 'booking_id')
        ];

    }


    public function rescheduleBooking(Request $request, $order_id, $booking_id){

        $request->validate([
            'slot_id'=>'required|integer'
        ]);

        $user=$request->user;

        $order=Order::with('details')
            ->where('status', 'confirmed')
            ->where('user_id', $user->id)
            ->find($order_id);
        if(!$order)
            return [
                'status'=>'failed',
                'message'=>'No Such Record Found'
            ];

        if($order->details[0]->entity_type!='App\Models\Therapy')
            return [
                'status'=>'failed',
                'message'=>'Unrecognized Request'
            ];

        if($order->is_instant){
            return $this->rescheduleHomeTherapyBooking($request, $order,$booking_id,$user);
        }else{
            if($order->details[0]->clinic_id){
                return $this->rescheduleClinicTherapyBooking($request, $order,$booking_id,$user);
            }else{
                return $this->rescheduleHomeTherapyBooking($request, $order,$booking_id,$user);
            }
        }
    }


    private function rescheduleHomeTherapyBooking(Request $request,$order, $booking_id, $user){
        $slot=DailyBookingsSlots::find($request->slot_id);
        if(!$slot)
            return [
                'status'=>'failed',
                'message'=>'Invalid Request'
            ];

        $booking=HomeBookingSlots::with('timeslot')
                ->where('status', 'pending')
               ->where('order_id', $order->id)
               ->find($booking_id);
           if(!$booking)
               return [
                   'status'=>'failed',
                   'message'=>'Booking Cannot Be Rescheduled'
               ];

           if($booking->is_instant){

               return [
                   'status'=>'failed',
                   'message'=>'Instant Booking Cannot Be Rescheduled.'
               ];

               if($booking->date > date('Y-m-d')){

                   RescheduleRequest::where('order_id', $order->id)
                       ->where('is_paid', 0)->delete();

                   RescheduleRequest::create([
                       'refid'=>env('MACHINE_ID').time(),
                       'order_id'=>$order->id,
                       'booking_id'=>$booking_id,
                       'new_slot_id'=>$request->slot_id,
                       'new_slot_time'=>$slot->internal_start_time,
                       'new_slot_date'=>$slot->date,
                       'total_cost'=>200
                   ]);

                   return [
                       'status'=>'success',
                       'data'=>[
                           'payment_status'=>'no',
                           'header'=>'Payment For Booking Reschedule',
                           'old_time'=>$booking->date.' Instant Booking',
                           'new_time'=>$slot->date.' '.$slot->start_time,
                           'amount'=>'20% deduction',
                           'wallet_balance'=>Wallet::balance($user->id)
                       ]
                   ];
               }else{

                   $booking->slot_id=$slot->id;
                   $booking->is_instant=0;
                   $booking->save();

                   $order->is_instant=0;
                   $order->save();

                   return [
                       'status'=>'success',
                       'date'=>[
                           'payment_status'=>'yes',
                           'header'=>'Booking Reschedule Successfull',
                           'old_time'=>'',
                           'new_time'=>'',
                           'amount'=>''
                       ]
                   ];
               }
           }else{
//               if(date('Y-m-d H:i:s', strtotime('+2 hours')) > $booking->timeslot->date.' '.$booking->internal_start_time){
//
//                   RescheduleRequest::where('order_id', $order->id)
//                       ->where('is_paid', 0)->delete();
//
//                   RescheduleRequest::create([
//                       'refid'=>env('MACHINE_ID').time(),
//                       'order_id'=>$order->id,
//                       'booking_id'=>$booking_id,
//                       'old_slot_id'=>$booking->slot_id,
//                       'new_slot_id'=>$request->slot_id,
//                       'new_slot_time'=>$slot->internal_start_time,
//                       'new_slot_date'=>$slot->date,
//                       'total_cost'=>200
//                   ]);
//
//                   return [
//                       'status'=>'success',
//                       'data'=>[
//                           'payment_status'=>'no',
//                           'header'=>'Payment For Booking Reschedule',
//                           'old_time'=>$booking->timeslot->date.' '.$booking->timeslot->start_time,
//                           'new_time'=>$slot->date.' '.$slot->start_time,
//                           'amount'=>'20% deduction',
//                           'wallet_balance'=>Wallet::balance($user->id)
//                       ]
//                   ];
//               }else{
//
//                   $booking->slot_id=$slot->id;
//                   $booking->save();
//
//                   return [
//                       'status'=>'success',
//                       'date'=>[
//                           'payment_status'=>'yes',
//                           'header'=>'Booking Reschedule Successfull',
//                           'old_time'=>'',
//                           'new_time'=>'',
//                           'amount'=>''
//                       ]
//                   ];
//               }
               if((strtotime($booking->date.' '.$booking->time) - strtotime('now')) > 60*60*24){
                   $booking->slot_id=$slot->id;
                   $booking->save();

                   return [
                       'status'=>'success',
                       'date'=>[
                           'payment_status'=>'yes',
                           'header'=>'Booking Reschedule Successfull',
                           'old_time'=>'',
                           'new_time'=>'',
                           'amount'=>''
                       ]
                   ];

               }

               return [
                   'status'=>'failed',
                   'message'=>'Bookings can be rescheduled before 24 hours only.'
               ];

           }

    }

    private function rescheduleClinicTherapyBooking(Request $request,$order, $booking_id, $user){
        $slot=TimeSlot::find($request->slot_id);
        if(!$slot)
            return [
                'status'=>'failed',
                'message'=>'Invalid Request'
            ];

        $booking=BookingSlot::with('timeslot')
            ->where('status', 'pending')
            ->where('order_id', $order->id)
            ->find($booking_id);

        if(!$booking)
            return [
                'status'=>'failed',
                'message'=>'Booking Cannot Be Rescheduled'
            ];

//        if(date('Y-m-d H:i:s', strtotime('+2 hours')) > $booking->timeslot->date.' '.$booking->internal_start_time){
//
//            RescheduleRequest::where('order_id', $order->id)
//                ->where('is_paid', 0)->delete();
//
//            RescheduleRequest::create([
//                'refid'=>env('MACHINE_ID').time(),
//                'order_id'=>$order->id,
//                'booking_id'=>$booking_id,
//                'old_slot_id'=>$booking->slot_id,
//                'new_slot_id'=>$request->slot_id,
//                'new_slot_time'=>$slot->internal_start_time,
//                'new_slot_date'=>$slot->date,
//                'total_cost'=>200
//            ]);
//
//            return [
//                'status'=>'success',
//                'data'=>[
//                    'payment_status'=>'no',
//                    'header'=>'Payment For Booking Reschedule',
//                    'old_time'=>$booking->timeslot->date.' '.$booking->timeslot->start_time,
//                    'new_time'=>$slot->date.' '.$slot->start_time,
//                    'amount'=>'20% deduction',
//                    'wallet_balance'=>Wallet::balance($user->id)
//                ]
//            ];
//        }else{
//
//            $booking->slot_id=$slot->id;
//            $booking->save();
//
//            return [
//                'status'=>'success',
//                'date'=>[
//                    'date'=>[
//                        'payment_status'=>'yes',
//                        'header'=>'Booking Reschedule Successfull',
//                        'old_time'=>'',
//                        'new_time'=>'',
//                        'amount'=>''
//                    ]
//                ]
//            ];
//        }

        if((strtotime($booking->date.' '.$booking->time) - strtotime('now')) > 60*60*24){
            $booking->slot_id=$slot->id;
            $booking->save();

            return [
                'status'=>'success',
                'date'=>[
                    'payment_status'=>'yes',
                    'header'=>'Booking Reschedule Successfull',
                    'old_time'=>'',
                    'new_time'=>'',
                    'amount'=>''
                ]
            ];

        }

        return [
            'status'=>'failed',
            'message'=>'Bookings can be rescheduled before 24 hours only.'
        ];

    }


    /*
     * Cancellation Of Complete Order Sessions Or Product Purchase
     */
    public function cancelAll(Request $request, $order_id){
        $user=$request->user;

        $order=Order::with(['details', 'bookingslots', 'homebookingslots'])
            ->where('user_id', $user->id)
            ->find($order_id);
        if(!$order)
            return [
                'status'=>'failed',
                'message'=>'No record Found'
            ];

        if(!in_array($order->status, ['confirmed']))
            return [
                'status'=>'failed',
                'message'=>'Booking Cannot Be Cancelled Now'
            ];


        if($order->details[0]->entity instanceof Product)
            return $this->cancelProductsBooking($order);

        if($order->details[0]->entity instanceof Therapy){
            if($order->is_instant)
                return $this->cancelAllInstantTherapy($order);
            else{
                if($order->details[0]->clinic_id)
                    return $this->cancelAllClinicTherapy($order);
                else
                    return $this->cancelAllHomeTherapy($order);

            }
        }
    }

    private function cancelProductsBooking($order){

        $product_cancellation_status=[
            'confirmed'
        ];

        if(!in_array($order->status, $product_cancellation_status)){
            return [
                'status'=>'failed',
                'message'=>'Order cannot be cancelled now'
            ];
        }

        $order->status='cancelled';
        $order->save();

        Wallet::updatewallet($order->user_id, 'Refund for Cancellation of Order ID: '.$order->refid, 'Credit', $order->total_cost, 'CASH', $order->refid);

        return [
            'status'=>'success',
            'message'=>'Order has been cancelled. Refund process will be initiated shortly'
        ];

    }

    private function cancelAllInstantTherapy($order){
        $booking=$order->homebookingslots[0];
        $booking->status='cancelled';
        $booking->save();
        $order->status='cancelled';
        $order->save();

        /*
         * Put Deduction Calculation Here
         */
        Wallet::updatewallet($order->user_id, 'Refund for Cancellation of Order ID: '.$order->refid, 'Credit', $booking->price, 'CASH', $order->refid);


        return [
            'status'=>'success',
            'message'=>'Your Booking Has Been Cancelled'
        ];
    }

    private function cancelAllClinicTherapy($order){
        $bookings=$order->bookingSlots;

        $refund=0;

        foreach($bookings as $booking){
            if($booking->status=='pending'){
                if((strtotime($booking->date.' '.$booking->time) - strtotime('now')) < 60*60*6){
                    return [
                        'status'=>'failed',
                        'message'=>'Few booking from this order cannot be cancelled now. Please cancel bookings individually'
                    ];
                }
            }
        }


        foreach($bookings as $booking){
            if($booking->status=='pending'){
                $booking->status='cancelled';
                $booking->save();

                if((strtotime($booking->date.' '.$booking->time) - strtotime('now')) > 60*60*24){
                    // before 24 hours full refund
                    $refund=$refund+$booking->price;
                }else if((strtotime($booking->date.' '.$booking->time) - strtotime('now')) > 60*60*12){
                    //before 12 hours
                    $refund=$refund+round($booking->price*90/100);
                }else if((strtotime($booking->date.' '.$booking->time) - strtotime('now')) > 60*60*6){
                    //before 6 hours
                    $refund=$refund+round($booking->price*80/100);
                }else{
                    $refund=0;
                }

            }
        }

        $order->status='cancelled';
        $order->save();

        Wallet::updatewallet($order->user_id, 'Booking Cancellation For Order ID: '.$order->refid, 'Credit', $refund, 'CASH', $order->refid);

        return [
            'status'=>'success',
            'message'=>'Your Booking Has Been Cancelled'
        ];

    }

    private function cancelAllHomeTherapy($order){
        $bookings=$order->homebookingslots;

        $refund=0;

        foreach($bookings as $booking){
            if($booking->status=='pending'){
                if((strtotime($booking->date.' '.$booking->time) - strtotime('now')) < 60*60*6){
                    return [
                        'status'=>'failed',
                        'message'=>'Few booking from this order cannot be cancelled now. Please cancel bookings individually'
                    ];
                }
            }
        }


        foreach($bookings as $booking){
            if($booking->status=='pending'){
                $booking->status='cancelled';
                $booking->save();

                if((strtotime($booking->date.' '.$booking->time) - strtotime('now')) > 60*60*24){
                    // before 24 hours full refund
                    $refund=$refund+$booking->price;
                }else if((strtotime($booking->date.' '.$booking->time) - strtotime('now')) > 60*60*12){
                    //before 12 hours
                    $refund=$refund+round($booking->price*90/100);
                }else if((strtotime($booking->date.' '.$booking->time) - strtotime('now')) > 60*60*6){
                    //before 6 hours
                    $refund=$refund+round($booking->price*80/100);
                }else{
                    $refund=0;
                }
            }
        }


//        foreach($bookings as $booking){
//            if($booking->status=='pending'){
//                $booking->status='cancelled';
//                $booking->save();
//            }
//        }

        $order->status='cancelled';
        $order->save();

        Wallet::updatewallet($order->user_id, 'Booking Cancellation For Order ID: '.$order->refid, 'Credit', $refund, 'CASH', $order->refid);

        return [
            'status'=>'success',
            'message'=>'Your Booking Has Been Cancelled'
        ];
    }


    //    private function cancelTherapyBooking($order){
//
//
//
//        $therapy_cancellation_status=[
//            'confirmed'
//        ];
//
//        if(!in_array($order->status, $therapy_cancellation_status)){
//            return [
//                'status'=>'failed',
//                'message'=>'Order cannot be cancelled now'
//            ];
//        }
//
//        $order->status='cancelled';
//        $order->save();
//        return [
//            'status'=>'success',
//            'message'=>'Your booking has been cancelled. Refund process will be initiated shortly'
//        ];
//
//    }

    //    public function initiateClinicBooking(Request $request){
//
//        $request->validate([
//            'clinic_id'=>'required|integer',
//            'therapy_id'=>'required|integer',
//            'num_sessions'=>'required|integer',
//            'grade'=>'required|integer|in:1,2,3,4',
//            'time'=>'required|date_format:H:i',
//            'date'=>'required|date_format:Y-m-d',
//        ]);
//
//        $clinic=Clinic::active()->with(['therapies'=>function($therapies)use($request){
//            $therapies->where('therapies.isactive', true)->where('therapies.id', $request->therapy_id);
//        }])->find($request->clinic_id);
//
//        if(!$clinic || empty($clinic->therapies)){
//            return [
//                'status'=>'failed',
//                'message'=>'Invalid Operation Performed'
//            ];
//        }
//
//        //return $clinic;
//        $grade=$request->grade??1;
//        $num_sessions=$request->num_sessions??1;
//
//        switch($grade){
//            case 1:$cost=($clinic->therapies[0]->pivot->grade1_price??0);
//                break;
//            case 2:$cost=($clinic->therapies[0]->pivot->grade2_price??0);
//                break;
//            case 3:$cost=($clinic->therapies[0]->pivot->grade3_price??0);
//                break;
//            case 4:$cost=($clinic->therapies[0]->pivot->grade4_price??0);
//                break;
//        }
//
//        $refid=env('MACHINE_ID').time();
//        $order=Order::create([
//            'user_id'=>auth()->guard('customerapi')->user()->id,
//            'refid'=>$refid,
//            'status'=>'pending',
//            'total_cost'=>$cost*$num_sessions,
//            'booking_date'=>$request->date,
//            'booking_time'=>$request->time
//        ]);
//        OrderStatus::create([
//            'order_id'=>$order->id,
//            'current_status'=>$order->status
//        ]);
//        OrderDetail::create([
//            'order_id'=>$order->id,
//            'entity_type'=>'App\Models\Therapy',
//            'entity_id'=>$clinic->therapies[0]->id,
//            'clinic_id'=>$clinic->id,
//            'cost'=>$cost,
//            'quantity'=>$num_sessions,
//            'grade'=>$request->grade
//        ]);
//
//        return [
//            'status'=>'success',
//            'data'=>[
//                'order_id'=>$order->id
//            ]
//        ];
//    }

}
