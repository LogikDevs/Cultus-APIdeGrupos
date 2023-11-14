<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\groups;
use App\Models\user;
use App\Models\integrates;
use App\Models\post;
use App\Http\Controllers\IntegratesController;
use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class GroupsController extends Controller
{

    public function GetUser(request $request){
        $user = new User();
        $user->fill($request->user);
        return $user;
    }

    public function ListAll(){
        return groups::all();
    }

    public function ListOne($id){
        return groups::findOrFail($id);
    }

    public function ListGroupPosts(request $request, $id){
        $tokenHeader = [ "Authorization" => $request->header("Authorization")];
        $route = getenv("API_POSTS_URL") . "/api/v1/posts/group/". $id;
        $response = Http::withHeaders($tokenHeader)->get($route);
        return response ($response);
    }

    public function ListUserGroups(request $request){
        $User = self::GetUser($request);
        $Integrates = new  IntegratesController();
        $Integrates = $Integrates -> ListUserGroups($User);
        $Groups = array();
        foreach ($Integrates as $Integrate){
            $Group = groups::findOrFail($Integrate->id_group);
            array_push($Groups, $Group);
        }
        return $Groups;

    }

    public function Create(request $request){

        $validation = self::CreateValidation($request);

        if ($validation->fails())
        return $validation->errors();
        $Chat = new  ChatController();
        $Chat = $Chat->CreateChat($request);
        return response([$this -> CreateRequest($request, $Chat), $Chat], 201);
    }

    public function CreateValidation(request $request){
        
            $validation = Validator::make($request->all(),[
                'name'=>'required | string | max:50',
                'description'=> 'nullable | max:255',
                'picture' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
                'privacy' => 'required | in:public,private',

                'user.id' => 'required | exists:users,id'
                
            ]);
            return $validation;
    }

    public function CreateRequest(request $request, $Chat){

        try {
        DB::raw('LOCK TABLE groups WRITE');
        DB::raw('LOCK TABLE integrates WRITE');
        DB::beginTransaction();

        $Group = new groups();
        $Group->name = $request->post("name");
        $Group -> description = $request ->post("description");
        $Group -> privacy = $request ->post("privacy");
        $Group -> id_chat = $Chat -> id;
        if ($request->file("picture")){
            
        $path = $request->file('picture')->store('/public/picture');
        $Group -> picture = basename($path);
        }
        $Group->save();
        
        $Integrates = new  IntegratesController();     
        $Integrate =  $Integrates -> JoinGroupRequest($request->input('user.id'), $Group->id_group, $Group->id_chat, "Admin", $request);

        DB::commit();
        DB::raw('UNLOCK TABLES');

        return ([$Group, $Integrate]);
    }
        catch (\Illuminate\Database\QueryException $th) {
            DB::rollback();
            return $th->getMessage();
        }
        catch (\PDOException $th) {
            return response("Permission to DB denied",403);

        }      
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
        try {
            DB::raw('LOCK TABLE groups WRITE');
            DB::beginTransaction();
        $Group = groups::findOrFail($request->post("id_group"));
        $Group->name = $request->post("name");
        $Group->save();
        return response()->json([$Group], 201);
        }
        catch (\Illuminate\Database\QueryException $th) {
            DB::rollback();
            return $th->getMessage();
        }
        catch (\PDOException $th) {
            return response("Permission to DB denied",403);

        }
    }

    public function LeaveGroup(request $request, $id){
        $group = self::LeaveGroupValidation($id);
        $user = self::GetUser($request);
        $Integrates = new  IntegratesController();       
        $Integrate =  $Integrates -> UserIntegrate($user->id, $id);
        if (!$Integrate){
            return "User is not part of this group";    
        }
        return self::LeaveGroupRequest($Integrate);
    }

    public function LeaveGroupValidation($id){
         $group = groups::findOrFail($id);
        }

    public function LeaveGroupRequest($Integrate){
        try {
            DB::raw('LOCK TABLE integrates WRITE');
            DB::beginTransaction();

            $Integrate->delete(); 
            DB::commit();
            DB::raw('UNLOCK TABLES');

            return ["response" => "User has left the group"];
        }
        catch (\Illuminate\Database\QueryException $th) {
            DB::rollback();
            return $th->getMessage();
        }
        catch (\PDOException $th) {
            return response("Permission to DB denied",403);
        }      
    }
}
