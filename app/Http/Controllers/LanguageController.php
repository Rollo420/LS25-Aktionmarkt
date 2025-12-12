<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    /**
     * Switch the application language
     */
    public function switch(Request $request, $lang)
    {
        // Validate the language
        $allowedLanguages = ['de', 'en', 'nl'];
        
        if (!in_array($lang, $allowedLanguages)) {
            return redirect()->back()->with('error', 'Unsupported language.');
        }

        // Store the language in session
        Session::put('locale', $lang);
        
        // Also update the user's locale if authenticated
        if (auth()->check()) {
            auth()->user()->update(['locale' => $lang]);
        }

        // Redirect back to the previous page
        return redirect()->back()->with('success', 'Language changed successfully.');
    }
}
