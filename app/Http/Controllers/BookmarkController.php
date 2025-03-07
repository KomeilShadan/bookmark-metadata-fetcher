<?php

namespace App\Http\Controllers;

use App\Jobs\FetchMetadataJob;
use App\Models\Bookmark;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BookmarkController extends Controller
{
    public function index(): JsonResponse
    {
        $bookmarks = Bookmark::all();
        return response()->json(['data' => $bookmarks]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'url' => 'required|url',
        ]);

        $bookmark = Bookmark::query()->create([
            'url' => $request->url,
        ]);

        FetchMetadataJob::dispatch($bookmark);

        return response()->json(['message' => 'Bookmark submitted successfully', 'data' => $bookmark], Response::HTTP_CREATED);
    }
}
