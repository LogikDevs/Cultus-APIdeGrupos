<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
class IntegratesTest extends TestCase
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

    public function test_ListOneGoodRequest(){
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->BearerToken])->get('/api/v1/integrates/100');
            
        $response -> assertStatus(200);
        $response->assertJsonStructure([
            [
            "id",
            "name",
            "surname",
            "age",
            "gender",
            "email",
            "profile_pic",
            "description",
            "homeland",
            "residence",
            "created_at",
            "updated_at",
            "deleted_at",
            ]
        ]);
    }

    public function test_ListOneBadRequest(){
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->BearerToken])->get('/api/v1/integrates/11111111111');
            
        $response -> assertStatus(405);
    }



    public function test_ExpelUserGoodRequest(){
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->BearerToken])->post('/api/v1/integrate/100',
        [
            "id_user" => 1,
            "id_group" => 100,
        ]);
        $response -> assertStatus(201);
    }

    public function test_ExpelUserSelf(){
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->BearerToken])->post('/api/v1/integrate/100',
        [
            "id_user" => 11,
            "id_group" => 100,
        ]);
        $response -> assertStatus(405);
    }

    public function test_ExpelUserNoGroup(){
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->BearerToken])->post('/api/v1/integrate/100',
        [
            "id_user" => 1,
            "id_group" => 1,
        ]);
        $response -> assertStatus(405);
    }

    public function test_ExpelUserNoAdmin(){
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->BearerToken])->post('/api/v1/integrate/100',
        [
            "id_user" => 2,
            "id_group" => 101,
        ]);
        $response -> assertStatus(405);
    }

    public function test_JoinGroupGoodRequest(){
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->BearerToken])->post('/api/v1/group/join',
        [
            "id_group" => 1,
        ]);
        $response -> assertStatus(201);
    }

    public function test_JoinGroupBadRequest(){
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->BearerToken])->post('/api/v1/group/join',
        [
            "id_group" => "aa",
        ]);
        $response -> assertStatus(200);
    }

    
}