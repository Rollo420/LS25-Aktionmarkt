<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\User;

Route::post('/api/farm/create', function (Request $request) {

    $userId = $request->input('userId');

    $user = User::find($userId);

    if (!$user) {
        return response()->json([
            'message' => 'User nicht gefunden'
        ], 404);
    }

    return response()->json([
        'message' => 'User gefunden',
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ]
    ]);
});
