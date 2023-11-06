<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\integrates;
use App\Models\user;
use App\Models\groups;
use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
class IntegratesController extends Controller
{
    public function GetUser(request $request){
        $user = new User();
        $user->fill($request->user);
        return $user;
    }

    public function JoinGroup(request $request){
        $validation = self::JoinGroupValidation($request);

        if ($validation->fails())
        return $validation->errors();
       
        $user = self::GetUser($request);
        $group = self::CheckUserGroup($user->id, $request->post("id_group"));
        if ($group->isEmpty()){
            $Group = new Groups();      
            $Group = $Group::findOrFail($request->post("id_group"));
            return response()->json(self::JoinGroupRequest($user->id, $request->post("id_group"), $Group->id_chat, "Member", $request));
        }
        return "User is already part of the group";
    }

    public function JoinGroupValidation(request $request){
        $validation = Validator::make($request->all(),[
            'id_group'=> 'required | exists:groups,id_group',
        ]);
        return $validation;
    }

    public function JoinGroupRequest(int $id_user, int $id_group, int $id_chat, string $rol, request $request){
        $Integrates = new integrates();

        $Integrates -> id_user = $id_user;
        $Integrates -> id_group = $id_group;
        $Integrates -> rol = $rol;

        $Chat = new ChatController();      
        
        try {
            DB::raw('LOCK TABLE integrates WRITE');
            DB::beginTransaction();
             $Integrates -> save();
            if ($Chat->JoinChat($id_chat, $request)){
                DB::commit();
                DB::raw('UNLOCK TABLES');
                return $Integrates;
            }
            else{
                DB::rollback();
                return response("Error joining chat", 405);
            }                    
        }
        catch (\Illuminate\Database\QueryException $th) {
            DB::rollback();
            return $th->getMessage();
        }
        catch (\PDOException $th) {
            return response("Permission to DB denied",403);
        }
    }

    public function ListUserGroups($User){
        $Integrates = integrates::with('group')->get()->where('id_user', $User->id);
        return $Integrates;
    }

    public function CheckUserGroup($id_user, $id_group){
        $Integrates = integrates::get()->where('id_user', $id_user)->where('id_group', $id_group);
        return $Integrates;
    }

    public function UserIntegrate($id_user, $id_group){
        $Integrate = integrates::get()->where('id_user', $id_user)->where('id_group', $id_group)->first();
        return $Integrate;
    }

    public function ListGroupIntegrates($Id){
        $Integrates = integrates::with('user')->get()->where('id_group', $Id);

        //check integrates is not empty
        if ($Integrates->isEmpty())
        return response()->json("Group is empty or not valid", 405);

        $Users = array();
        foreach ($Integrates as $Integrate){
            array_push($Users, $Integrate->user);
        }
        return $Users;

    }

    public function ValidateAdmin($User, $Id){
        $Integrate = self::UserIntegrate($User->id, $Id);
        if ($Integrate == null)
        return response()->json("User is not part of the group", 405);

        return($Integrate->rol == "Admin");
    }


    public function ExpellUserValidation(request $request){
        $validation = Validator::make($request->all(),[
            'id_group'=> 'required | exists:groups,id_group',
            'id_user'=> 'required | exists:users,id',
        ]);
        return $validation;
    }

    public function ExpelUser(request $request, $Id){

        $validation = self::ExpellUserValidation($request);
        if ($validation->fails())
        return $validation->errors();

        $expelled = $request->post("id_user");
        
        $id_group = $request->post("id_group"); 
        $User = self::GetUser($request);
    
        if ($expelled == $User->id)
        return response()->json("Users can't expel themselves", 405);

        $UserGroup = self::UserIntegrate($User->id, $id_group);
        if ($UserGroup == null)
        return response()->json("User is not part of the group", 405);

        if (self::ValidateAdmin($User, $id_group) == false)
        return response()->json("User is not admin", 405);

        
        $Integrate = self::UserIntegrate($expelled, $id_group);
        if ($Integrate == null)
        return response()->json("Expelled user is not part of the group", 405);
        
        

        $Group = new Groups();      
        $Group = $Group::findOrFail($id_group);
        $id_chat = $Group->id_chat;        

        return response (self::ExpelUserRequest($expelled, $id_chat, $Integrate), 201);
    }

    public function ExpelUserRequest($expelled, $id_chat, $Integrate){
        $chatController = new ChatController();
        
        try {
            DB::raw('LOCK TABLE integrates WRITE');
            DB::beginTransaction();
            
            $Integrate->delete();
            $chatController->ExpelUser($id_chat, $expelled);
            DB::commit();
            DB::raw('UNLOCK TABLES');
            return ("User expelled");
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
