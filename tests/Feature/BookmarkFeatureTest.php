<?php

namespace Tests\Feature;

use App\Models\Bookmark;
use App\Models\SharedBookmark;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class BookmarkFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_all_bookmarks()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();
        Bookmark::factory()->create(['user_id' => $user->id]);

        $token = auth()->login($admin);

        $response = $this->withHeader('Authorization', "Bearer $token")
                         ->getJson('/api/admin/bookmarks');

        $response->assertStatus(200)
                 ->assertJsonStructure(['status', 'data']);
    }

    public function test_non_admin_cannot_access_admin_route()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        $response = $this->withHeader('Authorization', "Bearer $token")
                         ->getJson('/api/admin/bookmarks');

        $response->assertStatus(403)
                 ->assertJsonFragment(['message' => 'Unauthorized. Admins only.']);
    }

    public function test_tags_are_stored_as_comma_separated_string()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        $payload = [
            'title' => 'Test',
            'url' => 'https://example.com',
            'tags' => ['laravel', 'php'],
            'category' => 'Framework'
        ];

        $this->withHeader('Authorization', "Bearer $token")
             ->postJson('/api/bookmarks', $payload);

        $this->assertDatabaseHas('bookmarks', [
            'title' => 'Test',
            'tags' => 'laravel,php'
        ]);
    }

    public function test_shared_bookmark_link_allows_public_access()
    {
        $user = User::factory()->create();
        $bookmark = Bookmark::factory()->create(['user_id' => $user->id]);

        $token = Str::random(32);
        SharedBookmark::create([
            'bookmark_id' => $bookmark->id,
            'token' => $token,
            'expires_at' => now()->addDays(1)
        ]);

        $response = $this->getJson("/api/shared/bookmarks/{$token}");

        $response->assertStatus(200)
                 ->assertJsonFragment(['title' => $bookmark->title]);
    }
}
