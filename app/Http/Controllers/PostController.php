<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\post;
use App\Models\groups;
use Illuminate\Support\Facades\DB;
class PostController extends Controller
{

    public function ListGroupPosts($id){
        $Group = groups::findOrFail($id);
        $Posts = $Group->posts()->get();
        return $Posts;
    }
}
