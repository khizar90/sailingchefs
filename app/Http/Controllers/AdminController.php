<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;


class AdminController extends Controller
{
    public function listCatgory(){
        $catgories = DB::table('categories_recipe')->get();
        
        return view('catgory', compact('catgories'));
    }
    public function listDietary(){
        $dietaries = DB::table('dietary_recipe')->get();
        
        return view('dietary', compact('dietaries'));
    }
    public function listAllergies(){
        $allergies = DB::table('allergies_recipe')->get();
        
        return view('allergies', compact('allergies'));
    }


    public function addCatgory(Request $request){
        $request->validate(
            [ 
                'catgory' => 'required|unique:categories_recipe,name',
            ]
         );
        $name = $request->catgory;

        DB::table('categories_recipe')->insert([
                'name' => $name,
        ]);
        session()->flash('add', 'catgory added successfully!');
        
        $catgories = DB::table('categories_recipe')->get();
        return view('catgory' ,compact('catgories'));
    }


    public function deleteCatgory($cat_id){
        DB::table('categories_recipe')->where('id',$cat_id)->delete();
        return redirect()->back()->with('delete', 'Record Delete');
    }

    public function addDietary(Request $request){
        $request->validate(
            [ 
                'dietary' => 'required|unique:dietary_recipe,name',
            ]
         );
         
        $name = $request->dietary;

        DB::table('dietary_recipe')->insert([
                'name' => $name,
        ]);
        session()->flash('add', 'Dietary added successfully!');
        
        $dietaries = DB::table('dietary_recipe')->get();
        return view('dietary' ,compact('dietaries'));
    }

    public function deleteDietary($dt_id){
        
        DB::table('dietary_recipe')->where('id',$dt_id)->delete();
        return redirect()->back()->with('delete', 'Record Delete');
    }


    public function addAllergies(Request $request){
        $request->validate(
            [ 
                'allergies' => 'required|unique:dietary_recipe,name',
            ]
         );
        
        $name = $request->allergies;

        DB::table('allergies_recipe')->insert([
                'name' => $name,
        ]);
        session()->flash('add', 'Allergy added successfully!');
        
        $allergies = DB::table('allergies_recipe')->get();
        return view('allergies' ,compact('allergies'));
    }
    public function deleteAllergies($all_id){

        DB::table('allergies_recipe')->where('id',$all_id)->delete();
        return redirect()->back()->with('delete', 'Record Delete');
    }

    public function userList(){
        
        $users = User::all();
        
        return view('users' ,compact('users'));
    }

    public function updateCatgory(Request $request,$cat_id){
        DB::table('categories_recipe')
        ->where('id', $cat_id)
        ->update([
            'name' => $request->catgory,
        ]);
        return redirect()->back();
    }

    public function updateAllergy(Request $request,$all_id){
        DB::table('allergies_recipe')
        ->where('id', $all_id)
        ->update([
            'name' => $request->allergy,
        ]);
        return redirect()->back();
    }

    public function updateDietary(Request $request,$dar_id){
        DB::table('dietary_recipe')
        ->where('id', $dar_id)
        ->update([
            'name' => $request->dietary,
        ]);
        return redirect()->back();
    }
}
