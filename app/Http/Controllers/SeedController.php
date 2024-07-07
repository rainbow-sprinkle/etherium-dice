<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SeedController extends Controller
{
    public function generateSeed(Request $request)
    {
        // Generate two random seeds
        $serverSeed = Hash::make(Str::random(32));
        $clientSeed = $request->input('client_seed') ?? Str::random(32);

        // Return the seeds as JSON
        return response()->json([
            'server_seed' => $serverSeed,
            'client_seed' => $clientSeed
        ]);
    }
}
