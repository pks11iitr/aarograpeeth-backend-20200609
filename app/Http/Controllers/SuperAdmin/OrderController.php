<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    public function index(Request $request){

		$orders=Order::with(['details.entity', 'customer', 'details.clinic'])
            ->where('status','!=', 'pending')
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

		$orders=Order::with(['details.entity', 'customer', 'details.clinic'])->where(function($orders) use($request){
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
        return view('admin.order.product', compact('orders'));

    }

    public function details(Request $request, $id){
        $order=Order::with(['details.entity', 'customer'])->where('status', '!=', 'pending')->find($id);
        return view('admin.order.details', compact('order'));
    }

}
