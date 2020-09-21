<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\PainPoint;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PainPointController extends Controller
{
    public function index(Request $request){
        $painpoints =PainPoint::get();
        return view('admin.painpoint.view',['painpoints'=>$painpoints]);
    }

    public function create(Request $request){
        return view('admin.painpoint.add');
    }

    public function store(Request $request){
        $request->validate([
            'name'=>'required',
            'isactive'=>'required'
        ]);

        if($painpoint=PainPoint::create([
            'name'=>$request->name,
            'isactive'=>$request->isactive,
        ]))
        {
            return redirect()->route('painpoint.list')->with('success', 'PainPoint has been created');
        }
        return redirect()->back()->with('error', 'PainPoint create failed');
    }

    public function edit(Request $request,$id){
        $painpoint =PainPoint::findOrFail($id);
        return view('admin.painpoint.edit',['painpoint'=>$painpoint]);
    }

    public function update(Request $request,$id){
        $request->validate([
            'name'=>'required',
            'isactive'=>'required'
        ]);

        $painpoint =PainPoint::findOrFail($id);

        if($painpoint->update([
            'name'=>$request->name,
            'isactive'=>$request->isactive,
        ]))
        {
            return redirect()->route('painpoint.list')->with('success', 'PainPoint has been updated');
        }
        return redirect()->back()->with('error', 'PainPoint updated failed');
    }

    public function delete(Request $request,$id){
        PainPoint::where('id', $id)->delete();
        return redirect()->back()->with('success', 'PainPoint has been deleted');
    }
}
