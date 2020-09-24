<?php

namespace App\Http\Controllers\ClinicAdmin;

use App\Models\BookingSlot;
use App\Models\Clinic;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request){

        $user=auth()->user();

        $completed=BookingSlot::where('status', 'completed')
            ->count();
        $completed_amount=BookingSlot::where('status', 'completed')
            ->sum('price');
        $inprocess=BookingSlot::where('status', '!=', 'completed')
            ->count();
        $inprocess_amount=BookingSlot::where('status', '!=', 'completed')
            ->count();

        return view('clinicadmin.home', compact('completed', 'inprocess', 'completed_amount', 'inprocess_amount'));
    }
}
