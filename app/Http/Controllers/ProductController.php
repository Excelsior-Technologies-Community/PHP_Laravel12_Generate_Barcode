<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use DNS1D; // Barcode generator class

class ProductController extends Controller
{
    // Display all products
    public function index()
    {
        $products = Product::latest()->get();
        return view('products.index', compact('products'));
    }

    // Show form to create new product
    public function create()
    {
        return view('products.create');
    }

    // Store new product
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:100|unique:products,sku',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $productData = [
            'name' => $request->name,
            'sku' => $request->sku,
            'price' => $request->price,
            'description' => $request->description,
        ];

        // Generate barcode if checkbox is checked
        if ($request->has('generate_barcode')) {
            $productData['barcode'] = $this->generateUniqueBarcode($request->sku);
        }

        Product::create($productData);

        return redirect()->route('products.index')
            ->with('success', 'Product created successfully!');
    }

    // Display specific product
    public function show($id)
    {
        $product = Product::findOrFail($id);
        return view('products.show', compact('product'));
    }

    // Show form to edit product
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        return view('products.edit', compact('product'));
    }

    // Update product
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:100|unique:products,sku,' . $id,
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $product->name = $request->name;
        $product->sku = $request->sku;
        $product->price = $request->price;
        $product->description = $request->description;

        // Regenerate barcode if checkbox is checked
        if ($request->has('regenerate_barcode')) {
            $product->barcode = $this->generateUniqueBarcode($request->sku);
        }

        $product->save();

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully!');
    }

    // Delete product
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully!');
    }

    // Generate barcode image for a product
    public function barcodeImage($id)
    {
        $product = Product::findOrFail($id);
        
        if (!$product->barcode) {
            abort(404, 'No barcode found for this product');
        }

        // Generate barcode as PNG image
        $barcode = DNS1D::getBarcodePNG($product->barcode, 'C128');
        
        // Return the image as response
        return response(base64_decode($barcode))
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', 'inline; filename="barcode-' . $product->sku . '.png"');
    }

    // Generate barcode for any code (demo)
    public function generateBarcode(Request $request)
    {
        $code = $request->get('code', '1234567890');
        
        // Redirect to product show page if we're generating for a product
        if ($request->has('product_id')) {
            return redirect()->route('products.show', $request->product_id);
        }
        
        // Otherwise show demo
        return redirect()->route('products.show', 1)->with('code', $code);
    }

    // Helper method to generate unique barcode
    private function generateUniqueBarcode($sku)
    {
        // Use SKU or generate unique barcode
        $barcode = 'BC-' . strtoupper(substr(md5($sku . time()), 0, 10));
        
        // Check if barcode already exists (unlikely but good practice)
        while (Product::where('barcode', $barcode)->exists()) {
            $barcode = 'BC-' . strtoupper(substr(md5($sku . time() . rand(1000, 9999)), 0, 10));
        }
        
        return $barcode;
    }
}