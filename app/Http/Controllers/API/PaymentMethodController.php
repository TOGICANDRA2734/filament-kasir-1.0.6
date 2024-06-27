<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;

class PaymentMethodController extends Controller
{
    
   /**
     * @OA\Get(
     *     path="/api/payment-methods",
     *     summary="Get list of payment methods",
     *     tags={"payment methods"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Credit Card"),
     *                 @OA\Property(property="image", type="string", example="http://example.com/storage/payment-methods/credit-card.jpg"),
     *                 @OA\Property(property="is_cash", type="boolean", example=false),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-06-08T12:34:56Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-06-08T12:34:56Z"),
     *                 @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true, example="2024-06-08T12:34:56Z")
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        $paymentMethods = PaymentMethod::all();

        return response()->json([
            'success' => true,
            'data' => $paymentMethods,
            'message' => 'Sukses menampilkan data'
        ]);
    }
}
