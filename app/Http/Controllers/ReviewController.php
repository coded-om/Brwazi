<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

#[\Illuminate\Routing\Controllers\Middleware(['auth'])]
class ReviewController extends Controller
{

    public function store(Request $request, Book $book)
    {
        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'content' => ['required', 'string', 'max:2000'],
        ]);

        Review::create([
            'book_id' => $book->id,
            'user_id' => Auth::id(),
            'name' => Auth::user()?->name,
            'rating' => $data['rating'],
            'content' => $data['content'],
            // approved defaults to false (moderation)
        ]);

        return back()->with('success', 'تم إرسال المراجعة وستظهر بعد اعتماد الإدارة.');
    }
}
