<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Http\Requests\StoreFarmRequest;
use App\Http\Requests\UpdateFarmRequest;

use Symfony\Component\HttpFoundation\Request;

use App\Services\FarmService;
use App\Helpers\AuthHelper;

use App\Models\Farm;
use App\Models\User;



class FarmController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $farmInvites = Auth::user()->farms()
                        ->wherePivot('invite_acception', false)
                        ->count();

        return view('farm.index', compact('farmInvites'));
    }

    public function newFarm(Request $request){
        
        

        return view('farm.newFarm');
    }


    public function sendNewFarm(Request $request){

        if(Farm::where('name', $request->input('name'))->exists())
        {
            return redirect()->back()->with('error', 'Der Hof exestiert bereits.');
        }

        $farm = new Farm();
        $farm->name = $request->input('name');
        $farm->farm_description = $request->input('description');
        $farm->email = 'farm_' . uniqid() . '@bt21.com'; // Dummy E-Mail
        $farm->password = Hash::make(Str::random(20));     // Dummy Passwort
        
        $farm->save();

        return redirect()->back()->with('success' , 'Die neue Farm wurde Erfolgreich erstellt.');
    }

    public function farmUserInvite()
    {
        return view('farm.userInvite');
    }

    public function sendUserInvite(Request $request)
    {
        $invitesUserID = $request->input('invites');
        $inviteFarm = Auth::user()->farms()->first();

        foreach($invitesUserID as $userID)
        {
            $inviteFarm->users()->syncWithoutDetaching([$userID]);
        }

        return redirect()->back()->with('success', 'User wurde erfolgreich Eingeladen.');

    }

    public function management()
    {


        return view('farm.management');
    }

    public function invitations()
    {

        $farms = Auth::user()->farms()
            ->wherePivot('invite_acception', false)
            ->get()
            ->map(function($farm){
                return [
                    'id' => $farm->id,
                    'name' => $farm->name
                ];
            });


        return view('farm.invitations', compact('farms'));
    }



    public function manageInviteAcception(Request $request)
    {
        if($request->has('acceptBTN'))
        {
            $farm = Farm::find($request->input('farmID'));

            $farm->users()->syncWithoutDetaching([Auth::user()->id => [
                'invite_acception' => true,
            ]]);

            return redirect()->back()->with('success', 'Du bist erfolgreich der Farm beigetreten.');
        }
    }

    /**
     * Zeigt die Bestätigungsseite für das Verlassen der aktuellen Farm
     */
    public function confirmLeaveFarm()
    {
        $user = Auth::user();
        
        // Prüfen ob der User bereits in einer Farm ist
        if (!$user->isInFarm()) {
            return redirect()->route('farm.index')
                ->with('error', 'Du bist nicht Mitglied einer Farm.');
        }

        // Aktuelle Farm des Users abrufen
        $currentFarm = $user->farms()
            ->wherePivot('invite_acception', true)
            ->first();

        return view('farm.leave-farm-confirmation', compact('currentFarm'));
    }

    /**
     * Führt das Verlassen der Farm durch
     */
    public function leaveFarm(Request $request)
    {
        $user = Auth::user();
        
        // CSRF-Token validieren
        if (!$request->has('_token')) {
            return redirect()->route('farm.index')
                ->with('error', 'Ungültige Anfrage.');
        }

        // Prüfen ob der User bereits in einer Farm ist
        if (!$user->isInFarm()) {
            return redirect()->route('farm.index')
                ->with('error', 'Du bist nicht Mitglied einer Farm.');
        }

        // Aktuelle Farm des Users abrufen
        $currentFarm = $user->farms()
            ->wherePivot('invite_acception', true)
            ->first();

        if ($currentFarm) {
            // User aus der Farm entfernen
            $currentFarm->users()->detach($user->id);
            
            // Session-Farm-Modus zurücksetzen
            session(['farmMode' => false]);
            
            return redirect()->route('farm.index')
                ->with('success', 'Du hast die Farm "' . $currentFarm->name . '" erfolgreich verlassen.');
        }

        return redirect()->route('farm.index')
            ->with('error', 'Farm konnte nicht gefunden werden.');
    }

}
