<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\user;
use Chat;
class ChatController extends Controller
{
    public function user(request $request){
        $userData = $request->user;
        $user = new User();
        $user->fill($userData);
        return $user;
    }

    public function CreateChat(request $request){
        $user = self::user($request);
        $participants = [$user];
        $conversation = Chat::createConversation($participants)->makePrivate(false);
        return $conversation;
    }
/*
    public function ChatData(request $request, $conversation){
        $data = ['name' => $request->post("name"), 'description' => $request->post("description")];
        $conversation->update(['data' => $data]);
    }
    */
}
