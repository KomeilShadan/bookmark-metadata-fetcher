<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookmarkRequest;
use App\Jobs\FetchMetadataJob;
use App\Models\Bookmark;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class BookmarkController extends Controller
{
    public function index(): JsonResponse
    {
        $bookmarks = Bookmark::all();
        return response()->json(['data' => $bookmarks]);
    }

    public function store(StoreBookmarkRequest $request): JsonResponse
    {
        $bookmark = Bookmark::query()->create([
            'url' => $request->url,
        ]);

        FetchMetadataJob::dispatch($bookmark->id);

        return response()->json(['message' => 'Bookmark submitted successfully', 'data' => $bookmark], Response::HTTP_CREATED);
    }
}
