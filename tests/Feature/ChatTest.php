<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatTest extends TestCase
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

    public function test_sendMessageGoodRequest()
    {
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->BearerToken])->post('/api/v1/message', [
            'id_chat' => 100,
            'text' => 'test message',
        ]);
        $response->assertStatus(201);
    }


    public function test_getChatGoodRequest()
    {
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->BearerToken])->get('/api/v1/chat/100');
        $jsonData = json_decode($response->getContent(), true);
        $this->assertTrue(
        collect($jsonData['data'])->contains('body', 'test message'),
        );
        $response->assertStatus(200);
    }

    public function test_getChatBadRequest()
    {
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->BearerToken])->get('/api/v1/chat/11111111111');
        $response->assertStatus(200);
        $this->assertEquals('Conversation does not exist', $response->getContent());    
    }


    public function test_getUserGroupsGoodRequest()
    {
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->BearerToken])->get('/api/v1/chats');
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id_group' => 100,
            'id_chat' => 100,
        ]);
    }

    public function test_getUserGroupsBadRequest()
    {
        $response = $this->get('/api/v1/chats');
        $response->assertStatus(403);
    }


    public function test_DeleteMessageGoodRequest(){
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->BearerToken])->post('/api/v1/message', [
            'id_chat' => 100,
            'text' => 'test message',
        ]);
        $id = $response['id'];
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->BearerToken])->delete('/api/v1/message/' . $id);
        $response->assertStatus(201);  
    }
}
