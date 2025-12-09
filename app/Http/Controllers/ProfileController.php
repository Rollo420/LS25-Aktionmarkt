<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Zeigt das Formular zur Bearbeitung des Benutzerprofils an.
     *
     * @param \Illuminate\Http\Request $request HTTP-Request mit Benutzerinformationen
     * @return \Illuminate\View\View
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Aktualisiert die Profildaten des Benutzers.
     *
     * @param \App\Http\Requests\ProfileUpdateRequest $request Validierter Request mit neuen Profildaten
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        // Refresh the authenticated user in session to ensure updated data is used
        Auth::setUser($request->user());

        // Set the locale for the current session and persist it
        $locale = $request->user()->locale;
        app()->setLocale($locale);
        session(['locale' => $locale]);

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Löscht das Benutzerkonto nach Passwortbestätigung.
     *
     * @param \Illuminate\Http\Request $request HTTP-Request mit Passwort zur Verifizierung
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
