<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Storage;

class CustomerController extends Controller
{
     public function index(Request $request){

            $customers=Customer::where(function($customers) use($request){
                $customers->where('name','LIKE','%'.$request->search.'%')->orwhere('mobile','LIKE','%'.$request->search.'%')->orwhere('email','LIKE','%'.$request->search.'%');
            });

            if($request->fromdate)
                $customers=$customers->where('created_at', '>=', $request->fromdate.'00:00:00');

            if($request->todate)
                $customers=$customers->where('created_at', '<=', $request->todate1.'23:59:50');

            if($request->status)
                $customers=$customers->where('status', $request->status);

            if($request->ordertype)
                $customers=$customers->orderBy('created_by', $request->ordertype);

            $customers=Customer::paginate(10);
            return view('admin.customer.view',['customers'=>$customers]);
     }

     public function customer_search(Request $request) {
	      $search=$request->input("search");
	      $ordertype=$request->input("ordertype");
	      $status=$request->input("status");
	     // var_dump($status); die;
	      $fromdate=$request->input("fromdate");
	      $todate1=$request->input("todate");
	      $todate = date('Y-m-d', strtotime($todate1. ' + 1 days'));

	   if($ordertype=='ASC'&& $search && $status && $fromdate && $todate){

		     $customers=Customer::orderBy('created_at','ASC')->whereBetween('created_at', [$fromdate, $todate])->where('status','=',$status)->where('name','LIKE','%'.$search.'%')->orwhere('mobile','LIKE','%'.$search.'%')->orwhere('email','LIKE','%'.$search.'%')->paginate(10);
			}elseif($ordertype=='DESC'&& $search && $status && $fromdate && $todate)
			{
		     $customers=Customer::orderBy('created_at','DESC')->whereBetween('created_at', [$fromdate, $todate])->where('status','=',$status)->where('name','LIKE','%'.$search.'%')->orwhere('mobile','LIKE','%'.$search.'%')->orwhere('email','LIKE','%'.$search.'%')->paginate(10);
		    }elseif($ordertype=='ASC'){
			$customers=Customer::orderBy('created_at','ASC')->paginate(10);
	         }elseif($ordertype=='DESC'){
		    $customers=Customer::orderBy('created_at','DESC')->paginate(10);
		     }elseif($status){
	        $customers=Customer::orderBy('created_at','DESC')->where('status','=',$status)->paginate(10);
             }elseif($search){
			$customers=Customer::orderBy('created_at','DESC')->where('name','LIKE','%'.$search.'%')->orwhere('mobile','LIKE','%'.$search.'%')->orwhere('email','LIKE','%'.$search.'%')->paginate(10);
            }elseif($fromdate && $todate){
		     $customers=Customer::orderBy('created_at','DESC')->whereBetween('created_at', [$fromdate, $todate])->paginate(10);
            }else{
			$customers=Customer::orderBy('created_at','DESC')->paginate(10);
            }
            return view('admin.customer.view',['customers'=>$customers]);
        }

    public function edit(Request $request,$id){
             $customers = Customer::findOrFail($id);
             return view('admin.customer.edit',['customers'=>$customers]);
             }

    public function update(Request $request,$id){
             $request->validate([
                             'status'=>'required',
                  			'name'=>'required',
                  			'dob'=>'required',
                  			'address'=>'required',
                  			'city'=>'required',
                  			'state'=>'required',
                  			'image'=>'image'
                  			]);

             $customers = Customer::findOrFail($id);
          if($request->image){
			 $customers->update([
                      'status'=>$request->status,
                      'name'=>$request->name,
                      'dob'=>$request->dob,
                      'address'=>$request->address,
                      'city'=>$request->city,
                      'state'=>$request->state,
                      'image'=>'a']);
             $customers->saveImage($request->image, 'customers');
        }else{
             $customers->update([
                      'status'=>$request->status,
                      'name'=>$request->name,
                      'dob'=>$request->dob,
                      'address'=>$request->address,
                      'city'=>$request->city,
                      'state'=>$request->state
                         ]);
             }
          if($customers)
             {
           return redirect()->route('customer.list')->with('success', 'Customer has been updated');
              }
           return redirect()->back()->with('error', 'Customer update failed');

      }

      function send_message(Request $request)
        {

        $cusid=$request->cusid;
        $title=$request->title;
        $des=$request->des;
        $Notification=Notification::create([
                      'title'=>$title,
                      'description'=>$des,
                      'user_id'=>$cusid,
                      'type'=>'individual'
                      ]);
         if($Notification){
           return response()->json(['users' => $Notification], 200);
       }else{
              return response()->json(['msg' => 'No result found!'], 404);
       }

        }

  }
