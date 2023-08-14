<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GroupsController;
use App\Http\Middleware\Autenticacion;
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::prefix('v1')->middleware(Autenticacion::class)->group(function(){

    Route::get("/group/{d}",[GroupsController::class, "ListOne"]);
    Route::post("/group", [GroupsController::class, "Create"]);

    Route::get("/group", [GroupsController::class, "ListAll"]);
});