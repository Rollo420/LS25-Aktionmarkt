<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SessionController extends Controller
{
    public function setTimeLineMonth(Request $request)
    {
        // Hole die ausgewählten Monate aus der Anfrage
        $selectedMonths = $request->input('months');

        // Speichere die Monate in der Session
        session(['timelineSelectedMonth' => $selectedMonths]);

        // Optional: Weiterleitung zurück zur vorherigen Seite
        return redirect()->back()->with('success', 'Monate erfolgreich aktualisiert: ' . $selectedMonths);
   
    }

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