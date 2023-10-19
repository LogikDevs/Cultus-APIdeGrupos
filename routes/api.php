<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GroupsController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\IntegratesController;
use App\Http\Middleware\Autenticacion;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->middleware(Autenticacion::class)->group(function(){

    Route::get("/group/{d}",[GroupsController::class, "ListOne"]);
    Route::get("/group", [GroupsController::class, "ListAll"]);
    Route::post("/group", [GroupsController::class, "Create"]);
    Route::put("/group/name", [GroupsController::class, "EditName"]);
    
    Route::get("/chat/{d}", [ChatController::class, "GetChat"]);
    Route::post("/message", [ChatController::class, "SendMessage"]);
    Route::get("/chats", [GroupsController::class, "ListUserGroups"]);
    Route::get("/integrates/{d}", [IntegratesController::class, "ListGroupIntegrates"]);
    Route::get("/leave/{d}", [GroupsController::class, "LeaveGroup"]);
});