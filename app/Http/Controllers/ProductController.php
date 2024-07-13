<?php

namespace App\Http\Controllers;

use App\Enums\Status;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        return Product::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
        ]);

        $product = new Product($request->all());
        $product->save();

        return response()->json(['message' => 'Product created successfully', 'product' => $product]);
    }

    public function show($id)
    {
        return Product::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'sometimes|required|string',
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric',
            'quantity' => 'sometimes|required|integer',
        ]);

        $product = Product::findOrFail($id);
        $product->fill($request->all());
        $product->save();

        return response()->json(['message' => 'Product updated successfully', 'product' => $product]);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }
}
