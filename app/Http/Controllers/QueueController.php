<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class QueueController extends Controller
{
    public function overlay(): View
    {
        return view('overlay');
    }

    public function index(): JsonResponse
    {
        $allQuery = Review::query()->orderBy('id');
        $total = (clone $allQuery)->count();

        $queue = (clone $allQuery)
            ->limit(10)
            ->get()
            ->values()
            ->map(function (Review $review, int $index): array {
                return [
                    'id' => $review->id,
                    'queue_number' => $index + 1,
                    'name' => $review->name,
                    'uid' => $review->uid,
                    'message' => $review->review,
                ];
            });

        $recentSubmissions = Review::query()
            ->latest('id')
            ->limit(10)
            ->get()
            ->reverse()
            ->values()
            ->map(fn (Review $review): array => [
                'id' => $review->id,
                'name' => $review->name,
            ]);

        return response()->json([
            'total' => $total,
            'queue' => $queue,
            'recent_submissions' => $recentSubmissions,
        ]);
    }
}
