<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


class GroupsTest extends TestCase
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
    $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->BearerToken])->get('/api/v1/group/1');
        
    $response -> assertStatus(200);
    $response->assertJsonStructure([
        "id_group",
        "name",
        "description",
        "picture",
        "privacy",
    ]);
    $response -> assertJsonFragment([
        "id_group"=> 1,
        "name"=>"grupoTest" ,
    ]);
    }
    public function test_ListOneBadRequest(){
    $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->BearerToken])->get('/api/v1/group/100000');
    $response -> assertStatus(404);
    }
    public function test_ListOneBadRequestNoBearer(){
    $response = $this->get('/api/v1/group/1');
    $response -> assertStatus(403);
    }

    public function test_ListAllGoodRequest(){
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->BearerToken])->get('/api/v1/group');
        $response -> assertStatus(200);
    }

    public function test_ListAllBadRequest(){
        $response = $this->get('/api/v1/group');
        $response -> assertStatus(403);
    }


    public function test_CreateGoodRequest(){
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->BearerToken])->post('/api/v1/group',
        [
            "name"=> "grupopro",
            "description"=> "pro",
            "privacy"=> "public",
            "id_user" => 11
        ]);
    $response -> assertStatus(201);
    $response -> assertJsonStructure([
       [
        0=>[
            "name",
            "description",
            "privacy",
        ],
        1=>[
            "id_user",
            "id_group",
            "rol",
        ]
        ]
    ]);
    $this->assertDatabaseHas('groups', [
        "name"=> "grupopro",
        "description"=> "pro",
        "privacy"=> "public",
        "deleted_at" =>null
    ]);
    $this->assertDatabaseHas('integrates', [
        "id_user"=> 11,
        "rol"=>"Admin",
        "deleted_at" =>null
    ]);
    }
    public function test_CreateBadRequest(){
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->BearerToken])->post('/api/v1/group',
        [
            "description"=> "aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
            "privacy"=> "hola",
        ]);
    $response -> assertStatus(200);

    $response -> assertJsonFragment([
        "name"=> ["The name field is required."],
        "description"=>["The description must not be greater than 255 characters."],
        "privacy"=>["The selected privacy is invalid."],
]);
    }
    
    public function test_CreateBadRequestNoBearer(){
        $response = $this->post('/api/v1/group',
        [
            "description"=> "aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
            "privacy"=> "hola",
            "id_user" => 1111111111111111
        ]);
        $response -> assertStatus(403);
    }


    public function test_EditNameGoodRequest(){
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->BearerToken])->put('/api/v1/group/name',
        [
            "name"=> "editado",
            "id_group"=> 1
        ]);
    $response -> assertStatus(201);
    $response -> assertJsonStructure([
        "*"=>[
            "name",
            "id_group"
        ],
    ]);
    $this->assertDatabaseHas('groups', [
        "name"=> "editado",
        "id_group"=> 1,
        "deleted_at" =>null
    ]);
    }

    public function test_EditNameBadRequest(){
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->BearerToken])->put('/api/v1/group/name',
        [
            "name"=> 1,
            "id_group"=> 1111111111
        ]);
    $response -> assertStatus(200);

    $response -> assertJsonFragment([
        "name"=> ["The name must be a string."],
        "id_group"=>["The selected id group is invalid."],
    ]);
    }

    public function test_EditNameBadRequestNoBearer(){
        $response = $this->put('/api/v1/group/name',
        [
            "name"=> 1,
            "id_group"=> 1111111111
        ]);
        $response -> assertStatus(403);
    }
}
