<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GroupsController;
use App\Http\Middleware\Autenticacion;
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::prefix('v1')->group(function(){

    Route::get("/group/{d}",[GroupsController::class,"ListOne"])->middleware(Autenticacion::class);

});