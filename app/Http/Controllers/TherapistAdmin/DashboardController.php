<?php

namespace App\Http\Controllers\TherapistAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index(Request $request){
        return view('therapistadmin.home');
    }
}
