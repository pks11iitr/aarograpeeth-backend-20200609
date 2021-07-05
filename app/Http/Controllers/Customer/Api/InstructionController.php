<?php

namespace App\Http\Controllers\Customer\Api;

use App\Models\Configuration;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class InstructionController extends Controller
{
    public function instructions(Request $request){
        $configuration=Configuration::where('param', 'customer_does_dont')->first();
        return view('customer-instructions', compact('configuration'));
    }
}
