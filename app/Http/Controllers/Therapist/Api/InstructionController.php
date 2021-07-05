<?php

namespace App\Http\Controllers\Therapist\Api;

use App\Models\Configuration;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class InstructionController extends Controller
{
    public function instructions(Request $request){
        $configuration=Configuration::whereIn('param', ['therapist_does_dont', 'what_to_do_if_pain_increase'])->get();
        return view('therapist-instructions', compact('configuration'));
    }
}
