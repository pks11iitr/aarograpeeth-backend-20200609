<?php

namespace App\Http\Controllers\ClinicAdmin;

use App\Models\Clinic;
use App\Models\Therapist;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class TherapistController extends Controller
{
    public function index(Request $request){

        $user=auth()->user();
        $clinic =Clinic::where('user_id',$user->id)
            ->firstOrFail();

        $users=User::with(['reviews'])
        ->where('clinic_id', $clinic->id)
        ->where(function($users) use($request){
            $users->where('name','LIKE','%'.$request->search.'%');
        });

        if($request->ordertype)
            $users=$users->orderBy('name', $request->ordertype);

        $users=$users->paginate(10);

        return view('clinicadmin.therapist.view',['therapists'=>$users]);
    }

    public function create(Request $request){
        return view('clinicadmin.therapist.add');
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

        $user=auth()->user();
        $clinic =Clinic::where('user_id',$user->id)->firstOrFail();

        $therapist=User::where('email', $request->email)
            ->orWhere('mobile', $request->mobile)
            ->first();

        if($therapist)
            return redirect()->back()->with('error', 'Email or Mobile already registered with us');

        if($therapist=User::create([
            'clinic_id'=>$clinic->id,
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
            $therapist->assignRole('clinic-therapist');
            return redirect()->route('clinicadmin.therapist.list', ['id'=>$therapist->id])->with('success', 'Therapist has been created');
        }
        return redirect()->back()->with('error', 'Therapist create failed');
    }

    public function edit(Request $request,$id){
        $user=auth()->user();
        $clinic =Clinic::where('user_id',$user->id)->firstOrFail();
        $user =User::where('clinic_id', $clinic->id)->findOrFail($id);
        return view('clinicadmin.therapist.edit',['user'=>$user]);
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

        $user=auth()->user();
        $clinic =Clinic::where('user_id',$user->id)->first();

        $therapist = User::where('clinic_id', $clinic->id)->findOrFail($id);
        $therapist->update([
            'isactive'=>$request->isactive,
            'name'=>$request->name,
            'email'=>$request->email,
            'mobile'=>$request->mobile,
            'address'=>$request->address,
            'city'=>$request->city,
            'state'=>$request->state,
            //'password'=>Hash::make($request->password),
        ]);
        if($request->password){
            $therapist->password=Hash::make($request->password);
            $therapist->save();
        }

        if(!empty($request->image))
            $therapist->saveImage($request->image, 'therapists');
        if($therapist)
        {
            return redirect()->back()->with('success', 'Therapist has been updated');
        }
        return redirect()->back()->with('error', 'Therapist update failed');
    }

}
