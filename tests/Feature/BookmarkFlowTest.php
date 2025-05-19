<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookmarkFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_and_create_bookmark()
    {
        // Register user
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(200);
        $token = $response->json('token');
        $this->assertNotEmpty($token);

        // Create bookmark
        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/bookmarks', [
                'title' => 'Laravel Docs',
                'url' => 'https://laravel.com/docs',
                'tags' => ['php', 'laravel'],
                'category' => 'Frameworks'
            ]);

        $response->assertStatus(201)
                 ->assertJsonFragment(['title' => 'Laravel Docs']);
    }
}
