<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    protected $filePath = 'products.json';

    public function index()
    {
        $products = $this->readData();
        usort($products, fn($a, $b) => strtotime($b['datetime']) <=> strtotime($a['datetime']));
        return response()->json($products);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'quantity' => 'required|numeric',
            'price' => 'required|numeric',
        ]);

        $products = $this->readData();

        $newProduct = [
            'id' => uniqid(),
            'name' => $request->name,
            'quantity' => $request->quantity,
            'price' => $request->price,
            'datetime' => now()->toDateTimeString(),
            'total_value' => $request->quantity * $request->price,
        ];

        $products[] = $newProduct;
        $this->writeData($products);

        return response()->json($newProduct);
    }

    public function update(Request $request, $id)
    {
        $products = $this->readData();

        foreach ($products as &$product) {
            if ($product['id'] === $id) {
                $product['name'] = $request->name;
                $product['quantity'] = $request->quantity;
                $product['price'] = $request->price;
                $product['datetime'] = now()->toDateTimeString();
                $product['total_value'] = $request->quantity * $request->price;
                break;
            }
        }

        $this->writeData($products);

        return response()->json(['status' => 'success']);
    }

    private function readData()
    {
        if (!Storage::exists($this->filePath)) {
            Storage::put($this->filePath, json_encode([]));
        }

        return json_decode(Storage::get($this->filePath), true);
    }

    private function writeData($data)
    {
        Storage::put($this->filePath, json_encode($data, JSON_PRETTY_PRINT));
    }
}
