<?php

namespace App\Http\Controllers;

use App\Models\{Product, Category};
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VendorProductController extends Controller
{
    public function index()
    {
        return view('vendor.products', [
            'products' => auth()->user()->products()->latest()->paginate(15)
        ]);
    }

    public function create()
    {
        return view('vendor.form', [
            'product' => new Product,
            'categories' => Category::where('is_active', 1)->get()
        ]);
    }

    public function store(Request $r)
    {
        $d = $r->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|max:180',
            'description' => 'nullable',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048'
        ]);

        $d['vendor_id'] = auth()->id();
        $d['slug'] = Str::slug($d['name']) . '-' . Str::random(5);
        $d['sku'] = 'SK-' . strtoupper(Str::random(8));

        if ($r->hasFile('image')) {
            $d['image'] = $r->file('image')->store('products', 'public');
        }

        Product::create($d);

        return redirect()
            ->route('vendor.products.index')
            ->with('success', 'Produit créé.');
    }

    public function edit(Product $product)
    {
        abort_unless($product->vendor_id == auth()->id(), 403);

        return view('vendor.form', [
            'product' => $product,
            'categories' => Category::where('is_active', 1)->get()
        ]);
    }

    public function update(Request $r, Product $product)
    {
        abort_unless($product->vendor_id == auth()->id(), 403);

        $d = $r->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|max:180',
            'description' => 'nullable',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048'
        ]);

        if ($r->hasFile('image')) {
            $d['image'] = $r->file('image')->store('products', 'public');
        }

        $product->update($d);

        return redirect()
            ->route('vendor.products.index')
            ->with('success', 'Produit mis à jour.');
    }

    public function destroy(Product $product)
    {
        abort_unless($product->vendor_id == auth()->id(), 403);

        $product->delete();

        return back()->with('success', 'Produit supprimé.');
    }
}
