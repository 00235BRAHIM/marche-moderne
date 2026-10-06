<?php

namespace App\Http\Controllers;

use App\Models\{Cart, CartItem, Product};
use Illuminate\Http\Request;

class CartController extends Controller
{
    private function cart()
    {
        return auth()->user()->cart()->firstOrCreate();
    }

    public function index()
    {
        return view('cart.index', [
            'cart' => $this->cart()->load('items.product')
        ]);
    }

    public function add(Request $r, Product $product)
    {
        $q = $r->integer('quantity', 1);

        abort_if(
            $q < 1 || $q > $product->stock,
            422,
            'Stock insuffisant.'
        );

        $c = $this->cart();

        $i = $c->items()->firstOrNew([
            'product_id' => $product->id
        ]);

        $i->quantity = min(
            $product->stock,
            ($i->quantity ?? 0) + $q
        );

        $i->unit_price = $product->price;
        $i->save();

        return back()->with(
            'success',
            'Produit ajouté au panier.'
        );
    }

    public function update(Request $r, CartItem $item)
    {
        abort_unless(
            (int) $item->cart->user_id === (int) auth()->id(),
            403
        );

        $d = $r->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        abort_if(
            $d['quantity'] > $item->product->stock,
            422,
            'Stock insuffisant.'
        );

        $item->update($d);

        return back()->with(
            'success',
            'Quantité du panier mise à jour.'
        );
    }

    public function remove(CartItem $item)
    {
        abort_unless(
            (int) $item->cart->user_id === (int) auth()->id(),
            403
        );

        $item->delete();

        return back()->with(
            'success',
            'Produit retiré du panier.'
        );
    }
}
