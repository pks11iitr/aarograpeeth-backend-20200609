<?php

namespace App\Http\Controllers\Customer\Api;

use App\Models\BookingSlot;
use App\Models\Clinic;
use App\Models\HomeBookingSlots;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\Therapist;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReviewController extends Controller
{
    public function index(Request $request, $type, $id){

        switch($type){
            case 'product':$entity=Product::active()->with(['comments.customer'])->find($id);
            break;
            case 'clinic':$entity=Clinic::active()->with('comments')->find($id);
            break;
            case 'therapy':$entity=Product::active()->with('comments')->find($id);
            break;
            default: $entity=null;
        }

        if(!$entity || !$entity->comments)
            return [
                'status'=>'failed',
                'message'=>'No reviews found'
            ];

        return [
            'status'=>'success',
            'data'=>[
                'reviews'=>$entity->comments
            ]
        ];

    }

    public function post(Request $request, $order_id, $item_id){

        $request->validate([
            'rating'=>'required|in:1,2,3,4,5',
            'review'=>'string|max:250'
        ]);

        $user=$request->user;

        $order=Order::with(['details'=>function($details) use($item_id){
            $details->where('entity_id', $item_id);
        }])->where('user_id', $user->id)
            ->where('status', 'completed')
            ->find($order_id);

        if(!$order || empty($order->details->toArray()))
            return [
                'status'=>'failed',
                'message'=>'No Such Record Found'
            ];

        $review=Review::where('order_id', $order_id)
            ->where('entity_type', $order->details[0]->entity_type)
            ->where('entity_id', $item_id)
            ->first();

        if(!$review){

            Review::create([
               'user_id'=>$user->id,
               'order_id'=>$order->id,
               'entity_type'=>$order->details[0]->entity_type,
                'entity_id'=>$order->details[0]->entity_id,
                'rating'=>$request->rating,
                'description'=>$request->review
            ]);

            if($order->details[0]->clinic){
                Review::create([
                    'user_id'=>$user->id,
                    'order_id'=>$order->id,
                    'entity_type'=>'App\Models\Clinic',
                    'entity_id'=>$order->details[0]->clinic_id,
                    'rating'=>$request->clinic_rating,
                    'description'=>$request->clinic_review
                ]);
            }


            return [
                'status'=>'success',
                'message'=>'Your Review Has Been Submitted'
            ];
        }else{
            return [
                'status'=>'failed',
                'message'=>'Already Reviewed'
            ];
        }

    }

    public function postSessionReview(Request $request, $order_id, $session_id){

        $request->validate([
            'rating'=>'required|in:1,2,3,4,5',
            'review'=>'string|max:250',
            'result'=>'required|in:1,2,3,4'
        ]);

        $user=$request->user;

        $order=Order::with(['details'])
            ->where('user_id', $user->id)
            ->where('status', '!=', 'pending')
            ->find($order_id);

        if(!$order || empty($order->details->toArray()))
            return [
                'status'=>'failed',
                'message'=>'No Such Record Found'
            ];

        if($order->details[0]->entity_type=='App\Models\Therapy'){
            if($order->details[0]->clinic_id!=null){
                $session=BookingSlot::where('order_id', $order_id)
                    ->where('status', 'completed')
                    ->find($session_id);

                $rating_for='App\Models\User';

                $therapist=User::find($session->assigned_therapist);

            }else{
                $session=HomeBookingSlots::where('order_id', $order_id)
                    ->where('status', 'completed')
                ->find($session_id);

                $rating_for='App\Models\Therapist';

                $therapist=Therapist::find($session->assigned_therapist);
            }
        }

        if(!$session)
            return [
                'status'=>'failed',
                'message'=>'No Record Found'
            ];

        $session->customer_result=$request->result;
        $session->save();

        $review=Review::where('order_id', $order_id)
            ->where('session_id', $session_id)
            ->first();

        if(!$review){

            Review::create([
                'user_id'=>$user->id,
                'order_id'=>$order->id,
                'session_id'=>$session->id,
                'entity_type'=>$rating_for,
                'entity_id'=>$therapist->id,
                'rating'=>$request->rating,
                'description'=>$request->review
            ]);

            return [
                'status'=>'success',
                'message'=>'Your Review Has Been Submitted'
            ];
        }else{
            return [
                'status'=>'failed',
                'message'=>'Already Reviewed'
            ];
        }

    }

}
