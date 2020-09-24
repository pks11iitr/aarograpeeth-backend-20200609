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
        $clinic=Clinic::where('user_id', $user->id)->first();
        $completed=BookingSlot::where('status', 'completed')
            ->where('clinic_id', $clinic->id)
            ->count();
        $completed_amount=BookingSlot::where('status', 'completed')
            ->where('clinic_id', $clinic->id)
            ->sum('price');
        $inprocess=BookingSlot::where('status', '!=', 'completed')
            ->where('clinic_id', $clinic->id)
            ->count();
        $inprocess_amount=BookingSlot::where('status', '!=', 'completed')
            ->where('clinic_id', $clinic->id)
            ->count();

        return view('clinicadmin.home', compact('completed', 'inprocess', 'completed_amount', 'inprocess_amount'));
    }
}
