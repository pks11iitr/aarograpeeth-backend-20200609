<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\Therapist;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
            'password'=>$request->password,
            'image'=>'a']))
        {
            $therapist->saveImage($request->image, 'therapists');
            return redirect()->route('therapists.list', ['id'=>$therapist->id])->with('success', 'Therapist has been created');
        }
        return redirect()->back()->with('error', 'Therapist create failed');
    }

    public function edit(Request $request,$id){
        $therapist =Therapist::findOrFail($id);
        return view('admin.therapist.edit',['therapist'=>$therapist]);
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
                'password'=>$request->password,
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
                'password'=>$request->password,]);
        }
        if($therapist)
        {
            return redirect()->route('therapists.list')->with('success', 'Therapist has been updated');
        }
        return redirect()->back()->with('error', 'Therapist update failed');


    }
}
