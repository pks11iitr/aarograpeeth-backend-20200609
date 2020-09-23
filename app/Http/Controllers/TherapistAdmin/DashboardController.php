<?php

namespace App\Http\Controllers\TherapistAdmin;

use App\Models\BookingSlot;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index(Request $request){

        $completed=BookingSlot::where('status', 'completed')
            ->count();
        $inprocess=BookingSlot::where('status', '!=', 'completed')
            ->count();

        return view('therapistadmin.home', compact('completed', 'inprocess'));
    }
}
