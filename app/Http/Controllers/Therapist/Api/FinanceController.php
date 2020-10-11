<?php

namespace App\Http\Controllers\Therapist\Api;

use App\Models\HomeBookingSlots;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    public function index(Request $request){

        $user=$request->user;

        $datefrom=$request->datefrom??'';
        $dateto=$request->dateto??'';
        $type=$request->type??'daily';

        $bookings=[];
        $total_count=0;
        $total_cost=0;

        $bookingsobj=HomeBookingSlots::where('status', 'completed')
            ->where('assigned_therapist', $user->id);

        switch($type){

            case 'daily':
                $bookingsobj=$bookingsobj
                    ->groupBy('date')
                    ->orderBy('date', 'desc')
                    ->selectRaw('count(*) as count, sum(price) as price, date')
                    ->get();
                foreach($bookingsobj as $booking){
                    $bookings[]=[
                        'name'=>date('d/m/Y', strtotime($booking->date)),
                        'count'=>$booking->count,
                        'price'=>$booking->price
                    ];
                    $total_cost=$total_cost+$booking->price;
                    $total_count=$total_count+$booking->count;
                }
                break;
            case 'monthly':

                $bookingsobj=$bookingsobj
                    ->groupBy(DB::raw('YEAR(date)'), DB::raw('MONTH(date)') , DB::raw('monthname(date)') )
                    ->orderBy('year', 'desc')
                    ->orderBy(DB::raw('MONTH(date)'), 'desc')
                    ->selectRaw('count(*) as count, sum(price) as price, monthname(date) as month, YEAR(date) as year')
                    ->get();
                foreach($bookingsobj as $booking){
                    $bookings[]=[
                        'name'=>$booking->month.' '.$booking->year,
                        'count'=>$booking->count,
                        'price'=>$booking->price
                    ];
                    $total_cost=$total_cost+$booking->price;
                    $total_count=$total_count+$booking->count;
                }
                break;
            case 'annually':
                $bookingsobj=$bookingsobj
                    ->groupBy(DB::raw('YEAR(date) as year'))
                    ->orderBy('year', 'desc')
                    ->selectRaw('count(*) as count, sum(price) as price, YEAR(date) as year')
                    ->get();
                foreach($bookingsobj as $booking){
                    $bookings[]=[
                        'name'=>$booking->month.' '.$booking->year,
                        'count'=>$booking->count,
                        'price'=>$booking->price
                    ];
                    $total_cost=$total_cost+$booking->price;
                    $total_count=$total_count+$booking->count;
                }
                break;

        }

        return [

            'status'=>'success',
            'data'=>compact('bookings', 'datefrom', 'dateto', 'type', 'total_cost', 'total_count')

        ];

    }
}
