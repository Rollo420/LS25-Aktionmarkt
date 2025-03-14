<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account\Account;
use Illuminate\View\View;
use App\Models\User;

class AdminController extends Controller
{
    public function index()
    {
        $accounts = account::with('transactions')->get();
        //$username = account::find(1)->details;
        //$passwordHash = account::find(1)->password;
        
        $user = User::find(2);
        //if ($user->isAdministrator()) {
        //    // User is an administrator
        //    dd('User is an administrator');
        //} else {
        //    // User is not an administrator
        //    dd('User is not an administrator');
        //}
        
        return view('admin', ['accounts' => $accounts]);
    }
}
