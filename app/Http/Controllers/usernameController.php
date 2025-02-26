<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\username;
use Illuminate\View\View;

class usernameController extends Controller
{
    public function index(): View
    {
        $usernames = username::all();
        return view('username.index', ['usernames' => $usernames]);
    }
}
