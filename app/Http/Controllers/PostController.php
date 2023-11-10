<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\post;
use Illuminate\Support\Facades\DB;
class PostController extends Controller
{

    public function listPostsFromGroup($id_group){
        $posts = Post::where('fk_id_group', $id_group)->get();
        return $posts;
    }
}
