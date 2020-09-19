<?php

namespace App\Http\Controllers\ClinicAdmin;

use App\Models\Clinic;
use App\Models\ClinicTherapy;
use App\Models\Therapy;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProfileController extends Controller
{
    public function view(Request $request){
        $user=auth()->user();
        $clinic =Clinic::with('gallery')
        ->where('user_id',$user->id)
            ->firstOrFail();
        $documents = $clinic->gallery;
        $therapys=Therapy::where('isactive',1)->get();
        $clinictherapys=ClinicTherapy::where('clinic_id',$clinic->id)->get();
        return view('clinicadmin.profile',['clinic'=>$clinic,'documents'=>$documents,'therapys'=>$therapys,'clinictherapys'=>$clinictherapys]);

    }


    public function update(Request $request){
        $request->validate([
            //'isactive'=>'required',
            'name'=>'required',
            'description'=>'required',
            'address'=>'required',
            'city'=>'required',
            'state'=>'required',
            'contact'=>'required',
            'image'=>'image'
        ]);

        $user=auth()->user();
        $clinic =Clinic::with('gallery')
            ->where('user_id',$user->id)
            ->firstOrFail();
        $clinic->update([
            //'isactive'=>$request->isactive,
            'name'=>$request->name,
            'description'=>$request->description,
            'address'=>$request->address,
            'city'=>$request->city,
            'state'=>$request->state,
            'contact'=>$request->contact,
            //'lat'=>$request->lat,
            //'lang'=>$request->lang,
        ]);

        if(!empty($request->image)){
            $clinic->saveImage($request->image, 'clinics');
        }
        if($clinic)
        {
            return redirect()->back()->with('success', 'Profile has been updated');
        }
        return redirect()->back()->with('error', 'Profile update failed');

    }

    public function therapystore(Request $request){
        $request->validate([
            'therapy_id'=>'required',
            'grade1_price'=>'required',
            'grade2_price'=>'required',
            'grade3_price'=>'required',
            'grade4_price'=>'required',
            'grade1_original_price'=>'required',
            'grade2_original_price'=>'required',
            'grade3_original_price'=>'required',
            'grade4_original_price'=>'required'
        ]);

        $user=auth()->user();
        $clinic =Clinic::with('gallery')
            ->where('user_id',$user->id)
            ->firstOrFail();

        if($clinictherapy=ClinicTherapy::create([
            'clinic_id'=>$clinic->id,
            'therapy_id'=>$request->therapy_id,
            'grade1_price'=>$request->grade1_price,
            'grade2_price'=>$request->grade2_price,
            'grade3_price'=>$request->grade3_price,
            'grade4_price'=>$request->grade4_price,
            'grade1_original_price'=>$request->grade1_original_price,
            'grade2_original_price'=>$request->grade2_original_price,
            'grade3_original_price'=>$request->grade3_original_price,
            'grade4_original_price'=>$request->grade4_original_price,
            'isactive'=>false,
        ]))
        {
            return redirect()->back()->with('success', 'Therapy has been added');
        }
        return redirect()->back()->with('error', 'Clinic create failed');
    }

}
