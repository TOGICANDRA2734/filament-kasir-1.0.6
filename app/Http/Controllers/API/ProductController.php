<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * @OA\SecurityScheme(
     *     securityScheme="bearerAuth",
     *     type="http",
     *     scheme="bearer",
     *     bearerFormat="JWT"
     * )
     * 
     * @OA\Get(
     *     path="/api/products",
     *     summary="Get list of products",
     *     tags={"products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Product Name"),
     *                     @OA\Property(property="slug", type="string", example="product-name"),
     *                     @OA\Property(property="category_id", type="integer", example=1),
     *                     @OA\Property(property="stock", type="integer", example=100),
     *                     @OA\Property(property="price", type="number", format="float", example=99.99),
     *                     @OA\Property(property="is_active", type="boolean", example=true),
     *                    @OA\Property(property="image_url", type="string", example="http://example.com/storage/image.jpg"),
     *                     @OA\Property(property="description", type="string", example="Product Description")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        $products = Product::all();
        
        return response()->json([
            'success' => true,
            'data' => $products,
            'message' => 'Sukses menampilkan data'
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/products",
     *     summary="Create a new product",
     *     tags={"products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "category_id", "stock", "price"},
     *             @OA\Property(property="name", type="string", example="Product Name"),
     *             @OA\Property(property="category_id", type="integer", example=1),
     *             @OA\Property(property="stock", type="integer", example=100),
     *             @OA\Property(property="price", type="number", format="float", example=99.99),
     *             @OA\Property(property="is_active", type="boolean", example=true),
     *             @OA\Property(property="image", type="string", example="image.jpg"),
     *             @OA\Property(property="barcode", type="string", example="1213x1412"),
     *             @OA\Property(property="description", type="string", example="Product Description")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Product created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Product created successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Product Name"),
     *                 @OA\Property(property="slug", type="string", example="product-name"),
     *                 @OA\Property(property="category_id", type="integer", example=1),
     *                 @OA\Property(property="stock", type="integer", example=100),
     *                 @OA\Property(property="price", type="number", format="float", example=99.99),
     *                 @OA\Property(property="is_active", type="boolean", example=true),
     *                 @OA\Property(property="image", type="string", example="image.jpg"),
     *                 @OA\Property(property="barcode", type="string", example="1213x1412"),
     *                 @OA\Property(property="description", type="string", example="Product Description")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation Error"),
     *             @OA\Property(property="data", type="object", example=null)
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'category_id' => 'required|integer',
            'stock' => 'required|integer',
            'price' => 'required|numeric',
            'is_active' => 'boolean',
            'image' => 'string',
            'barcode' => 'string',
            'description' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'data' => $validator->errors(),
            ], 422);
        }

        $product = Product::create([
            'name' => $request->name,
            'slug' => Product::generateUniqueSlug($request->name),
            'category_id' => $request->category_id,
            'stock' => $request->stock,
            'price' => $request->price,
            'is_active' => $request->is_active ?? true,
            'image' => $request->image ?? null,
            'barcode' => $request->barcode ?? null,
            'description' => $request->description ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'data' => $product,
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/products/{id}",
     *     summary="Get product details",
     *     tags={"products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Product ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Product Name"),
     *                 @OA\Property(property="slug", type="string", example="product-name"),
     *                 @OA\Property(property="category_id", type="integer", example=1),
     *                 @OA\Property(property="stock", type="integer", example=100),
     *                 @OA\Property(property="price", type="number", format="float", example=99.99),
     *                 @OA\Property(property="is_active", type="boolean", example=true),
     *                 @OA\Property(property="image", type="string", example="image.jpg"),
     *                 @OA\Property(property="barcode", type="string", example="1213x1412"),
     *                 @OA\Property(property="description", type="string", example="Product Description")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Product not found"),
     *             @OA\Property(property="data", type="object", example=null)
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $product,
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/products/{id}",
     *     summary="Update a product",
     *     tags={"products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Product ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "category_id", "stock", "price"},
     *             @OA\Property(property="name", type="string", example="Updated Product Name"),
     *             @OA\Property(property="category_id", type="integer", example=1),
     *             @OA\Property(property="stock", type="integer", example=100),
     *             @OA\Property(property="price", type="number", format="float", example=99.99),
     *             @OA\Property(property="is_active", type="boolean", example=true),
     *             @OA\Property(property="image", type="string", example="image.jpg"),
     *             @OA\Property(property="barcode", type="string", example="1213x1412"),
     *             @OA\Property(property="description", type="string", example="Updated Product Description")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Product updated successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Updated Product Name"),
     *                 @OA\Property(property="slug", type="string", example="updated-product-name"),
     *                 @OA\Property(property="category_id", type="integer", example=1),
     *                 @OA\Property(property="stock", type="integer", example=100),
     *                 @OA\Property(property="price", type="number", format="float", example=99.99),
     *                 @OA\Property(property="is_active", type="boolean", example=true),
     *                 @OA\Property(property="image", type="string", example="image.jpg"),
     *                 @OA\Property(property="barcode", type="string", example="1213x1412"),
     *                 @OA\Property(property="description", type="string", example="Updated Product Description")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation Error"),
     *             @OA\Property(property="data", type="object", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Product not found"),
     *             @OA\Property(property="data", type="object", example=null)
     *         )
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
                'data' => null,
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'category_id' => 'required|integer',
            'stock' => 'required|integer',
            'price' => 'required|numeric',
            'is_active' => 'boolean',
            'image' => 'string',
            'bardoce' => 'string',
            'description' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'data' => $validator->errors(),
            ], 422);
        }

        $product->update([
            'name' => $request->name,
            'slug' => Product::generateUniqueSlug($request->name),
            'category_id' => $request->category_id,
            'stock' => $request->stock,
            'price' => $request->price,
            'is_active' => $request->is_active ?? true,
            'image' => $request->image ?? null,
            'bardoce' => $request->barcode ?? null,
            'description' => $request->description ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'data' => $product,
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/products/{id}",
     *     summary="Delete a product",
     *     tags={"products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Product ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Product deleted successfully"),
     *             @OA\Property(property="data", type="object", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Product not found"),
     *             @OA\Property(property="data", type="object", example=null)
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
                'data' => null,
            ], 404);
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully',
            'data' => null,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/products/barcode/{barcode}",
     *     summary="Get product details by barcode",
     *     tags={"products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="barcode",
     *         in="path",
     *         description="Product barcode",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Product Name"),
     *                 @OA\Property(property="slug", type="string", example="product-name"),
     *                 @OA\Property(property="category_id", type="integer", example=1),
     *                 @OA\Property(property="quantity", type="integer", example=100),
     *                 @OA\Property(property="price", type="number", format="float", example=99.99),
     *                 @OA\Property(property="is_active", type="boolean", example=true),
     *                 @OA\Property(property="image", type="string", example="image.jpg"),
     *                 @OA\Property(property="barcode", type="string", example="1213x1412"),
     *                  @OA\Property(property="image_url", type="string", example="http://example.com/storage/image.jpg"),
     *                 @OA\Property(property="description", type="string", example="Product Description")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Product not found"),
     *             @OA\Property(property="data", type="object", example=null)
     *         )
     *     )
     * )
     */
    public function showByBarcode($barcode)
    {
        $product = Product::where('barcode', $barcode)->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $product,
        ]);
    }

}
