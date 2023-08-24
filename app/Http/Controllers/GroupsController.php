<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\groups;
use App\Http\Controllers\IntegratesController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
class GroupsController extends Controller
{

    public function ListAll(){
        return groups::all();
    }


    public function ListOne($id){
        return groups::findOrFail($id);
    }

    public function Create(request $request){

        $validation = self::CreateValidation($request);

        if ($validation->fails())
        return $validation->errors();
    
        return $this -> CreateRequest($request);
    }

    public function CreateValidation(request $request){
            $validation = Validator::make($request->all(),[
                'name'=>'required | string | max:50',
                'description'=> 'nullable | max:255',
                'picture' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
                'privacy' => 'required | in:public,private',

                'id_user' => 'required | exists:users,id'
                
            ]);
            return $validation;
    }


    public function CreateRequest(request $request){
        $Group = new groups();

        $Group->name = $request->post("name");
        $Group -> description = $request ->post("description");
        $Group -> privacy = $request ->post("privacy");
        
        if ($request->file("picture")){
            
        $path = $request->file('picture')->store('/public/picture');
        $Group -> picture = $path;
        }
        $Group->save();
        $Integrates = new  IntegratesController();
        $Integrate =  $Integrates -> createAdmin($request->post("id_user"), $Group->id_group);

        return response()->json([$Group, $Integrate], 201);
    }

    public function EditName(request $request){
        $validation = self::EditNameValidation($request);

        if ($validation->fails())
        return $validation->errors();
    
        return $this -> EditNameRequest($request);
    }


    public function EditNameValidation(request $request){
        $validation = Validator::make($request->all(),[
            'name'=>'required | string | max:50',
            'id_group'=>'required | exists:groups,id_group'
        ]);
        return $validation;
    }

    public function EditNameRequest(request $request){
        $Group = groups::findOrFail($request->post("id_group"));
        $Group->name = $request->post("name");
        $Group->save();
        return response()->json([$Group], 201);
    }
}
