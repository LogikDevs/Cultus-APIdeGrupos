<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GroupsController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\IntegratesController;
use App\Http\Middleware\Autenticacion;


Route::prefix('v1')->middleware(Autenticacion::class)->group(function(){

    Route::get("/group/{d}",[GroupsController::class, "ListOne"]);
    Route::get("/group", [GroupsController::class, "ListAll"]);
    Route::post("/group", [GroupsController::class, "Create"]);
    Route::put("/group/name", [GroupsController::class, "EditName"]);
    Route::post("/group/join", [IntegratesController::class, "JoinGroup"]);

    Route::get("/chat/{d}", [ChatController::class, "GetChat"]);
    Route::post("/message", [ChatController::class, "SendMessage"]);
    Route::delete("/message/{d}", [ChatController::class, "DeleteMessage"]);
    Route::get("/chats", [GroupsController::class, "ListUserGroups"]);
    
    Route::get("/integrates/{d}", [IntegratesController::class, "ListGroupIntegrates"]);
    Route::get("/leave/{d}", [GroupsController::class, "LeaveGroup"]);
    Route::post("/integrate/{d}", [IntegratesController::class, "ExpelUser"]);

    Route::post("/chat/direct", [ChatController::class, "createDirectChat"]);
    Route::get("/chat/get/direct", [ChatController::class, "ListUserDirectChats"]);
    Route::get("/chat/get/direct/{d}", [ChatController::class, "ListChatBetweenUsers"]);


    Route::get("/group/all/{d}", [GroupsController::class, "ListAllGroup"]);
});