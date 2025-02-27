<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account\account;
use Illuminate\View\View;

class accountController extends Controller
{
    public function index()
    {
        $accounts = account::all();
        //$username = account::find(1)->details;
        $passwordHash = account::find(1)->GetPassword;
        return view('username.index', ['accounts' => $accounts, 'passwordHash' => $passwordHash]);
    }
}
