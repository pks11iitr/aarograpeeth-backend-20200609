<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StaticPagesController extends Controller
{
    public function aboutus(Request $request){

        return view('staticpages.about');

    }

    public function terms(Request $request){

        return view('staticpages.t-n-c');

    }

    public function privacy(Request $request){

        return view('staticpages.privacy');

    }

}
