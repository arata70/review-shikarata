<?php
namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function create()
    {
        return view('review-form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'uid' => ['nullable', 'string', 'max:120'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        Review::create([
            'name' => trim($validated['name']),
            'uid' => isset($validated['uid']) && $validated['uid'] !== '' ? trim($validated['uid']) : null,
            'review' => trim($validated['message']),
        ]);

        return redirect()
            ->route('review.create')
            ->with('success', 'Review berhasil dikirim.');
    }
}
