<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'book_id' => ['required', 'exists:books,id'],
                'rating'  => ['required', 'integer', 'min:1', 'max:5'],
                'comment' => ['nullable', 'string', 'max:1000'],
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        $book = Book::findOrFail($validated['book_id']);

        $existing = Review::where('user_id', $request->user()->id)
            ->where('book_id', $validated['book_id'])
            ->first();

        if ($existing) {
            $existing->update([
                'rating'  => $validated['rating'],
                'comment' => $validated['comment'] ?? null,
            ]);

            return response()->json([
                'review'         => $this->reviewData($existing),
                'average_rating' => $book->fresh()->average_rating,
                'reviews_count'  => $book->fresh()->reviews_count,
                'message'        => 'Review berhasil diperbarui.',
            ]);
        }

        $review = Review::create([
            'user_id' => $request->user()->id,
            'book_id' => $validated['book_id'],
            'rating'  => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
        ]);

        return response()->json([
            'review'         => $this->reviewData($review),
            'average_rating' => $book->fresh()->average_rating,
            'reviews_count'  => $book->fresh()->reviews_count,
            'message'        => 'Review berhasil ditambahkan.',
        ]);
    }

    public function destroy(Request $request, Review $review)
    {
        if ($review->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Anda tidak bisa menghapus review milik orang lain.'], 403);
        }

        $book = $review->book;
        $review->delete();

        return response()->json([
            'average_rating' => $book->fresh()->average_rating,
            'reviews_count'  => $book->fresh()->reviews_count,
            'message'        => 'Review berhasil dihapus.',
        ]);
    }

    private function reviewData(Review $review): array
    {
        return [
            'id'         => $review->id,
            'rating'     => $review->rating,
            'comment'    => $review->comment,
            'user_name'  => $review->user->name,
            'user_id'    => $review->user_id,
            'created_at' => $review->created_at->diffForHumans(),
            'can_delete' => true,
        ];
    }
}
