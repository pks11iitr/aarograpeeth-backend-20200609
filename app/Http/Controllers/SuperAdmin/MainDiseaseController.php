<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\Disease;
use App\Models\DiseasewiseTreatment;
use App\Models\MainDisease;
use App\Models\PainPoint;
use App\Models\ReasonDisease;
use App\Models\Treatment;
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
            'isactive'=>'required',
            'recommended_days'=>'required'
        ]);

        if($disease=MainDisease::create([
            'name'=>$request->name,
            'isactive'=>$request->isactive,
            'recommended_days'=>$request->recommended_days
        ]))
        {
            return redirect()->route('main-disease.list')->with('success', 'Disease has been created');
        }
        return redirect()->back()->with('error', 'Disease create failed');
    }

    public function edit(Request $request,$id){
        $disease =MainDisease::findOrFail($id);
        $treatments=DiseasewiseTreatment::with(['mainDisease', 'painPoints'])
            ->where('main_disease_id', $id)->get();
        return view('admin.main-disease.edit',['disease'=>$disease, 'treatments'=>$treatments]);
    }

    public function update(Request $request,$id){
        $request->validate([
            'name'=>'required',
            'isactive'=>'required',
            'recommended_days'=>'required'
        ]);

        $disease =MainDisease::findOrFail($id);

        if($disease->update([
            'name'=>$request->name,
            'isactive'=>$request->isactive,
            'recommended_days'=>$request->recommended_days
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

    public function addTreatment(Request $request, $disease_id){
        $disease=MainDisease::find($disease_id);
        $reason_diseases=ReasonDisease::get();
        $ignore_diseases=Disease::get();
        $pain_points=PainPoint::get();
        return view('admin.main-disease.treatment-add', compact('disease','reason_diseases','ignore_diseases','pain_points'));

    }

    public function storeTreatment(Request $request, $disease_id){

        //var_dump($request->all());die;
        $disease=MainDisease::find($disease_id);
        $treatment=DiseasewiseTreatment::create(array_merge(['main_disease_id'=>$disease_id], $request->only('title','description','precautions','exercise','dont_exercise','diet', 'recommended_days','action_when_pain_increase', 'isactive')));

        $treatment->painPoints()->sync($request->pain_points);

        return redirect()->route('main-disease.edit',['id'=>$disease->id, 'treatment_id'=>$treatment->id])->with('success', 'Treatment has been added');
    }

    public function editTreatment(Request $request, $disease_id, $treatment_id){
        $disease=MainDisease::find($disease_id);
        $treatment=DiseasewiseTreatment::find($treatment_id);
        //$reason_diseases=ReasonDisease::get();
        //$ignore_diseases=Disease::get();
        $pain_points=PainPoint::get();
        return view('admin.main-disease.treatment-edit', compact('disease','pain_points', 'treatment'));
    }

    public function updateTreatment(Request $request, $disease_id, $treatment_id){
        //var_dump($request->all());die;
        $disease=MainDisease::find($disease_id);
        $treatment=DiseasewiseTreatment::find($treatment_id);
        $treatment->update($request->only('description','exercise','precautions','diet', 'isactive', 'title'));
        //$treatment->reasonDiseases()->sync($request->reason_diseases);
        $treatment->painPoints()->sync($request->pain_points);

        return redirect()->route('main-disease.treatment-edit',['id'=>$disease->id, 'treatment_id'=>$treatment->id])->with('success', 'Treatment has been updated');
    }

    public function addReason(Request $request, $main_disease_id){
        //die();
        $main_disease=MainDisease::findOrFail($main_disease_id);
        //die('h');
        return view('admin.main-disease.add-reason', compact('main_disease'));
    }

    public function storeReason(Request $request, $main_disease_id){

        $request->validate([
            'name'=>'required',
            'isactive'=>'required:in:0,1'
        ]);

        $main_disease=MainDisease::findOrFail($main_disease_id);

        ReasonDisease::create([
            'main_disease_id'=>$main_disease_id,
            'name'=>$request->name,
            'isactive'=>$request->isactive
        ]);

        return redirect()->back()->with('success', 'Reason has been added');
    }


    public function editReason(Request $request, $main_disease_id, $reason_id){
        $main_disease=MainDisease::findOrFail($main_disease_id);

        $reason=ReasonDisease::findOrFail($reason_id);

        return view('admin.main-disease.edit-reason', compact('main_disease', 'reason'));
    }

    public function updateReason(Request $request, $main_disease_id, $reason_id){

        $request->validate([
            'name'=>'required',
            'isactive'=>'required:in:0,1'
        ]);

        $main_disease=MainDisease::findOrFail($main_disease_id);

        $reason=ReasonDisease::findOrFail($reason_id);

        $reason->update([
            'name'=>$request->name,
            'isactive'=>$request->isactive
        ]);

        return redirect()->back()->with('success', 'Reason has been updated');
    }

}
