<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::orderBy('created_at', 'desc')->get();
        return view('products.index', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
        ]);

        $product = Product::create([
            'name' => $request->name,
            'quantity' => $request->quantity,
            'price' => $request->price,
        ]);

        // Save to JSON file as required
        $this->saveToJsonFile();

        if ($request->ajax()) {
            $products = Product::orderBy('created_at', 'desc')->get();
            $totalSum = $products->sum('total_value');
            
            return response()->json([
                'success' => true,
                'product' => $product,
                'products' => $products,
                'totalSum' => $totalSum,
                'html' => view('products.table', compact('products', 'totalSum'))->render()
            ]);
        }

        return redirect()->back()->with('success', 'Product added successfully!');
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
        ]);

        $product->update([
            'name' => $request->name,
            'quantity' => $request->quantity,
            'price' => $request->price,
        ]);

        // Save to JSON file as required
        $this->saveToJsonFile();

        if ($request->ajax()) {
            $products = Product::orderBy('created_at', 'desc')->get();
            $totalSum = $products->sum('total_value');
            
            return response()->json([
                'success' => true,
                'product' => $product,
                'products' => $products,
                'totalSum' => $totalSum,
                'html' => view('products.table', compact('products', 'totalSum'))->render()
            ]);
        }

        return redirect()->back()->with('success', 'Product updated successfully!');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        
        // Save to JSON file as required
        $this->saveToJsonFile();

        if (request()->ajax()) {
            $products = Product::orderBy('created_at', 'desc')->get();
            $totalSum = $products->sum('total_value');
            
            return response()->json([
                'success' => true,
                'products' => $products,
                'totalSum' => $totalSum,
                'html' => view('products.table', compact('products', 'totalSum'))->render()
            ]);
        }

        return redirect()->back()->with('success', 'Product deleted successfully!');
    }

    private function saveToJsonFile()
    {
        $products = Product::orderBy('created_at', 'desc')->get();
        $jsonData = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'quantity' => $product->quantity,
                'price' => $product->price,
                'total_value' => $product->total_value,
                'datetime_submitted' => $product->created_at->format('Y-m-d H:i:s'),
            ];
        });

        Storage::disk('public')->put('products.json', json_encode($jsonData, JSON_PRETTY_PRINT));
    }
}
