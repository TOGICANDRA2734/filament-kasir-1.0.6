<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/setting",
     *     summary="Get setting",
     *     tags={"setting"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Get the setting",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="shop", type="string"),
     *                 @OA\Property(property="address", type="string"),
     *                 @OA\Property(property="phone", type="string")
     *             ),
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Setting not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="object", nullable=true)
     *         )
     *     )
     * )
     */
    public function index()
    {
        $setting = Setting::first();
        if ($setting) {
            return response()->json([
                'success' => true,
                'data' => $setting,
                'message' => 'Sukses menemukan data'
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => 'Setting not found',
            'data' => null
        ], 404);
    }
}
