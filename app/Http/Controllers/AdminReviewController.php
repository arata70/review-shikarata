<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminReviewController extends Controller
{
    public function index(): View
    {
        return view('admin-reviews');
    }

    public function data(): JsonResponse
    {
        $reviews = Review::query()
            ->orderBy('id')
            ->get();

        return response()->json([
            'total' => $reviews->count(),
            'reviews' => $reviews->values()->map(function (Review $review, int $index): array {
                return [
                    'id' => $review->id,
                    'queue_number' => $index + 1,
                    'name' => $review->name,
                    'uid' => $review->uid,
                    'message' => $review->review,
                    'created_at' => $review->created_at?->toIso8601String(),
                ];
            }),
        ]);
    }

    public function destroy(Review $review): RedirectResponse|JsonResponse
    {
        $review->delete();

        if (request()->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return redirect()->route('admin.reviews.index');
    }
}
