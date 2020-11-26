<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\MainDisease;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MainDiseaseController extends Controller
{
    public function index(Request $request){
        $diseases =MainDisease::get();
        return view('admin.main-disease.view',['diseases'=>$diseases]);
    }

    public function create(Request $request){
        return view('admin.main-disease.add');
    }

    public function store(Request $request){
        $request->validate([
            'name'=>'required',
            'isactive'=>'required'
        ]);

        if($disease=MainDisease::create([
            'name'=>$request->name,
            'isactive'=>$request->isactive,
        ]))
        {
            return redirect()->route('main-disease.list')->with('success', 'Disease has been created');
        }
        return redirect()->back()->with('error', 'Disease create failed');
    }

    public function edit(Request $request,$id){
        $disease =MainDisease::findOrFail($id);
        return view('admin.main-disease.edit',['disease'=>$disease]);
    }

    public function update(Request $request,$id){
        $request->validate([
            'name'=>'required',
            'isactive'=>'required'
        ]);

        $disease =MainDisease::findOrFail($id);

        if($disease->update([
            'name'=>$request->name,
            'isactive'=>$request->isactive,
        ]))
        {
            return redirect()->back()->with('success', 'Disease has been updated');
        }
        return redirect()->back()->with('error', 'Disease updated failed');
    }

//    public function delete(Request $request,$id){
//        MainDisease::where('id', $id)->delete();
//        return redirect()->back()->with('success', 'Disease has been deleted');
//    }
}
