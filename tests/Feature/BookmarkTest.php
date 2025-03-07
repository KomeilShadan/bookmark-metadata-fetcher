<?php

namespace Tests\Feature;

use App\Models\Bookmark;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class BookmarkTest extends TestCase
{
    use RefreshDatabase;

    public function test_retrieve_all_bookmarks(): void
    {
        Bookmark::factory(5)->create();

        $response = $this->getJson('/api/bookmarks');

        $response->assertStatus(Response::HTTP_OK)
                 ->assertJsonStructure(['data' => [['id', 'url', 'title', 'description', 'created_at']]]);
    }

    public function test_retrieve_empty_bookmarks(): void
    {
        $response = $this->getJson('/api/bookmarks');

        $response->assertStatus(Response::HTTP_OK)
                 ->assertJson(['data' => []]);
    }

    public function test_store_bookmark_with_valid_url(): void
    {
        $validUrl = 'https://google.com';
        $response = $this->postJson('/api/bookmarks', [
            'url' => $validUrl,
        ]);

        $response->assertStatus(Response::HTTP_CREATED)
                 ->assertJson([
                     'message' => 'Bookmark submitted successfully!',
                 ]);

        $this->assertDatabaseHas('bookmarks', [
            'url' => $validUrl,
        ]);
    }

    public function test_store_bookmark_with_invalid_url(): void
    {
        $response = $this->postJson('/api/bookmarks', [
            'url' => 'https://invalid-url-123456.com',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
                 ->assertJsonValidationErrors(['url']);
    }

    public function test_store_bookmark_with_missing_url(): void
    {
        $response = $this->postJson('/api/bookmarks', []);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
                 ->assertJsonValidationErrors(['url']);
    }
}
