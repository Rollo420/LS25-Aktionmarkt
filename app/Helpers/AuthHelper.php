<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class AuthHelper 
{
    public static function user()
    {
        if (session('farmMode') == true) {
            return Auth::user()->farms()->first();
        }
        return Auth::user();
    }
}