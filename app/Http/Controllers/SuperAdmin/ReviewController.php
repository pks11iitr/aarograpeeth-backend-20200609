<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\Review;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReviewController extends Controller
{
    public function index(Request $request){
        $reviews= Review::paginate(10);
        return view('admin.review.view',['reviews'=>$reviews]);
    }

    public function status(Request $request,$id,$isactive){
        // var_dump($id);
        //  var_dump($isactive);die();
        $reviews =Review::where('id',$id)->get();
        foreach($reviews as $key=>$review){
            $review->isactive =$isactive;
            $review->save();
        }

        return redirect()->back()->with('success', 'Review has been updated');
    }
}
