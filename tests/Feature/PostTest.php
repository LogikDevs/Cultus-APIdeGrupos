<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
class PostTest extends TestCase
{

    private $BearerToken;
    
    public function setUp() :void{
     parent::setUp();
     $tokenHeader = [ "Content-Type" => "application/json"];
     $Bearer = Http::withHeaders($tokenHeader)->post(getenv("API_AUTH_URL") . "/oauth/token",
      [
         'username' => getenv("USERNAME"),
         'password' => getenv("USERPASSWORD"),
         "grant_type" => "password",
         'client_id' => getenv("CLIENTID"),
         'client_secret' => getenv("CLIENTSECRET"),
     ])->json();
     $this->BearerToken = $Bearer['access_token'];
    }

    public function testListGroupPosts()
    {
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->BearerToken])->get('/api/v1/group/posts/1');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*'=>[
            "id_post",
            "text",
            "latitud",
            "longitud",
            "comments",
            "votes",
            "fk_id_group",
            ]
        ]);
    }
}
