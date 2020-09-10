<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\Therapist;
use App\Models\TherapistTherapy;
use App\Models\Therapy;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class NewTherapistController extends Controller
{
    public function index(Request $request){
        $therapists=Therapist::where(function($therapists) use($request){
            $therapists->where('name','LIKE','%'.$request->search.'%');
        });

        if($request->ordertype)
            $therapists=$therapists->orderBy('name', $request->ordertype);

        $therapists=$therapists->paginate(10);
        return view('admin.therapist.view',['therapists'=>$therapists]);
    }

    public function create(Request $request){
        return view('admin.therapist.add');
    }

    public function store(Request $request){
        $request->validate([
            'isactive'=>'required',
            'name'=>'required',
            'email'=>'required',
            'mobile'=>'required',
            'address'=>'required',
            'city'=>'required',
            'state'=>'required',
            'image'=>'required|image'
        ]);

        if($therapist=Therapist::create([
            'isactive'=>$request->isactive,
            'name'=>$request->name,
            'email'=>$request->email,
            'mobile'=>$request->mobile,
            'address'=>$request->address,
            'city'=>$request->city,
            'state'=>$request->state,
            'password'=>Hash::make($request->password),
            'image'=>'a']))
        {
            $therapist->saveImage($request->image, 'therapists');
            return redirect()->route('therapists.list', ['id'=>$therapist->id])->with('success', 'Therapist has been created');
        }
        return redirect()->back()->with('error', 'Therapist create failed');
    }

    public function edit(Request $request,$id){
        $therapist =Therapist::findOrFail($id);
        $therapys = Therapy::get();
        $therapistherapys =TherapistTherapy::where('therapist_id',$id)->paginate(5);
        return view('admin.therapist.edit',['therapist'=>$therapist,'therapys'=>$therapys,'therapistherapys'=>$therapistherapys]);
    }

    public function update(Request $request,$id){
        $request->validate([
            'isactive'=>'required',
            'name'=>'required',
            'email'=>'required',
            'mobile'=>'required',
            'address'=>'required',
            'city'=>'required',
            'state'=>'required',
            'image'=>'image'
        ]);
        $therapist = Therapist::findOrFail($id);
        if($request->image){
            $therapist->update([
                'isactive'=>$request->isactive,
                'name'=>$request->name,
                'email'=>$request->email,
                'mobile'=>$request->mobile,
                'address'=>$request->address,
                'city'=>$request->city,
                'state'=>$request->state,
                'password'=>Hash::make($request->password),
                'image'=>'a']);
            $therapist->saveImage($request->image, 'therapists');
        }else{
            $therapist->update([
                'isactive'=>$request->isactive,
                'name'=>$request->name,
                'email'=>$request->email,
                'mobile'=>$request->mobile,
                'address'=>$request->address,
                'city'=>$request->city,
                'state'=>$request->state,
                'password'=>Hash::make($request->password),]);
        }
        if($therapist)
        {
            return redirect()->route('therapists.list')->with('success', 'Therapist has been updated');
        }
        return redirect()->back()->with('error', 'Therapist update failed');
    }

    public function therapystore(Request $request,$id){
        $request->validate([
            'isactive'=>'required',
            'therapy_id'=>'required',
            'therapist_grade'=>'required'
        ]);

        if($therapisttherapy=TherapistTherapy::create([
            'therapist_id'=>$id,
            'therapy_id'=>$request->therapy_id,
            'therapist_grade'=>$request->therapist_grade,
            'isactive'=>$request->isactive,
        ]))
        {
            return redirect()->back()->with('success', 'Therapy has been added');
        }
        return redirect()->back()->with('error', 'Clinic create failed');
    }

    public function therapyedit(Request $request,$id){
        $therapisttherapy =TherapistTherapy::with('therapy')->find($id);
        return view('admin.therapist.therapist-therapy-edit',['therapisttherapy'=>$therapisttherapy]);

    }
    public function therapyupdate(Request $request,$id){
        $request->validate([
            'isactive'=>'required',
            'therapist_grade'=>'required'
        ]);

        $therapisttherapy=TherapistTherapy::find($id);
        if($therapistupdate=$therapisttherapy->update([
            'therapy_id'=>$request->therapy_id,
            'therapist_grade'=>$request->therapist_grade,
            'isactive'=>$request->isactive,
        ]))
        {
            return redirect()->back()->with('success', 'Therapy has been update');
        }
        return redirect()->back()->with('error', 'Clinic update failed');
    }

}
