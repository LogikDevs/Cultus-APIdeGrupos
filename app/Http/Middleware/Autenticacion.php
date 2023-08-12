<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class Autenticacion
{
       public function handle(Request $request, Closure $next){    
        
           $tokenHeader = [ "Authorization" => $request -> header("Authorization")];
           //getenv("API_AUTH_URL") . "
            $response = Http::withHeaders($tokenHeader)->timeout(500)->get("http://api_auth:8000/api/v1/validate");
            
            if($response -> successful())
                return $next($request);
            
            return response(['message' => 'Not Allowed'], 403);
        }

    }