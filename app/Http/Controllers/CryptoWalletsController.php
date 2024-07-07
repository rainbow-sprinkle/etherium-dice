<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\log;
use App\Models\User;

class CryptoWalletsController extends Controller
{
    public function update(Request $request)
    {
        $user = Auth::user();
    
        // Check if eth_address or eth_private_key have already been set
        if (!$user->eth_address && !$user->eth_private_key) {
            $user->eth_address = $request->input('eth_address');
            $user->eth_private_key = $request->input('eth_private_key');
            $user->save();
    
            // Add debug logging
            Log::debug('Eth address and private key saved successfully', [
                'user_id' => $user->id,
                'eth_address' => $user->eth_address,
                'eth_private_key' => $user->eth_private_key,
            ]);
    
            return response()->json(['message' => 'Eth address and private key saved successfully'], 200);
        } else {
            return response()->json(['message' => 'Eth address and private key already set'], 400);
        }
    }
    
}    