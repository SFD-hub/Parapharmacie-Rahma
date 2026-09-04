<?php

namespace App\Http\Controllers\Admin\Marketing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductComplement\StoreProductComplementRequest;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProductComplementController extends Controller
{
    public function index(Product $product): View
    {
        $product->load('complements');
        $availableProducts = Product::query()
            ->whereKeyNot($product->id)
            ->whereNotIn('id', $product->complements->pluck('id'))
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.marketing.product-complements.index', compact('product', 'availableProducts'));
    }

    public function store(StoreProductComplementRequest $request, Product $product): RedirectResponse
    {
        $position = (int) $product->complements()->count();

        $product->complements()->attach($request->validated('complementary_product_id'), ['position' => $position]);

        return redirect()->route('admin.products.complements.index', $product)->with('success', 'Produit complémentaire ajouté avec succès.');
    }

    public function destroy(Product $product, Product $complement): RedirectResponse
    {
        $product->complements()->detach($complement->id);

        return redirect()->route('admin.products.complements.index', $product)->with('success', 'Association supprimée avec succès.');
    }
}
