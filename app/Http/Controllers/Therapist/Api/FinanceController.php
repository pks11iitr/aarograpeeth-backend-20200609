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

        $bookings=HomeBookingSlots::where('status', 'completed')
            ->where('therapist_id', $user->id);

        switch($request->type){

            case 'daily':
                $bookingsobj=$bookings
                    ->groupBy('date')
                    ->orderBy('date', 'desc')
                    ->selectRaw('count(*) as count, sum(price) as price, date')
                    ->get();
                $bookings=[];
                $total_count=0;
                $total_cost=0;
                foreach($bookingsobj as $booking){
                    $bookings[]=[
                        'name'=>date('d/m/Y', strtotime($booking->date)),
                        'count'=>$booking->count,
                        'price'=>$booking->price
                    ];
                    $total_cost=$total_cost+$booking->price;
                    $total_count=$total_count+$booking->count;
                }
            case 'monthly':

                $bookingsobj=$bookings
                    ->groupBy(DB::raw('YEAR(date) as year'), DB::raw('MONTH(date) as m1') , DB::raw('monthname(date) as month') )
                    ->orderBy('year', 'desc')
                    ->orderBy(DB::raw('MONTH(date) as m1'), 'desc')
                    ->selectRaw('count(*) as count, sum(price) as price, monthname(date) as month, YEAR(date) as year')
                    ->get();
                $bookings=[];
                $total_count=0;
                $total_cost=0;
                foreach($bookingsobj as $booking){
                    $bookings[]=[
                        'name'=>$booking->month.' '.$booking->year,
                        'count'=>$booking->count,
                        'price'=>$booking->price
                    ];
                    $total_cost=$total_cost+$booking->price;
                    $total_count=$total_count+$booking->count;
                }

            case 'annually':
                $bookingsobj=$bookings
                    ->groupBy(DB::raw('YEAR(date) as year'))
                    ->orderBy('year', 'desc')
                    ->selectRaw('count(*) as count, sum(price) as price, YEAR(date) as year')
                    ->get();
                $bookings=[];
                $total_count=0;
                $total_cost=0;
                foreach($bookingsobj as $booking){
                    $bookings[]=[
                        'name'=>$booking->month.' '.$booking->year,
                        'count'=>$booking->count,
                        'price'=>$booking->price
                    ];
                    $total_cost=$total_cost+$booking->price;
                    $total_count=$total_count+$booking->count;
                }

        }

        return [

            'status'=>'success',
            'data'=>compact('bookings', 'datefrom', 'dateto', 'type', 'total_cost', 'total_count')

        ];

    }
}
