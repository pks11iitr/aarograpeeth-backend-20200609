<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\Treatment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TreatmentController extends Controller
{
    public function index(Request $request){
        $treatments =Treatment::get();
        return view('admin.treatment.view',['treatments'=>$treatments]);
    }

    public function create(Request $request){
        return view('admin.treatment.add');
    }

    public function store(Request $request){
        $request->validate([
            'name'=>'required',
            'isactive'=>'required'
        ]);

        if($treatment=Treatment::create([
            'name'=>$request->name,
            'isactive'=>$request->isactive,
        ]))
        {
            return redirect()->route('treatment.list')->with('success', 'Treatment has been created');
        }
        return redirect()->back()->with('error', 'Treatment create failed');
    }

    public function edit(Request $request,$id){
        $treatment =Treatment::findOrFail($id);
        return view('admin.treatment.edit',['treatment'=>$treatment]);
    }

    public function update(Request $request,$id){
        $request->validate([
            'name'=>'required',
            'isactive'=>'required'
        ]);

        $treatment =Treatment::findOrFail($id);

        if($treatment->update([
            'name'=>$request->name,
            'isactive'=>$request->isactive,
        ]))
        {
            return redirect()->route('treatment.list')->with('success', 'Treatment has been updated');
        }
        return redirect()->back()->with('error', 'Treatment updated failed');
    }

    public function delete(Request $request,$id){
        Treatment::where('id', $id)->delete();
        return redirect()->back()->with('success', 'Treatment has been deleted');
    }
}
