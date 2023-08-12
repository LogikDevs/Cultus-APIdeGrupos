<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\groups;
class GroupsController extends Controller
{
 

    public function ListOne($id){
        return groups::findOrFail($id);
    }

}
