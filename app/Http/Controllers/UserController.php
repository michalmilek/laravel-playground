<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
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
