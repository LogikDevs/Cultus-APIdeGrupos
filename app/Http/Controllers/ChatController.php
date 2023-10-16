<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\user;
use Chat;
use Illuminate\Support\Facades\Validator;
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

    public function MessageValidation(request $request){    
        $validation = Validator::make($request->all(),[
            'text'=>'required | string',           
        ]);
        return $validation;
}

    public function SendMessage(request $request){
        $user = self::user($request);
        $conversation = self::ListOneConversation($request);
        if (!$conversation){
            return "Conversation does not exist";
        }
        if (!self::CheckUserGroup($user, $conversation)){
            return "User is not part of this conversation";
        } 

        $validation = self::MessageValidation($request);
        if ($validation->fails()){
            return $validation->errors();
        }
        $text = $request->post("text");   
        return self::SendMessageRequest($text, $user, $conversation);
    }
    
    public function SendMessageRequest($text, $user, $conversation){ 
        $message = Chat::message($text)
            ->from($user)
            ->to($conversation)
            ->send();
 
            return response($message, 201);
    }

/*
    public function ChatData(request $request, $conversation){
        $data = ['name' => $request->post("name"), 'description' => $request->post("description")];
        $conversation->update(['data' => $data]);
    }
    */
}
