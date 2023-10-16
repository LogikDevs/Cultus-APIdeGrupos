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
    public function CheckUserGroup($user, $conversation){
        $conversations = Chat::conversations()->setParticipant($user)->get()->where('conversation_id', $conversation->id);
        if(!$conversations->isEmpty()){
        return true;
        }
        return false;
    }
    public function CreateChat(request $request){
        $user = self::user($request);
        $participants = [$user];
        $conversation = Chat::createConversation($participants)->makePrivate(false);
        return $conversation;
    }
    
    public function ListOneConversation(request $request){
        $id = $request->post("id_chat");
        $conversation = Chat::conversations()->getById($id); 
        return $conversation;
    }

    public function SendMessage(request $request){
        $user = self::user($request);
        $conversation = self::ListOneConversation($request);
        if ($conversation){
          if (self::CheckUserGroup($user, $conversation)){
            $text = $request->post("text");
            $message = Chat::message($text)
                ->from($user)
                ->to($conversation)
                ->send();
            return $message;
          }
          return "User is not part of this conversation";
        }
        return "Conversation does not exist";
    }
/*
    public function ChatData(request $request, $conversation){
        $data = ['name' => $request->post("name"), 'description' => $request->post("description")];
        $conversation->update(['data' => $data]);
    }
    */
}
