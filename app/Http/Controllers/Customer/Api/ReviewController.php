<?php

namespace App\Http\Controllers\Customer\Api;

use App\Models\Clinic;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
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
        $user=auth()->guard('customerapi')->user();
        if(!$user)
            return [
                'status'=>'failed',
                'message'=>'Please login to continue'
            ];

        $request->validate([
            'rating'=>'required|in:1,2,3,4,5',
            'review'=>'string|max:250'
        ]);

        $order=Order::with(['details'=>function($details) use($item_id){
            $details->where('entity_id', $item_id);
        }])->where('user_id', $user->id)
            ->where('status', 'completed')
            ->find($order_id);

        if(!$order || !empty($order->details->toArray()))
            return [
                'status'=>'failed',
                'message'=>'No Such Record Found'
            ];

        $review=Review::where('order_id', $order_id)
            ->where('entity_id', $item_id)
            ->first();

        if(!$review){

            Review::create([
               'user_id'=>$user->id,
               'order_id'=>$order->id,
               'entity_type'=>$order->details[0]->entity_type,
                'entity_id'=>$order->details[0]->entity_id
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
