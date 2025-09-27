<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Publisher;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;

class LiteraryController extends Controller
{
    public function index()
    {
        $publishers = Publisher::query()->orderBy('name')->take(8)->get();
        $books = Book::query()->with(['authors', 'publisher', 'categories'])->latest('id')->take(12)->get();
        return view("literaryViwes.index", compact('publishers', 'books'));
    }
    public function allLitetaty()
    {
        return view("literaryViwes.allLitetaty");
    }
    public function book($id)
    {
        $book = Book::with(['authors', 'publisher', 'categories', 'images', 'reviews' => fn($q) => $q->where('approved', true)->latest()])->findOrFail($id);
        $user = Auth::user();
        // Basic purchase check: has any confirmed/paid order item for this book's title (placeholder until book-linked items exist)
        $hasPurchased = false;
        if ($user) {
            $hasPurchased = OrderItem::whereHas('order', function ($q) use ($user) {
                $q->where('user_id', $user->id)->where('payment_status', 'paid');
            })->where('title', $book->title)->exists();
        }
        return view("literaryViwes.book", compact('book', 'hasPurchased'));
    }
}
