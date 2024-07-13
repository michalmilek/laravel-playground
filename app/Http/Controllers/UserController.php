<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * @OA\Put(
     *     path="/api/user/address",
     *     tags={"User"},
     *     summary="Update user's address",
     *     description="Updates the address information for the authenticated user",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="address", type="string", example="123 Main St"),
     *             @OA\Property(property="city", type="string", example="Anytown"),
     *             @OA\Property(property="state", type="string", example="CA"),
     *             @OA\Property(property="postal_code", type="string", example="12345"),
     *             @OA\Property(property="country", type="string", example="USA")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Address updated successfully",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function updateAddress(Request $request)
    {
        $request->validate([
            'address' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'postal_code' => 'required|string',
            'country' => 'required|string',
        ]);

        $user = Auth::user();
        $user->update($request->only('address', 'city', 'state', 'postal_code', 'country'));

        return response()->json(['message' => 'Address updated successfully']);
    }
}
