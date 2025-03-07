<?php

namespace App\Jobs;

use App\Models\Bookmark;
use DOMDocument;
use GuzzleHttp\Client;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class FetchMetadataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(protected Bookmark $bookmark) {}

    /**
     * Calculate the number of seconds to wait before retrying the job.
     *
     * @return array<int, int>
     */
    public function backoff(): array
    {
        return [1, 5, 10];
    }

    /**
     * Get the middleware the job should pass through.
     *
     * @return array<int, object>
     */
    public function middleware(): array
    {
        return [new WithoutOverlapping($this->bookmark->id)];
    }

    public function handle()
    {
        try {
            $client = new Client();
            $response = $client->get($this->bookmark->url);
            $html = $response->getBody()->getContents();

            // Parse metadata
            $dom = new DOMDocument();
            //ignore warnings or notices if the provided HTML is not well-formed or contains invalid markup.
            @$dom->loadHTML($html);

            $title = $dom->getElementsByTagName('title')->item(0)?->nodeValue;
            $description = '';

            foreach ($dom->getElementsByTagName('meta') as $meta) {
                if (strtolower($meta->getAttribute('name')) === 'description') {
                    $description = $meta->getAttribute('content');
                    break;
                }
            }

            $this->bookmark->update([
                'title' => $title,
                'description' => $description,
            ]);
        }
        catch (Throwable $e) {
            Log::error('Failed to fetch metadata', [
                'bookmark_id' => $this->bookmark->id,
                'url' => $this->bookmark->url,
                'error' => $e->getMessage(),
            ]);
            $this->fail();
        }
    }
}
