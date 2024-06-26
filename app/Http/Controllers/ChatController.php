<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\user;
use Chat;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
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
        return !$conversations->isEmpty();
    }
    public function CreateChat(request $request){
        $user = self::user($request);
        $participants = [$user];
        try {
            DB::raw('LOCK TABLE integrates WRITE');
            DB::beginTransaction();
             $conversation = Chat::createConversation($participants)->makePrivate(false);
        
            DB::commit();
            DB::raw('UNLOCK TABLES');
            return $conversation;
        }
        catch (\Illuminate\Database\QueryException $th) {
            DB::rollback();
            return $th->getMessage();
        }
        catch (\PDOException $th) {
            return response("Permission to DB denied",403);
        }      
    }
    
    public function JoinChat($id, request $request){
        $user = self::user($request);
        $conversation = self::ListOneConversation($id);
        if (!$conversation){
            return "Conversation does not exist";
        }
        if (self::CheckUserGroup($user, $conversation)){
            return "User is already part of this conversation";
        } 
        try {
            DB::raw('LOCK TABLE chat_participation WRITE');
            DB::beginTransaction();
                Chat::conversation($conversation)->addParticipants([$user]);         
            DB::commit();
            DB::raw('UNLOCK TABLES');
            return true;
        }
        catch (\Illuminate\Database\QueryException $th) {
            DB::rollback();
            return $th->getMessage();
        }
        catch (\PDOException $th) {
            return response("Permission to DB denied",403);
        }      
    }
    

    public function ListOneConversation($id){
        $conversation = Chat::conversations()->getById($id); 
        return $conversation;
    }

    public function MessageValidation(request $request){    
        $validation = Validator::make($request->all(),[
            'text'=>'required | string',           
        ]);
        return $validation;
}

    public function FindMessage($id){
        return $message = Chat::messages()->getById($id);
    }

    public function SendMessage(request $request){
        $user = self::user($request);
        $idChat = $request->post("id_chat");
        $conversation = self::ListOneConversation($idChat);
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
        try {
            DB::raw('LOCK TABLE chat_messages WRITE');
            DB::beginTransaction();
        $message = Chat::message($text)
            ->from($user)
            ->to($conversation)
            ->send();
            DB::commit();
            DB::raw('UNLOCK TABLES');
            return response($message, 201);
        }
        catch (\Illuminate\Database\QueryException $th) {
            DB::rollback();
            return $th->getMessage();
        }
        catch (\PDOException $th) {
            return response("Permission to DB denied",403);
        }      
        }

    public function DeleteMessage($id, request $request){
        
        $user = self::user($request);
        $message = self::FindMessage($id);
        if ($message->sender->id != $user->id){
            return "User must be sender to delete";
        }
        if(!$message){
            return "Message not found";
            
        }
        try {
            DB::raw('LOCK TABLE chat_messages WRITE');
            DB::beginTransaction();
            
            $message->delete();
           
            DB::commit();
            DB::raw('UNLOCK TABLES');
            return response("Message deleted succesfully", 201);
        }
        catch (\Illuminate\Database\QueryException $th) {
            DB::rollback();
            return $th->getMessage();
        }
        catch (\PDOException $th) {
            return response("Permission to DB denied",403);

        }      

    }

    public function GetChat($Id, request $request){
        $conversation = self::ListOneConversation($Id);
        if (!$conversation){
            return "Conversation does not exist";
        }
        $user = self::user($request);
        if (!self::CheckUserGroup($user, $conversation)){
            return "User is not part of this conversation";
        } 
        $participants = $conversation->getParticipants();
        return response()->json([chat::conversation($conversation)->setParticipant($user)->getMessages(), $participants], 200);
    }

    public function DirectChatValidation(request $request){
        $validation = Validator::make($request->all(),[
            'id_user'=>'required | integer | exists:users,id'
        ]);
        return $validation;
    }

    public function createDirectChat(request $request){
        $user = self::user($request);
        $validation = self::DirectChatValidation($request);
        if ($validation->fails())
        return $validation->errors();

        $participant = user::findOrFail($request->post("id_user"));
        $participants = [$user, $participant];
        try{
            DB::raw('LOCK TABLE chat_conversations WRITE');
            DB::beginTransaction();
            $conversation = Chat::createConversation($participants)->makeDirect();
            DB::commit();
            DB::raw('UNLOCK TABLES');
            return response ($conversation, 201);
        }
        catch (\Musonza\Chat\Exceptions\DirectMessagingExistsException $th) {
            DB::rollback();
             return response("Conversation already exists", 200);
        }
        catch (\Illuminate\Database\QueryException $th) {
            DB::rollback();
             return $th->getMessage();
        }
        catch (\PDOException $th) {
            return response("Permission to DB denied",403);
        }
    }

    public function ListUserDirectChats(request $request){
        $user = self::user($request);
        return Chat::conversations()->setParticipant($user)->isDirect()->get();
    }

    public function ExpelUser($id_chat, $id_user){
        $user = user::findOrFail($id_user);
        $conversation = self::ListOneConversation($id_chat);
        if (!$conversation){
            return "Conversation does not exist";
        }
        try {
            DB::raw('LOCK TABLE chat_participation WRITE');
            DB::beginTransaction();
            $conversation->removeParticipant([$user]);         
            DB::commit();
            DB::raw('UNLOCK TABLES');

        }
        catch (\Illuminate\Database\QueryException $th) {
            DB::rollback();
            return $th->getMessage();
        }
        catch (\PDOException $th) {
            return response("Permission to DB denied",403);
        }      
    }

    //list chat between 2 users
    public function ListChatBetweenUsers($id, request $request){
        $user = self::user($request);
        //transactions
        try{
        DB::raw('LOCK TABLE users WRITE');
        DB::raw('LOCK TABLE chat_participation WRITE');
        DB::raw('LOCK TABLE chat_conversations WRITE');
        DB::beginTransaction();
        $participant = user::findOrFail($id);
        $conversation = Chat::conversations()->between($user, $participant);
        DB::commit();
        DB::raw('UNLOCK TABLES');
        return response($conversation);
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