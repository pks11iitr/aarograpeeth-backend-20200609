<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Exports\CustomerExport;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Storage;
use Maatwebsite\Excel\Facades\Excel;

class CustomerController extends Controller
{
     public function index(Request $request){

            $customers=Customer::where(function($customers) use($request){
                $customers->where('name','LIKE','%'.$request->search.'%')
                    ->orWhere('mobile','LIKE','%'.$request->search.'%')
                    ->orWhere('email','LIKE','%'.$request->search.'%');
            });

            if($request->fromdate)
                $customers=$customers->where('created_at', '>=', $request->fromdate.'00:00:00');

            if($request->todate)
                $customers=$customers->where('created_at', '<=', $request->todate.'23:59:50');

            if($request->status)
                $customers=$customers->where('status', $request->status);

            if($request->ordertype)
                $customers=$customers->orderBy('created_at', $request->ordertype);

            if($request->export)
                return $this->export($customers);

            $customers=$customers->paginate(10);
            return view('admin.customer.view',['customers'=>$customers]);
     }

    public function export($customers){
        $customers=$customers->get();
        return Excel::download(new CustomerExport($customers), 'customers.xlsx');
        //return view('admin.customer.export',['customers'=>$customers]);
    }


    public function edit(Request $request,$id){
             $customers = Customer::findOrFail($id);
             return view('admin.customer.edit',['customers'=>$customers]);
             }




    public function update(Request $request,$id){
             $request->validate([
                             'status'=>'required',
                  			'name'=>'required',
//                  			'dob'=>'required',
//                  			'address'=>'required',
//                  			'city'=>'required',
//                  			'state'=>'required',
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
