<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB; 
class OrderController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/orders",
     *     summary="Get list of orders",
     *     tags={"orders"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="email", type="string", example="john@example.com"),
     *                     @OA\Property(property="gender", type="string", example="male"),
     *                     @OA\Property(property="birthday", type="string", format="date", example="1990-01-01"),
     *                     @OA\Property(property="phone", type="string", example="123456789"),
     *                     @OA\Property(property="total_price", type="number", format="float", example=100.00),
     *                     @OA\Property(property="notes", type="string", example="Some notes"),
     *                     @OA\Property(property="payment_method", type="string", example="Credit Card"),
     *                     @OA\Property(property="paid_amount", type="number", format="float", example=100.00),
     *                     @OA\Property(property="change_amount", type="number", format="float", example=0.00),
     *                     @OA\Property(property="items", type="array",
     *                         @OA\Items(type="object",
     *                             @OA\Property(property="product_id", type="integer", example=1),
     *                             @OA\Property(property="quantity", type="integer", example=2),
     *                             @OA\Property(property="unit_price", type="number", format="float", example=50.00)
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        $orders = Order::with('items', 'paymentMethod')->get();
        $orders->transform(function ($order) {
            $order->payment_method = $order->paymentMethod->name ?? '-';
            $order->items->transform(function ($item) {
                return [
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name ?? '-',
                    'quantity' => $item->quantity ?? 0,
                    'unit_price' => $item->unit_price ?? 0,
                ];
            });
            return $order;
        });

        return response()->json([
            'success' => true,
            'data' => $orders,
        ]);
    }

    /**
 * @OA\Post(
 *     path="/api/orders",
 *     summary="Create a new order",
 *     tags={"orders"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name", "email", "phone", "total_price", "payment_method_id", "items"},
 *             @OA\Property(property="name", type="string", example="John Doe"),
 *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *             @OA\Property(property="gender", type="string", example="male"),
 *             @OA\Property(property="birthday", type="string", format="date", example="1990-01-01"),
 *             @OA\Property(property="phone", type="string", example="123456789"),
 *             @OA\Property(property="total_price", type="number", format="float", example=100.00),
 *             @OA\Property(property="notes", type="string", example="Some notes"),
 *             @OA\Property(property="payment_method_id", type="integer", example=1),
 *             @OA\Property(
 *                 property="items",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="product_id", type="integer", example=1),
 *                     @OA\Property(property="quantity", type="integer", example=2),
 *                     @OA\Property(property="unit_price", type="number", format="float", example=50.00),
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Order created successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="John Doe"),
 *                 @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *                 @OA\Property(property="gender", type="string", example="male"),
 *                 @OA\Property(property="birthday", type="string", format="date", example="1990-01-01"),
 *                 @OA\Property(property="phone", type="string", example="123456789"),
 *                 @OA\Property(property="total_price", type="number", format="float", example=100.00),
 *                 @OA\Property(property="notes", type="string", example="Some notes"),
 *                 @OA\Property(property="payment_method_id", type="integer", example=1),
 *                 @OA\Property(property="paid_amount", type="number", format="float", example=100.00),
 *                 @OA\Property(property="change_amount", type="number", format="float", example=0.00),
 *                 @OA\Property(property="items", type="array", @OA\Items(
 *                     @OA\Property(property="product_id", type="integer", example=1),
 *                     @OA\Property(property="quantity", type="integer", example=2),
 *                     @OA\Property(property="unit_price", type="number", format="float", example=50.00),
 *                 ))
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="The given data was invalid."),
 *             @OA\Property(property="errors", type="object",
 *                 @OA\Property(property="field_name", type="array",
 *                     @OA\Items(type="string", example="Error message")
 *                 )
 *             )
 *         )
 *     )
 * )
 */


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'nullable|string',
            'gender' => 'nullable|string',
            'birthday' => 'nullable|date',
            'phone' => 'nullable|string',
            'total_price' => 'required|numeric',
            'notes' => 'nullable|string',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        foreach ($request->items as $item) {
            $product = Product::find($item['product_id']);
            if (!$product || $product->stock < $item['quantity']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient stock for product ID: ' . $item['product_id'],
                ], 422);
            }
        }

        $order = Order::create($request->only([
            'name', 'email', 'gender', 'birthday', 'phone', 'total_price', 'notes', 'payment_method_id', 'paid_amount', 'change_amount'
        ]));

        foreach ($request->items as $item) {
            $order->items()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
            ]);

            $product->decrement('stock', $item['quantity']);
        }

        return response()->json([
            'success' => true,
            'data' => $order,
            'message' => 'Order created successfully',
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/orders/{id}",
     *     summary="Get details of a specific order",
     *     tags={"orders"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the order",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", example="john@example.com"),
     *                 @OA\Property(property="gender", type="string", example="male"),
     *                 @OA\Property(property="birthday", type="string", format="date", example="1990-01-01"),
     *                 @OA\Property(property="phone", type="string", example="123456789"),
     *                 @OA\Property(property="total_price", type="number", format="float", example=100.00),
     *                 @OA\Property(property="notes", type="string", example="Some notes"),
     *                 @OA\Property(property="payment_method", type="string", example="Credit Card"),
     *                 @OA\Property(property="paid_amount", type="number", format="float", example=100.00),
     *                 @OA\Property(property="change_amount", type="number", format="float", example=0.00),
     *                 @OA\Property(property="items", type="array",
     *                     @OA\Items(type="object",
     *                         @OA\Property(property="product_id", type="integer", example=1),
     *                         @OA\Property(property="quantity", type="integer", example=2),
     *                         @OA\Property(property="unit_price", type="number", format="float", example=50.00)
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Order not found")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $order = Order::with('items', 'paymentMethod')->find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        $order->payment_method = $order->paymentMethod->name;
        $order->items->transform(function ($item) {
            return [
                'product_id' => $item->product_id,
                'product_name' => $item->product->name,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/orders/{id}",
     *     summary="Update details of a specific order",
     *     tags={"orders"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the order",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="gender", type="string", example="male"),
     *             @OA\Property(property="birthday", type="string", format="date", example="1990-01-01"),
     *             @OA\Property(property="phone", type="string", example="123456789"),
     *             @OA\Property(property="total_price", type="number", format="float", example=100.00),
     *             @OA\Property(property="notes", type="string", example="Some notes"),
     *             @OA\Property(property="payment_method_id", type="integer", example=1),
     *             @OA\Property(property="paid_amount", type="number", format="float", example=100.00),
     *             @OA\Property(property="change_amount", type="number", format="float", example=0.00),
     *             @OA\Property(property="items", type="array",
     *                 @OA\Items(type="object",
     *                     @OA\Property(property="product_id", type="integer", example=1),
     *                     @OA\Property(property="quantity", type="integer", example=2),
     *                     @OA\Property(property="unit_price", type="number", format="float", example=50.00)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", example="john@example.com"),
     *                 @OA\Property(property="gender", type="string", example="male"),
     *                 @OA\Property(property="birthday", type="string", format="date", example="1990-01-01"),
     *                 @OA\Property(property="phone", type="string", example="123456789"),
     *                 @OA\Property(property="total_price", type="number", format="float", example=100.00),
     *                 @OA\Property(property="notes", type="string", example="Some notes"),
     *                 @OA\Property(property="payment_method", type="string", example="Credit Card"),
     *                 @OA\Property(property="paid_amount", type="number", format="float", example=100.00),
     *                 @OA\Property(property="change_amount", type="number", format="float", example=0.00),
     *                 @OA\Property(property="items", type="array",
     *                     @OA\Items(type="object",
     *                         @OA\Property(property="product_id", type="integer", example=1),
     *                         @OA\Property(property="quantity", type="integer", example=2),
     *                         @OA\Property(property="unit_price", type="number", format="float", example=50.00)
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Order not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="field_name", type="array",
     *                     @OA\Items(type="string", example="Error message")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'string',
            'email' => 'nullable',
            'gender' => 'nullable|string',
            'birthday' => 'nullable|date',
            'phone' => 'nullable|string',
            'total_price' => 'numeric',
            'notes' => 'nullable|string',
            'payment_method_id' => 'exists:payment_methods,id',
            'items' => 'array',
            'items.*.product_id' => 'exists:products,id',
            'items.*.quantity' => 'integer|min:1',
            'items.*.unit_price' => 'numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $order->update($request->only([
            'name', 'email', 'gender', 'birthday', 'phone', 'total_price', 'notes', 'payment_method_id', 'paid_amount', 'change_amount'
        ]));

        if ($request->has('items')) {
            foreach ($order->items as $item) {
                $item->delete();
            }

            foreach ($request->items as $item) {
                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                ]);
            }
        }

        $order->load('items', 'paymentMethod');
        $order->payment_method = $order->paymentMethod->name;
        $order->items->transform(function ($item) {
            return [
                'product_id' => $item->product_id,
                'product_name' => $item->product->name,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }

    /**
 * @OA\Delete(
 *     path="/api/orders/{id}",
 *     summary="Delete an order",
 *     tags={"orders"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the order",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Order deleted successfully.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Order not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Order not found.")
 *         )
 *     )
 * )
 */
public function destroy($id)
{
    DB::transaction(function () use ($id) {
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        // Mengembalikan stok produk
        foreach ($order->items as $item) {
            $product = $item->product;
            $product->stock += $item->quantity;
            $product->save();
        }

        $order->items()->delete();
        $order->delete();
    });

    return response()->json([
        'success' => true,
        'message' => 'Order deleted successfully.'
    ]);
}

}
