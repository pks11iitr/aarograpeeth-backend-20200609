<?php

namespace App\Http\Controllers\ClinicAdmin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class TherapistController extends Controller
{
    public function index(Request $request){
        $users=User::where(function($users) use($request){
            $users->where('name','LIKE','%'.$request->search.'%');
        });

        if($request->ordertype)
            $users=$users->orderBy('name', $request->ordertype);

        $users=$users->paginate(10);

        return view('clinicadmin.therapistadmin.view',['users'=>$users]);
    }

    public function create(Request $request){
        return view('clinicadmin.therapistadmin.add');
    }

    public function store(Request $request){
        $request->validate([
            'status'=>'required',
            'name'=>'required',
            'email'=>'required',
            'mobile'=>'required'
        ]);

        if($user=User::create([
            'status'=>$request->status,
            'name'=>$request->name,
            'email'=>$request->email,
            'mobile'=>$request->mobile,
            'password'=> Hash::make($request->password)]))

            $user->assignRole('clinic-therapistadmin');
        {
            return redirect()->route('therapistadmin.list', ['id'=>$user->id])->with('success', 'Therapist has been created');
        }
        return redirect()->back()->with('error', 'Therapist create failed');
    }

    public function edit(Request $request,$id){
        $user =User::findOrFail($id);
        return view('clinicadmin.therapistadmin.edit',['user'=>$user]);
    }

    public function update(Request $request,$id){
        $request->validate([
            'status'=>'required',
            'name'=>'required',
            'email'=>'required',
            'mobile'=>'required',
        ]);
        $user = User::findOrFail($id);
        if($user->update([
                'status'=>$request->status,
                'name'=>$request->name,
                'email'=>$request->email,
                'mobile'=>$request->mobile,
                'password'=>$request->password]));
        {
            return redirect()->route('therapistadmin.list')->with('success', 'Therapist has been updated');
        }

        return redirect()->back()->with('error', 'Therapist update failed');

    }
}
