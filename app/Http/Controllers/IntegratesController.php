<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\integrates;
use App\Models\user;
use Illuminate\Support\Facades\Validator;
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
        if ($group->isEmpty())
        return response()->json(self::JoinGroupRequest($user->id, $request->post("id_group"), "Member"), 201);

        return "User is already part of the group";
    }

    public function JoinGroupValidation(request $request){
        $validation = Validator::make($request->all(),[
            'id_group'=> 'required | exists:groups,id_group',
        ]);
        return $validation;
    }

    public function JoinGroupRequest(int $id_user, int $id_group, string $rol){
        $Integrates = new integrates();

        $Integrates -> id_user = $id_user;
        $Integrates -> id_group = $id_group;
        $Integrates -> rol = $rol;

        try {
            DB::raw('LOCK TABLE integrates WRITE');
            DB::beginTransaction();
             $Integrates -> save();
            DB::commit();
            DB::raw('UNLOCK TABLES');
             return $Integrates;
        
             
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

    public function UserIntegrate($User, $Id){
        $Integrate = integrates::get()->where('id_user', $User->id)->where('id_group', $Id)->first();
        return $Integrate;
    }

    public function ListGroupIntegrates($Id){
        $Integrates = integrates::with('user')->get()->where('id_group', $Id);

        $users = $Integrates->map(function ($integrate) {
            return $integrate->user;
        })->all();
        return $users;
    }

    /*
    try {
            DB::raw('LOCK TABLE integrates WRITE');
            DB::beginTransaction();
            //codigo
            DB::commit();
            DB::raw('UNLOCK TABLES');
            return ["response" => "respuesta", codigo];
        }
        catch (\Illuminate\Database\QueryException $th) {
            DB::rollback();
            return $th->getMessage();
        }
        catch (\PDOException $th) {
            return response("Permission to DB denied",403);

        }
    */
}
