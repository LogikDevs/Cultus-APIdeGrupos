<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\integrates;

class IntegratesController extends Controller
{
    public function createAdmin(int $id_user, int $id_group){

        $Integrates = new integrates();

        $Integrates -> id_user = $id_user;
        $Integrates -> id_group = $id_group;
        $Integrates -> rol = 'Admin';

        $Integrates -> save();
    }
}
