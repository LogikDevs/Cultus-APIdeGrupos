<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ChatTest extends TestCase
{
    //test send message
    public function test_sendMessageGoodRequest()
    {
        $response = $this->post('/api/v1/message', [
            'id_chat' => 1,
            'text' => 'test message',
        ]);
        $response->assertStatus(201);
    }


    public function test_getChat()
    {
        $response = $this->get('/api/v1/chat/1');
        $response->assertStatus(200);
    }
}
