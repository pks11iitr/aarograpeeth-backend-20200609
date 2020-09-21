<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\Disease;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DiseaseController extends Controller
{
    public function index(Request $request){
        $diseases =Disease::get();
        return view('admin.disease.view',['diseases'=>$diseases]);
    }

    public function create(Request $request){
        return view('admin.disease.add');
    }

    public function store(Request $request){
        $request->validate([
            'name'=>'required',
            'isactive'=>'required'
        ]);

        if($disease=Disease::create([
            'name'=>$request->name,
            'isactive'=>$request->isactive,
        ]))
        {
            return redirect()->route('disease.list')->with('success', 'Disease has been created');
        }
        return redirect()->back()->with('error', 'Disease create failed');
    }

    public function edit(Request $request,$id){
        $disease =Disease::findOrFail($id);
        return view('admin.disease.edit',['disease'=>$disease]);
    }

    public function update(Request $request,$id){
        $request->validate([
            'name'=>'required',
            'isactive'=>'required'
        ]);

        $disease =Disease::findOrFail($id);

        if($disease->update([
            'name'=>$request->name,
            'isactive'=>$request->isactive,
        ]))
        {
            return redirect()->route('disease.list')->with('success', 'Disease has been updated');
        }
        return redirect()->back()->with('error', 'Disease updated failed');
    }

    public function delete(Request $request,$id){
        Disease::where('id', $id)->delete();
        return redirect()->back()->with('success', 'Disease has been deleted');
    }
}
