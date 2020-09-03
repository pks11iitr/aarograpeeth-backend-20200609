<?php

namespace App\Http\Controllers\Customer\Api;

use App\Events\OrderConfirmed;
use App\Events\OrderSuccessfull;
use App\Events\RescheduleConfirmed;
use App\Models\BookingSlot;
use App\Models\Cart;
use App\Models\HomeBookingSlots;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\RescheduleRequest;
use App\Models\Therapy;
use App\Models\Wallet;
use App\Services\Payment\RazorPayService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PaymentController extends Controller
{
    public function __construct(RazorPayService $pay)
    {
        $this->pay=$pay;
    }

    public function initiatePayment(Request $request, $id){
        $user=auth()->guard('customerapi')->user();
        if(!$user)
            return [
                'status'=>'failed',
                'message'=>'Please login to continue'
            ];


        $order=Order::with('details.entity')->where('user_id', $user->id)->where('status', 'pending')->find($id);

        if(!$order)
            return [
                'status'=>'failed',
                'message'=>'Invalid Operation Performed'
            ];


        if($order->details[0]->entity_type=='App\Models\Therapy'){

            if($order->details[0]->clinic_id){
                if($order->bookingSlots()->where('status', 'pending')->count()==0){
                    if(!$order)
                        return [
                            'status'=>'failed',
                            'message'=>'Please Select Booking Schedule To Continue'
                        ];
                }
            }else{
                if($order->homebookingslots()->where('status', 'pending')->count()==0){
                    if(!$order)
                        return [
                            'status'=>'failed',
                            'message'=>'Please Select Booking Schedule To Continue'
                        ];
                }
            }
        }

        // set to initial state
        $order->update([
            'use_balance'=>false,
            'use_points'=>false,
            'points_used'=>0,
            'balance_used'=>0,
        ]);



        if($request->use_points==1) {
            $result=$this->payUsingPoints($order);
            if($result['status']=='success'){

                event(new OrderConfirmed($order));

                return [
                    'status'=>'success',
                    'message'=>'Congratulations! Your order at Arogyapeeth is successful',
                    'data'=>[
                        'payment_done'=>'yes',
                        'ref_id'=>$order->refid,
                        'order_id'=>$order->id
                    ]
                ];
            }
        }

        if($request->use_balance==1) {
            $result=$this->payUsingBalance($order);
            if($result['status']=='success'){

                event(new OrderConfirmed($order));

                return [
                    'status'=>'success',
                    'message'=>'Congratulations! Your order at Arogyapeeth is successful',
                    'data'=>[
                        'payment_done'=>'yes',
                        'ref_id'=>$order->refid,
                        'order_id'=>$order->id
                    ]
                ];
            }

        }

        $result=$this->initiateGatewayPayment($order);

        return $result;

    }

    private function initiateGatewayPayment($order){
        $response=$this->pay->generateorderid([
            "amount"=>($order->total_cost-$order->balance_used)*100,
            "currency"=>"INR",
            "receipt"=>$order->refid,
        ]);
        $responsearr=json_decode($response);
        //var_dump($responsearr);die;
        if(isset($responsearr->id)){
            $order->order_id=$responsearr->id;
            $order->order_id_response=$response;
            $order->save();
            return [
                'status'=>'success',
                'message'=>'success',
                'data'=>[
                    'payment_done'=>'no',
                    'razorpay_order_id'=> $order->order_id,
                    'total'=>($order->total_cost-$order->balance_used)*100,
                    'email'=>$order->email,
                    'mobile'=>$order->mobile,
                    'description'=>'Order Booking at Aarogyapeeth',
                    'name'=>$order->name,
                    'currency'=>'INR',
                    'merchantid'=>$this->pay->merchantkey,
                ],
            ];
        }else{
            return [
                'status'=>'failed',
                'message'=>'Payment cannot be initiated',
                'data'=>[
                ],
            ];
        }
    }

    private function payUsingBalance($order){

            $walletbalance=Wallet::balance($order->user_id);
            if($walletbalance<=0)
                return [
                    'status'=>'failed',
                    'remaining_amount'=>$order->total_cost
                ];

            if($order->total_cost <= $walletbalance) {
                $order->payment_status='paid';
                $order->status='confirmed';
                $order->use_balance=true;
                $order->balance_used=$order->total_cost;
                $order->payment_mode='online';
                $order->save();

                OrderStatus::create([
                    'order_id'=>$order->id,
                    'current_status'=>$order->status
                ]);

                if($order->details[0]->clinic_id){
                    $order->bookingSlots()->where('status', 'pending')->update(['booking_slots.status'=>'confirmed']);
                }else{
                    $order->bookingSlots()->where('status', 'pending')->update(['home_booking_slots.status'=>'confirmed']);
                }

                Wallet::updatewallet($order->user_id, 'Paid For Order ID: '.$order->refid, 'DEBIT',$order->balance_used, 'CASH', $order->id);

                Cart::where('user_id', $order->user_id)->delete();

                return [
                    'status'=>'success',
                ];
            }else {

                $order->use_balance=true;
                $order->balance_used=$walletbalance;
                $order->payment_mode='online';
                $order->save();

                return [
                    'status'=>'failed',
                ];
            }
    }

    private function payUsingPoints($order){
        //points can be used for therapy only

        if($order->details[0]->entity instanceof Therapy){
                $walletpoints=Wallet::points($order->user_id);
                $amount=$walletpoints/env('POINTS_CONVERSION_RATE');
                if($amount>=$order->total_cost){
                    $order->payment_status='paid';
                    $order->status='confirmed';
                    $order->use_points=true;
                    $order->points_used=$order->total_cost*env('POINTS_CONVERSION_RATE');
                    $order->payment_mode='online';
                    $order->save();

                    OrderStatus::create([
                        'order_id'=>$order->id,
                        'current_status'=>$order->status
                    ]);

                    Wallet::updatewallet($order->user_id, 'Paid For Order ID: '.$order->refid, 'DEBIT',$order->points_used, 'POINT', $order->id);

                    Cart::where('user_id', $order->user_id)->delete();

                    return [
                            'status'=>'success',
                        ];
                }
            }

        return [
            'status'=>'failed'
        ];
    }


    public function verifyPayment(Request $request){

//        $request->validate([
//           'razorpay_order_id'=>'required',
//            'razorpay_signature'=>'required',
//            'razorpay_payment_id'=>'required'
//
//        ]);

        $order=Order::with('details')->where('order_id', $request->razorpay_order_id)->first();

        if(!$order || $order->status!='pending')
            return [
                'status'=>'failed',
                'message'=>'Invalid Operation Performed'
            ];

        $paymentresult=$this->pay->verifypayment($request->all());
        if($paymentresult) {
            if ($order->use_balance == true) {
                $balance = Wallet::balance($order->user_id);
                if ($balance < $order->balance_used) {
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'We apologize, Your order is not successful',
                        'errors' => [

                        ],
                    ], 200);
                }
            }
            $order->status = 'confirmed';
            $order->payment_id = $request->razorpay_payment_id;
            $order->payment_id_response = $request->razorpay_signature;
            $order->payment_status = 'paid';
            $order->payment_mode = 'online';
            $order->save();

            // comfirm slots booking
            if ($order->details[0]->entity_type == 'App\Models\Therapy'){
                if ($order->details[0]->clinic_id != null) {
                    $order->schedule()->where('bookings_slots.status', 'pending')->update(['bookings_slots.status' => 'confirmed']);
                }else if($order->is_instant==0){
                    $order->homebookingslots()->where('home_booking_slots.status', 'pending')->update(['home_booking_slots.status' => 'confirmed']);
                }
            }

            OrderStatus::create([
                'order_id'=>$order->id,
                'current_status'=>$order->status
            ]);

            if($order->balance_used > 0)
                Wallet::updatewallet($order->user_id, 'Paid For Order ID: '.$order->refid, 'DEBIT',$balance, 'CASH', $order->id);

            Cart::where('user_id', $order->user_id)->delete();

            //event(new OrderSuccessfull($order));
            event(new OrderConfirmed($order));
            return [
                'status'=>'success',
                'message'=> 'Congratulations! Your order at Arogyapeeth is successful',
                'data'=>[
                    'ref_id'=>$order->refid,
                    'order_id'=>$order->id
                ]
            ];
        }else{
            return [
                'status'=>'failed',
                'message'=>'We apologize, Your payment cannot be verified',
                'data'=>[

                ],
            ];
        }
    }

    public function initiateReschedulePayment(Request $request, $order_id, $booking_id){

        $user=$request->user;

        $order=Order::with(['details'])->where('user_id', $user->id)
            ->find($order_id);

        if($order->details[0]->entity_type!='App\Models\Therapy')
            return [
                'status'=>'failed',
                'message'=>'Unreognized Request'
            ];
        if($order->details[0]->clinic_id)
            $booking=BookingSlot::where('order_id', $order_id)
                ->where('status', 'confirmed')->find($booking_id);
        else
            $booking=HomeBookingSlots::where('order_id', $order_id)
                ->where('status', 'confirmed')->find($booking_id);

        if(!$booking)
            return [
                'status'=>'failed',
                'message'=>'Unreognized Request'
            ];

        /*
         * Calculate Amount Here
         */
        $amount=200;

        $reschedule_request=RescheduleRequest::where('order_id', $order->id)
            ->where('booking_id', $booking->id)
            ->where('is_paid', false)
            ->first();


        if($request->use_balance==1) {
            $result=$this->scheduleUsingWallet($order, $reschedule_request, $booking);
            if($result['status']=='success'){

                event(new RescheduleConfirmed($order, $user));

                return [
                    'status'=>'success',
                    'message'=>'Congratulations! Your order at Arogyapeeth is successful',
                    'data'=>[
                        'payment_done'=>'yes',
                        'ref_id'=>$reschedule_request->refid,
                        'order_id'=>$order->id
                    ]
                ];
            }
        }

        $result=$this->initiateRescheduleGatewayPayment($order, $reschedule_request, $booking);

        return $result;

    }

    private function scheduleUsingWallet($order, $reschedule_request, $booking){

        $walletbalance=Wallet::balance($order->user_id);
        if($walletbalance<=0)
            return [
                'status'=>'failed',
                'remaining_amount'=>$order->total_cost
            ];

        if($reschedule_request->total_cost <= $walletbalance) {
            $reschedule_request->is_paid=true;
            $reschedule_request->use_balance=true;
            $reschedule_request->balance_used=$order->total_cost;
            $reschedule_request->save();

            Wallet::updatewallet($order->user_id, 'Paid For Booking Reschedule  with Order ID: '.$order->refid, 'DEBIT',$reschedule_request->balance_used, 'CASH', $order->id);

            return [
                'status'=>'success',
            ];
        }else {

            $reschedule_request->use_balance=true;
            $reschedule_request->balance_used=$walletbalance;
            $reschedule_request->save();

            return [
                'status'=>'failed',
            ];
        }
    }

    public function initiateRescheduleGatewayPayment($order, $reschedule_request, $booking){
        $response=$this->pay->generateorderid([
            "amount"=>($reschedule_request->total_cost-$reschedule_request->balance_used)*100,
            "currency"=>"INR",
            "receipt"=>$reschedule_request->refid,
        ]);
        $responsearr=json_decode($response);
        //var_dump($responsearr);die;
        if(isset($responsearr->id)){
            $reschedule_request->razorpay_order_id=$responsearr->id;
            $reschedule_request->razorpay_order_id_response=$response;
            $reschedule_request->save();
            return [
                'status'=>'success',
                'message'=>'success',
                'data'=>[
                    'payment_done'=>'no',
                    'razorpay_order_id'=> $reschedule_request->razorpay_order_id,
                    'total'=>($reschedule_request->total_cost-$reschedule_request->balance_used)*100,
                    'email'=>$order->email,
                    'mobile'=>$order->mobile,
                    'description'=>'Reschedule Booking at Aarogyapeeth',
                    'name'=>$order->name,
                    'currency'=>'INR',
                    'merchantid'=>$this->pay->merchantkey,
                ],
            ];
        }else{
            return [
                'status'=>'failed',
                'message'=>'Payment cannot be initiated',
                'data'=>[
                ],
            ];
        }
    }

    public function verifyReschedulePayment(Request $request){

        $user=$request->user;

//        $request->validate([
//            'razorpay_order_id'=>'required',
//            'razorpay_signature'=>'required',
//            'razorpay_payment_id'=>'required'
//        ]);

        $reschedule_request=RescheduleRequest::with('order.details')
            ->where('razorpay_order_id', $request->razorpay_order_id)->first();

        if(!$reschedule_request || $reschedule_request->is_paid==1)
            return [
                'status'=>'failed',
                'message'=>'Invalid Operation Performed'
            ];



        $paymentresult=$this->pay->verifypayment($request->all());
        if($paymentresult) {
            if ($reschedule_request->use_balance == true) {
                $balance = Wallet::balance($reschedule_request->order->user_id);
                if ($balance < $reschedule_request->balance_used) {
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'We apologize, Your order is not successful',
                        'errors' => [

                        ],
                    ], 200);
                }
            }
            $reschedule_request->is_paid=1;
            $reschedule_request->payment_id = $request->razorpay_payment_id;
            $reschedule_request->payment_id_response = $request->razorpay_signature;
            $reschedule_request->save();

            // comfirm slots booking
            if ($reschedule_request->order->details[0]->entity_type == 'App\Models\Therapy'){
                if ($reschedule_request->order->details[0]->clinic_id != null) {
                    $booking=BookingSlot::find($reschedule_request->booking_id);
                    $booking->slot_id=$reschedule_request->new_slot_id;
                    $booking->save();
                }else{
                    $booking=HomeBookingSlots::find($reschedule_request->booking_id);
                    $booking->slot_id=$reschedule_request->new_slot_id;
                    $booking->is_instant=0;
                    $booking->save();
                }
            }

            if($reschedule_request->balance_used > 0)
                Wallet::updatewallet($reschedule_request->order->user_id, 'Paid For Reschedule Booking Order ID: '.$reschedule_request->order->refid, 'DEBIT',$reschedule_request->balance_used, 'CASH', $reschedule_request->order->id);

            //event(new OrderSuccessfull($order));
            event(new RescheduleConfirmed($reschedule_request->order, $user));
            return [
                'status'=>'success',
                'message'=> 'Congratulations! Your payment at Arogyapeeth is successful',
                'data'=>[
                    'ref_id'=>$reschedule_request->order->refid,
                    'order_id'=>$reschedule_request->order->id
                ]
            ];
        }else{
            return [
                'status'=>'failed',
                'message'=>'We apologize, Your payment cannot be verified',
                'data'=>[

                ],
            ];
        }
    }

}
