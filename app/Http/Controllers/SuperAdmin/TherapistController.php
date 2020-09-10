<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Clinic;
use App\Models\DailyBookingsSlots;
use App\Models\Therapist;
use App\Models\Therapy;
use App\Models\Document;
use App\Models\TimeSlot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Storage;

class TherapistController extends Controller
{
     public function index(Request $request){
		 $therapist=Therapy::where(function($therapist) use($request){
                $therapist->where('name','LIKE','%'.$request->search.'%');
            });

            if($request->ordertype)
                $therapist=$therapist->orderBy('name', $request->ordertype);

            $therapist=$therapist->paginate(10);

            return view('admin.therapy.view',['therapist'=>$therapist]);
              }

    public function create(Request $request){
            return view('admin.therapy.add');
               }

   public function store(Request $request){
               $request->validate([
                  			'isactive'=>'required',
                  			'name'=>'required',
                  			'description'=>'required',
                  			'price1'=>'required',
                  			'price2'=>'required',
                  			'price3'=>'required',
                  			'price4'=>'required',
                  			'image'=>'required|image'
                               ]);

          if($therapy=Therapy::create([
                      'isactive'=>$request->isactive,
                      'name'=>$request->name,
                      'description'=>$request->description,
                      'grade1_price'=>$request->price1,
                      'grade2_price'=>$request->price2,
                      'grade3_price'=>$request->price3,
                      'grade4_price'=>$request->price4,
                      'image'=>'a']))
            {
				$therapy->saveImage($request->image, 'therapies');
             return redirect()->route('therapy.edit', ['id'=>$therapy->id])->with('success', 'Therapy has been created');
            }
             return redirect()->back()->with('error', 'Therapy create failed');
          }

    public function edit(Request $request,$id){
             $therapy = Therapy::findOrFail($id);
             $documents = $therapy->gallery;
             return view('admin.therapy.edit',['therapy'=>$therapy,'documents'=>$documents]);
             }

    public function update(Request $request,$id){
             $request->validate([
                            'isactive'=>'required',
                  			'name'=>'required',
                  			'description'=>'required',
                  			'price1'=>'required',
                  			'price2'=>'required',
                  			'price3'=>'required',
                  			'price4'=>'required',
                  			'image'=>'image'
                               ]);
             $therapy = Therapy::findOrFail($id);
          if($request->image){
			 $therapy->update([
                      'isactive'=>$request->isactive,
                      'name'=>$request->name,
                      'description'=>$request->description,
                      'grade1_price'=>$request->price1,
                      'grade2_price'=>$request->price2,
                      'grade3_price'=>$request->price3,
                      'grade4_price'=>$request->price4,
                      'image'=>'a']);
             $therapy->saveImage($request->image, 'therapies');
        }else{
             $therapy->update([
                      'isactive'=>$request->isactive,
                      'name'=>$request->name,
                      'description'=>$request->description,
                      'grade1_price'=>$request->price1,
                      'grade2_price'=>$request->price2,
                      'grade3_price'=>$request->price3,
                      'grade4_price'=>$request->price4 ]);
             }
          if($therapy)
             {
           return redirect()->back()->with('success', 'Therapy has been updated');
              }
           return redirect()->back()->with('error', 'Therapy update failed');

      }

   public function document(Request $request, $id){
                   $request->validate([
                               'file_path.*'=>'image'
                               ]);
                $therapy=Therapy::find($id);
              foreach($request->file_path as $file){
                $therapy->saveDocument($file, 'therapies');
                  }
             if($therapy)  {
                   return redirect()->back()->with('success', 'Document has been created');
                     }
                   return redirect()->back()->with('error', 'Document create failed');
          }

     public function delete(Request $request, $id){
           Document::where('id', $id)->delete();
           return redirect()->back()->with('success', 'Document has been deleted');
        }


    public function getAvailableHomeTherapist(Request $request){
        $therapist=Therapist::active()->whereHas('therapies', function($therapies) use($request){
            $therapies->where('therapies.id', $request->therapy_id);
        })
            ->whereDoesntHave('bookings', function($bookings) use($request){
                $bookings->where('slot_id', $request->slot_id);
            })
            ->select('id', 'name')
            ->get();//die;

        return $therapist;
    }


    public function getAvailableTimeSlots(Request $request){
        $therapy=Therapy::findOrFail($request->therapy_id);
        $slots=DailyBookingsSlots::getTimeSlotsForAdmin($therapy, $request->date, $request->grade);
        return $slots;

    }
  }
