<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Clinic;
use App\Models\Document;
use App\Models\Therapy;
use App\Models\ClinicTherapy;
use App\Models\TimeSlot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Storage;

class ClinicController extends Controller
{
     public function index(Request $request){

		 $clinics=Clinic::with('reviews')

            ->where(function($clinics) use($request){
                $clinics->where('name','LIKE','%'.$request->search.'%');
            });

            if($request->ordertype)
                $clinics=$clinics->orderBy('name', $request->ordertype);

            $clinics=$clinics->paginate(10);
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
                  			'image'=>'required|image'
                               ]);

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
             return redirect()->route('clinic.edit', ['id'=>$clinic->id])->with('success', 'Clinic has been created');
            }
             return redirect()->back()->with('error', 'Clinic create failed');
          }

    public function edit(Request $request,$id){
             $clinic = Clinic::findOrFail($id);
             $documents = $clinic->gallery;
             $therapys=Therapy::where('isactive',1)->get();
             $clinictherapys=ClinicTherapy::where('clinic_id',$id)->get();
             return view('admin.clinic.edit',['clinic'=>$clinic,'documents'=>$documents,'therapys'=>$therapys,'clinictherapys'=>$clinictherapys]);
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
                  			'image'=>'image'
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
                      'lang'=>$request->lang
             ]);
             }
          if($clinic)
             {
           return redirect()->back()->with('success', 'Clinic has been updated');
              }
           return redirect()->back()->with('error', 'Clinic update failed');

      }
      public function document(Request $request, $id){

                $request->validate([
                    'file_path.*'=>'required|image'
                ]);

                $user=auth()->user();
                if($user->hasRole('admin'))
                    $clinic=Clinic::findOrFail($id);
                else if($user->hasRole('clinic-admin'))
                    $clinic=Clinic::where('user_id', $user->id)->findOrFail($id);
                else
                    return redirect()->back()->with('error', 'Access Denied');

              foreach($request->file_path as $file){
                $clinic->saveDocument($file, 'clinics');
                  }
             if($clinic)  {
                   return redirect()->back()->with('success', 'Images ahve been uploaded');
                     }
                   return redirect()->back()->with('error', 'Clinic create failed');
          }

     public function delete(Request $request, $id){

         $user=auth()->user();
         if($user->hasRole('admin'))
             Document::where('id', $id)->delete();
         else if($user->hasRole('clinic-admin')){
                 $document=Document::with('entity')
                ->where('documents.id', $id)->firstOrFail();
                 if($document && $document->entity->user_id==$user->id)
                     $document->delete();

             //dd($document);
         }
         else
             return redirect()->back()->with('error', 'Access Denied');

           return redirect()->back()->with('success', 'Document has been deleted');
        }

     public function therapystore(Request $request,$id){
               $request->validate([
                  			'isactive'=>'required',
                  			'therapy_id'=>'required',
                  			'grade1_price'=>'required',
                  			'grade2_price'=>'required',
                  			'grade3_price'=>'required',
                  			'grade4_price'=>'required',
                  			'grade1_original_price'=>'required',
                  			'grade2_original_price'=>'required',
                  			'grade3_original_price'=>'required',
                  			'grade4_original_price'=>'required'
                               ]);

          if($clinictherapy=ClinicTherapy::create([
                       'clinic_id'=>$id,
                       'therapy_id'=>$request->therapy_id,
                      'grade1_price'=>$request->grade1_price,
                      'grade2_price'=>$request->grade2_price,
                      'grade3_price'=>$request->grade3_price,
                      'grade4_price'=>$request->grade4_price,
                      'grade1_original_price'=>$request->grade1_original_price,
                      'grade2_original_price'=>$request->grade2_original_price,
                      'grade3_original_price'=>$request->grade3_original_price,
                      'grade4_original_price'=>$request->grade4_original_price,
                      'isactive'=>$request->isactive,
                      ]))
            {
             return redirect()->back()->with('success', 'Therapy has been added');
            }
             return redirect()->back()->with('error', 'Clinic create failed');
          }

   public function therapyedit(Request $request,$id){
         $therapy = ClinicTherapy::with(['clinic', 'therapy'])->find($id);

         return view('admin.clinic.clinic-therapy', compact('therapy'));
   }

    public function therapyupdate(Request $request,$id){
        $request->validate([
            'isactive'=>'required',
            //'therapy_id'=>'required',
            'grade1_price'=>'required',
            'grade2_price'=>'required',
            'grade3_price'=>'required',
            'grade4_price'=>'required',
            'grade1_original_price'=>'required',
            'grade2_original_price'=>'required',
            'grade3_original_price'=>'required',
            'grade4_original_price'=>'required'
        ]);

        $therapy=ClinicTherapy::find($id);

        if($clinictherapy=$therapy->update([
            //'clinic_id'=>$id,
            //'therapy_id'=>$request->therapy_id,
            'grade1_price'=>$request->grade1_price,
            'grade2_price'=>$request->grade2_price,
            'grade3_price'=>$request->grade3_price,
            'grade4_price'=>$request->grade4_price,
            'grade1_original_price'=>$request->grade1_original_price,
            'grade2_original_price'=>$request->grade2_original_price,
            'grade3_original_price'=>$request->grade3_original_price,
            'grade4_original_price'=>$request->grade4_original_price,
            'isactive'=>$request->isactive,
        ]))
        {
            return redirect()->back()->with('success', 'Therapy has been added');
        }
        return redirect()->back()->with('error', 'Clinic create failed');
    }

      public function therapyedelete(Request $request, $id){
           ClinicTherapy::where('id', $id)->delete();
           return redirect()->back()->with('success', 'Clinic Therapy has been deleted');
      }

        public function getAvailableTherapistInClinic(Request $request){
            $clinic=Clinic::findOrFail($request->clinic_id);//die;
            return $clinic->getAvailableTherapist($request->slot_id);
        }


        public function getAvailableTimeSlots(Request $request){
            $clinic=Clinic::findOrFail($request->clinic_id);
            $slots=TimeSlot::getTimeSlotsForAdmin($clinic, $request->date, $request->grade);
            return $slots;

        }


  }
