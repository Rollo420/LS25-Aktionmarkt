<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SessionController extends Controller
{
    /**
     * Setzt die Anzahl der Monate für die Timeline in der Session.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function setTimeLineMonth(Request $request)
    {
        // Hole die ausgewählten Monate aus der Anfrage
        $selectedMonths = $request->input('months');

        // Speichere die Monate in der Session
        session(['timelineSelectedMonth' => $selectedMonths]);

        // Optional: Weiterleitung zurück zur vorherigen Seite
        return redirect()->back()->with('success', 'Monate erfolgreich aktualisiert: ' . $selectedMonths);
   
    }

    /**
     * Gibt die gespeicherte Anzahl Monate aus der Session als JSON zurück.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSession()
    {
        // Hole die gespeicherte Zahl aus der Session
        $months = session('selected_months', null); // Standardwert ist null, falls nichts gesetzt ist

        return response()->json([
            'success' => true,
            'selected_months' => $months
        ]);
    }
}