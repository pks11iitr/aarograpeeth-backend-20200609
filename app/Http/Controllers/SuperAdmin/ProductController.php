<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Storage;

class ProductController extends Controller
{
     public function index(Request $request){

		 $products=Product::with('reviews')
             ->where(function($products) use($request){
                $products->where('name','LIKE','%'.$request->search.'%');
            });

            if($request->ordertype)
                $products=$products->orderBy('name', $request->ordertype);

            $products=$products->paginate(10);
            return view('admin.product.view',['products'=>$products]);
              }

    public function create(Request $request){
            return view('admin.product.add');
               }

   public function store(Request $request){
               $request->validate([
                  			'isactive'=>'required',
                  			'name'=>'required',
                  			'description'=>'required',
                  			'company'=>'required',
                  			'price'=>'required',
                  			'cut_price'=>'required',
                  			'ratings'=>'required',
                  			'top_deal'=>'required',
                  			'best_seller'=>'required',
                  			'image'=>'required|image'
                               ]);
          if($products=Product::create([
                      'name'=>$request->name,
                      'description'=>$request->description,
                      'company'=>$request->company,
                      'price'=>$request->price,
                      'cut_price'=>$request->cut_price,
                      'ratings'=>$request->ratings,
                      'top_deal'=>$request->top_deal,
                      'best_seller'=>$request->best_seller,
                      'isactive'=>$request->isactive,
                      'image'=>'a']))
            {
				$products->saveImage($request->image, 'products');
             return redirect()->route('product.edit', ['id'=>$products->id])->with('success', 'Product has been created');
            }
             return redirect()->back()->with('error', 'Product create failed');
          }

    public function edit(Request $request,$id){
             $products = Product::findOrFail($id);
             $documents = $products->gallery;
             return view('admin.product.edit',['products'=>$products,'documents'=>$documents]);
             }

    public function update(Request $request,$id){
             $request->validate([
                             'isactive'=>'required',
                  			'name'=>'required',
                  			'description'=>'required',
                  			'company'=>'required',
                  			'price'=>'required',
                  			'cut_price'=>'required',
                  			'ratings'=>'required',
                  			'top_deal'=>'required',
                  			'best_seller'=>'required',
                  			'image'=>'image'
                               ]);

             $product = Product::findOrFail($id);
          if($request->image){
			 $product->update([
                      'name'=>$request->name,
                      'description'=>$request->description,
                      'company'=>$request->company,
                      'price'=>$request->price,
                      'cut_price'=>$request->cut_price,
                      'ratings'=>$request->ratings,
                      'top_deal'=>$request->top_deal,
                      'best_seller'=>$request->best_seller,
                      'isactive'=>$request->isactive,
                      'image'=>'a']);
             $product->saveImage($request->image, 'products');
        }else{
             $product->update([
                      'name'=>$request->name,
                      'description'=>$request->description,
                      'company'=>$request->company,
                      'price'=>$request->price,
                      'cut_price'=>$request->cut_price,
                      'ratings'=>$request->ratings,
                      'top_deal'=>$request->top_deal,
                      'best_seller'=>$request->best_seller,
                      'isactive'=>$request->isactive
                      ]);
             }
          if($product)
             {
           return redirect()->back()->with('success', 'Product has been updated');
              }
           return redirect()->back()->with('error', 'Product update failed');

      }

      public function document(Request $request, $id){
                     $request->validate([
                               'file_path.*'=>'image'
                               ]);
                $product=Product::find($id);

              foreach($request->file_path as $file){
                $product->saveDocument($file, 'products');
                  }
             if($product)  {
                   return redirect()->back()->with('success', 'Product has been created');
                     }
                   return redirect()->back()->with('error', 'Product create failed');
          }

     public function delete(Request $request, $id){
           Document::where('id', $id)->delete();
           return redirect()->back()->with('success', 'Document has been deleted');
        }

  }
