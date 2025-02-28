<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account\Account;
use Illuminate\View\View;

class accountController extends Controller
{
    public function index()
    {
        $accounts = account::with('transactions')->get();
        //$username = account::find(1)->details;
        //$passwordHash = account::find(1)->password;
        return view('account.index', ['accounts' => $accounts]);
    }
}
