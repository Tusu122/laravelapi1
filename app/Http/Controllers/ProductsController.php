<?php

namespace App\Http\Controllers;

use App\Models\Products;
use App\Http\Requests\StoreProductsRequest;
use App\Http\Requests\UpdateProductsRequest;
class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Products::OrderBy('id', 'ASC')->get();
        return ['data' => $data];
    }

    /**
     * Store a newly created resource in storage.
     */
    // App/Http/Controllers/ProductController.php


    public function store(StoreProductsRequest $request)
    {
        // Validate the request (handled by StoreProductsRequest)
        $validated = $request->validated();

        // Handle image upload
        if ($request->hasFile('image')) {
            // Generate unique filename
            $filename = time() . '_' . $request->file('image')->getClientOriginalName();

            // Save image to public/uploads/
            $path = $request->file('image')->move(public_path('uploads'), $filename);

            // Add image path to validated data
            $validated['image'] = '/uploads/' . $filename;
        }

        // Create the product
        $product = Products::create($validated);

        // Return response with full image URL
        return response()->json([
            'status' => 200,
            'message' => 'Product created successfully',
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'qty' => $product->qty,
                'description' => $product->description,
                'status' => $product->status,
                'image' => $product->image ? url($product->image) : null, // Full URL
            ],
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Products $product)
    {
        return response()->json([
            'status' => 200,
            'product' => $product
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $product = Products::find($id);

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'product' => $product
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductsRequest $request, $id)
    {
        $product = Products::find($id);

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found'
            ], 404);
        }

        $product->update($request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Product updated successfully',
            'product' => $product
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $product = Products::find($id);

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found'
            ], 404);
        }

        $product->update(['status' => 0]);

        return response()->json([
            'status' => 'success',
            'message' => 'Product deleted successfully'
        ], 200);
    }
}
