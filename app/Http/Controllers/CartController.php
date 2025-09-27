<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session('cart', []);
        $items = [];
        $subtotal = 0;
        foreach ($cart as $artworkId => $qty) {
            $art = Artwork::find($artworkId);
            if (!$art)
                continue;
            $price = (float) ($art->price ?? 0);
            $items[] = [
                'id' => $art->id,
                'title' => $art->title,
                'image' => $art->primary_image_url,
                'artist' => $art->user?->full_name ?? $art->user?->fname . ' ' . $art->user?->lname,
                'price' => $price,
                'qty' => (int) $qty,
            ];
            $subtotal += $price * (int) $qty;
        }
        $discount = round($subtotal * 0.2, 3); // demo 20% like screenshot
        $shipping = 15.000; // flat as screenshot
        $total = max(0, $subtotal - $discount + $shipping);

        return view('cart.index', compact('items', 'subtotal', 'discount', 'shipping', 'total'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|integer',
            'items.*.qty' => 'required|integer|min:0|max:99',
        ]);
        $cart = session('cart', []);
        foreach ($data['items'] as $row) {
            $id = (int) $row['id'];
            $qty = (int) $row['qty'];
            if ($qty > 0) {
                $cart[$id] = $qty;
            } else {
                unset($cart[$id]);
            }
        }
        session(['cart' => $cart]);
        return back()->with('success', 'تم تحديث السلة');
    }

    public function remove(Request $request, int $artworkId)
    {
        $cart = session('cart', []);
        unset($cart[$artworkId]);
        session(['cart' => $cart]);
        return back()->with('success', 'تم حذف المنتج من السلة');
    }
}
