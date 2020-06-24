<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Clinic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Storage;

class ClinicController extends Controller
{
     public function index(Request $request){
            $clinics=Clinic::paginate(10);;
            return view('admin.clinic.view',['clinics'=>$clinics]);
              }

    public function create(Request $request){
            return view('admin.clinic.add');
               }

   public function store(Request $request){
               $request->validate([
                  			'isactive'=>'required',
                  			'name'=>'required',
                  			'description'=>'required',
                  			'address'=>'required',
                  			'city'=>'required',
                  			'state'=>'required',
                  			'contact'=>'required',
                  			'lat'=>'required',
                  			'lang'=>'required',
                  			'image'=>'required|image'
                               ]);
       //    var_dump($request->address); die;
          if($clinic=Clinic::create([
                      'name'=>$request->name,
                      'description'=>$request->description,
                      'address'=>$request->address,
                      'city'=>$request->city,
                      'state'=>$request->state,
                      'contact'=>$request->contact,
                      'lat'=>$request->lat,
                      'lang'=>$request->lang,
                      'isactive'=>$request->isactive,
                      'image'=>'a']))
            {
				$clinic->saveImage($request->image, 'clinics');
             return redirect()->route('clinic.list')->with('success', 'Clinic has been created');
            }
             return redirect()->back()->with('error', 'Clinic create failed');
          }
          
    public function edit(Request $request,$id){
             $clinic = Clinic::findOrFail($id);
             return view('admin.clinic.edit',['clinic'=>$clinic]);
             }

    public function update(Request $request,$id){
             $request->validate([
                             'isactive'=>'required',
                  			'name'=>'required',
                  			'description'=>'required',
                  			'address'=>'required',
                  			'city'=>'required',
                  			'state'=>'required',
                  			'contact'=>'required',
                  			'lat'=>'required',
                  			'lang'=>'required'
                  			]);
                      
             $clinic = Clinic::findOrFail($id);
          if($request->image){                  
			 $clinic->update([
                      'isactive'=>$request->isactive,
                      'name'=>$request->name,
                      'description'=>$request->description,
                      'address'=>$request->address,
                      'city'=>$request->city,
                      'state'=>$request->state,
                      'contact'=>$request->contact,
                      'lat'=>$request->lat,
                      'lang'=>$request->lang,
                      'image'=>'a']);
             $clinic->saveImage($request->image, 'clinics');
        }else{
             $clinic->update([
                      'isactive'=>$request->isactive,
                      'name'=>$request->name,
                      'description'=>$request->description,
                      'address'=>$request->address,
                      'city'=>$request->city,
                      'state'=>$request->state,
                      'contact'=>$request->contact,
                      'lat'=>$request->lat,
                      'lang'=>$request->lang ]);
             }
          if($clinic)
             {
           return redirect()->route('clinic.list')->with('success', 'Clinic has been updated');
              }
           return redirect()->back()->with('error', 'Clinic update failed');

      }

  }
