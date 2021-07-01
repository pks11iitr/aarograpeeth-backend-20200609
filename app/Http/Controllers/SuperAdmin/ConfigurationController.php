<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\Configuration;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ConfigurationController extends Controller
{
    public function index(Request $request){
        $configurations=Configuration::get();

        return view('admin.configuration.view', compact('configurations'));
    }


    public function edit(Request $request, $id){
        $configuration=Configuration::findOrFail($id);
        return view('admin.configuration.edit', compact('configuration'));
    }


    public function update(Request $request, $id){

        $request->validate([
            'param_value'=>'required'
        ]);

        $configuration=Configuration::findOrFail($id);
        $configuration->value=$request->param_value;
        $configuration->save();

        return redirect()->back()->with('success', 'Settings has been updated');
    }


}
