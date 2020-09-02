<?php

namespace App\Http\Controllers\ClinicAdmin;

use App\Models\Clinic;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request){

        $user=auth()->user();
        $clinic =Clinic::where('user_id',$user->id)->first();
        $therapy_orders = Order::whereHas('details', function ($details) use ($clinic) {
            $details->where('clinic_id', $clinic->id);
        })
            ->where('status', '!=', 'pending')
            ->groupBy('status')
            ->selectRaw('count(*) as count, status')
            ->get();
        $therapy_orders_array=[];
        $total_order=0;
        foreach($therapy_orders as $o){
            if(isset($therapy_orders_array[$o->status]))
                $therapy_orders_array[$o->status]=0;
            $therapy_orders_array[$o->status]=$o->count;
            $total_order=$total_order+$o->count??0;
        }

        $therapy_orders_array['total']=$total_order;

        $user=auth()->user();
        $clinic =Clinic::where('user_id',$user->id)->first();
        $revenue_therapy = Order::whereHas('details', function ($details) use ($clinic) {
            $details->where('clinic_id', $clinic->id);
        })->where('status', 'confirmed')->sum('total_cost');

        $therapy_orders_data=Order::where('status', 'confirmed')
            ->where('created_at', '>=', date('Y').'-01-01 00:00:00')
            ->whereHas('details', function ($details) use ($clinic) {
                $details->where('clinic_id', $clinic->id);
            })
            ->select(DB::raw('Month(created_at) as month'), DB::raw('SUM(total_cost) as total_cost'))
            ->groupBy(DB::raw('Month(created_at)'))
            ->orderBy(DB::raw('Month(created_at)'), 'asc')
            ->get();

        $therapy_sales=[];
        foreach($therapy_orders_data as $d){
            $therapy_sales[$d->month]=$d->total_cost;
        }
        //var_dump($therapy_orders_data->toArray());die;

        $revenue=[];
        $revenue['therapy']=$revenue_therapy;
        $revenue['total']=$revenue_therapy;

        return view('clinicadmin.home',['therapy'=>$therapy_orders_array,'revenue'=>$revenue]);
    }
}
