<?php

namespace Tests\Feature;

use App\Jobs\FetchMetadataJob;
use App\Models\Bookmark;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class BookmarkTest extends TestCase
{
    use RefreshDatabase;

    public function test_bookmark_auth_middleware(): void
    {
        $this->withoutExceptionHandling();

        $response = $this->getJson('/api/bookmarks');
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);

        $response = $this->postJson('/api/bookmarks', ['url' => 'malware.com']);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_retrieve_all_bookmarks(): void
    {
        $this->withoutExceptionHandling();

        Bookmark::factory(5)->create();

        $response = $this->getJson('/api/bookmarks', ['Authorization' => config('api.api_token')]);

        $response->assertStatus(Response::HTTP_OK)
                 ->assertJsonStructure(['data' => [['id', 'url', 'title', 'description', 'created_at']]]);
    }

    public function test_retrieve_empty_bookmarks(): void
    {
        $this->withoutExceptionHandling();

        $response = $this->getJson('/api/bookmarks', ['Authorization' => config('api.api_token')]);

        $response->assertStatus(Response::HTTP_OK)
                 ->assertJson(['data' => []]);
    }

    public function test_store_bookmark_with_valid_url(): void
    {
        $this->withoutExceptionHandling();
        Queue::fake();

        $validUrl = 'https://google.com';
        $response = $this->postJson('/api/bookmarks', [
            'url' => $validUrl,
        ],
        [
            'Authorization' => config('api.api_token')
        ]);

        $response->assertStatus(Response::HTTP_CREATED)
                 ->assertJson([
                     'message' => 'Bookmark submitted successfully',
                 ]);

        $this->assertDatabaseHas('bookmarks', [
            'url' => $validUrl,
        ]);
        Queue::assertPushed(FetchMetadataJob::class);
    }

    public function test_store_bookmark_with_invalid_url(): void
    {
        $this->withoutExceptionHandling();
        $this->expectException(ValidationException::class);

        $response = $this->postJson('/api/bookmarks', [
            'url' => 'https://invalid-url-123456.com',
        ],
        [
            'Authorization' => config('api.api_token')
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
                 ->assertJsonValidationErrors(['url']);
    }

    public function test_store_bookmark_with_missing_url(): void
    {
        $this->withoutExceptionHandling();
        $this->expectException(ValidationException::class);

        $response = $this->postJson('/api/bookmarks', [], ['Authorization' => config('api.api_token')]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
                 ->assertJsonValidationErrors(['url']);
    }
}
