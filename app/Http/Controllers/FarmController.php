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
use App\Models\Bank;



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

        // Farm-Einladungen für Wechsel-Anzeige abrufen
        $pendingFarmInvites = Auth::user()->farms()
                        ->wherePivot('invite_acception', false)
                        ->get()
                        ->map(function($farm) {
                            return [
                                'id' => $farm->id,
                                'name' => $farm->name,
                                'description' => $farm->farm_description
                            ];
                        });

        // Aktuelle Farm abrufen (falls vorhanden)
        $currentFarm = Auth::user()->farms()
                        ->wherePivot('invite_acception', true)
                        ->first();

        return view('farm.index', compact('farmInvites', 'pendingFarmInvites', 'currentFarm'));
    }

    public function newFarm(Request $request){
        
        

        return view('farm.newFarm');
    }



    public function sendNewFarm(Request $request){

        if(Farm::where('name', $request->input('name'))->exists())
        {
            return redirect()->back()->with('error', 'Der Hof exestiert bereits.');
        }

        // Farm-Besitzer validieren
        $ownerId = $request->input('owner_id');
        if (!$ownerId) {
            return redirect()->back()->with('error', 'Ein Farm-Besitzer muss ausgewählt werden.');
        }

        $owner = User::find($ownerId);
        if (!$owner) {
            return redirect()->back()->with('error', 'Der ausgewählte Farm-Besitzer konnte nicht gefunden werden.');
        }

        $farm = new Farm;
        $farm->name = $request->input('name');
        $farm->farm_description = $request->input('description');
        $farm->email = 'farm_' . uniqid() . '@bt21.com'; // Dummy E-Mail
        $farm->password = Hash::make(Str::random(20));     // Dummy Passwort

        $farm->save();

        $farm->bank()->create([
            'user_id' => $farm->id,
            'iban' => Bank::generateIban(),
            'balance' => 0.0
        ]);

        // Besitzer als Farm-Mitglied mit Bestätigung hinzufügen
        $farm->users()->syncWithoutDetaching([$ownerId => [
            'invite_acception' => false,
            'user_role_id' => 1,
        ]]);
        

        return redirect()->route('farm.index')->with('success' , 'Die neue Farm wurde erfolgreich erstellt.');
    }

    public function farmUserInvite()
    {
        return view('farm.userInvite');
    }


    public function sendUserInvite(Request $request)
    {
        $invitesUserID = $request->input('invites');
        $inviteFarm = Auth::user()->farms()->first();

        if (!$inviteFarm) {
            return redirect()->back()->with('error', 'Keine aktive Farm gefunden.');
        }

        $successCount = 0;
        foreach($invitesUserID as $userID)
        {
            $userToInvite = User::find($userID);
            if ($userToInvite) {
                // User zur Farm hinzufügen (ohne Bestätigung)
                $inviteFarm->users()->syncWithoutDetaching([$userID => [
                    'invite_acception' => false,
                ]]);
                
                // Benachrichtigung für den eingeladenen User erstellen
                $this->createFarmInviteNotification($userToInvite, $inviteFarm, Auth::user());
                
                $successCount++;
            }
        }

        if ($successCount > 0) {
            $message = $successCount === 1 
                ? 'User wurde erfolgreich eingeladen. Er wurde über die neue Hof-Einladung benachrichtigt.' 
                : $successCount . ' User wurden erfolgreich eingeladen. Sie wurden über ihre neuen Hof-Einladungen benachrichtigt.';
            return redirect()->back()->with('success', $message);
        }

        return redirect()->back()->with('error', 'Keine gültigen User gefunden.');
    }

    /**
     * Erstellt eine Farm-Einladung-Benachrichtigung für den User
     */
    private function createFarmInviteNotification(User $user, Farm $farm, User $inviter)
    {
        $notification = [
            'type' => 'farm_invite',
            'farm_id' => $farm->id,
            'farm_name' => $farm->name,
            'farm_description' => $farm->farm_description,
            'inviter_name' => $inviter->name,
            'message' => "Sie wurden von {$inviter->name} zu dem Hof '{$farm->name}' eingeladen.",
            'created_at' => now(),
        ];

        // Benachrichtigung in der Session des Users speichern
        $userNotifications = session("notifications_{$user->id}", []);
        $userNotifications[] = $notification;
        session(["notifications_{$user->id}" => $userNotifications]);

        // Session-Benachrichtigung setzen für sofortige Anzeige beim nächsten Login
        session([
            "user_{$user->id}_has_farm_notifications" => true,
            "user_{$user->id}_farm_notification_count" => count($userNotifications)
        ]);

        // Log für Debugging
        \Log::info("Farm invite notification created for user {$user->id} for farm {$farm->name} by inviter {$inviter->name}");
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
            $newFarm = Farm::find($request->input('farmID'));
            $user = Auth::user();

            // Prüfen ob der User bereits in einer anderen Farm ist
            $currentFarm = $user->farms()
                ->wherePivot('invite_acception', true)
                ->first();


            if ($currentFarm) {
                // User ist bereits in einer Farm, zur Bestätigungsseite leiten
                return view('farm.leave-and-join-confirmation', compact('currentFarm', 'newFarm'));
            }

            // User ist in keiner Farm, direkt beitreten
            $newFarm->users()->syncWithoutDetaching([$user->id => [
                'invite_acception' => true,
            ]]);

            // Farm-Modus aktivieren
            session(['farmMode' => true]);

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
            
            // Admin-Verwaltung: Wenn Admin die Farm verlässt
            $this->handleAdminPromotionOnLeave($currentFarm);
            
            // Prüfen ob Farm gelöscht werden kann (keine Mitglieder mehr)
            $this->checkAndDeleteEmptyFarm($currentFarm);
            
            // Session-Farm-Modus zurücksetzen
            session(['farmMode' => false]);
            
            return redirect()->route('farm.index')
                ->with('success', 'Du hast die Farm "' . $currentFarm->name . '" erfolgreich verlassen.');
        }


        return redirect()->route('farm.index')
            ->with('error', 'Farm konnte nicht gefunden werden.');
    }

    /**
     * Führt das Verlassen der aktuellen Farm und den Beitritt zu einer neuen Farm durch
     */
    public function leaveAndJoin(Request $request)
    {
        $user = Auth::user();
        
        // CSRF-Token validieren
        if (!$request->has('_token') || !$request->has('new_farm_id')) {
            return redirect()->route('farm.invitations')
                ->with('error', 'Ungültige Anfrage.');
        }

        $newFarmId = $request->input('new_farm_id');
        $newFarm = Farm::find($newFarmId);

        if (!$newFarm) {
            return redirect()->route('farm.invitations')
                ->with('error', 'Die neue Farm konnte nicht gefunden werden.');
        }

        // Prüfen ob der User bereits in einer Farm ist
        if (!$user->isInFarm()) {
            return redirect()->route('farm.invitations')
                ->with('error', 'Du bist nicht Mitglied einer Farm.');
        }

        // Aktuelle Farm des Users abrufen
        $currentFarm = $user->farms()
            ->wherePivot('invite_acception', true)
            ->first();

        if ($currentFarm) {
            // 1. User aus der aktuellen Farm entfernen
            $currentFarm->users()->detach($user->id);
            
            // 2. Admin-Verwaltung: Wenn Admin die Farm verlässt
            $this->handleAdminPromotionOnLeave($currentFarm);
            
            // 3. Prüfen ob Farm gelöscht werden kann (keine Mitglieder mehr)
            $this->checkAndDeleteEmptyFarm($currentFarm);
            
            // 4. User zur neuen Farm hinzufügen
            $newFarm->users()->syncWithoutDetaching([$user->id => [
                'invite_acception' => true,
            ]]);
            
            // 5. Session-Farm-Modus für neue Farm aktivieren
            session(['farmMode' => true]);
            
            return redirect()->route('farm.index')
                ->with('success', 'Du hast die Farm "' . $currentFarm->name . '" verlassen und bist der Farm "' . $newFarm->name . '" beigetreten.');
        }

        return redirect()->route('farm.invitations')
            ->with('error', 'Farm konnte nicht gefunden werden.');
    }

    /**
     * Behandelt die Promotion eines neuen Admins wenn der aktuelle Admin die Farm verlässt
     */
    private function handleAdminPromotionOnLeave(Farm $farm)
    {
        $remainingUsers = $farm->users()->wherePivot('invite_acception', true)->get();
        
        if ($remainingUsers->isEmpty()) {
            // Keine weiteren Benutzer in der Farm
            return;
        }

        // Prüfen ob ein Administrator in der Farm verbleibt
        $adminExists = $remainingUsers->filter(function($user) {
            return $user->isAdministrator();
        })->isNotEmpty();

        if (!$adminExists) {
            // Kein Administrator vorhanden, den ersten Benutzer zum Administrator machen
            $firstUser = $remainingUsers->first();
            $adminRole = \App\Models\Role::where('name', 'admin')->first();
            
            if ($adminRole) {
                $firstUser->roles()->syncWithoutDetaching([$adminRole->id]);
                \Log::info("User {$firstUser->id} promoted to admin in farm {$farm->id} after previous admin left");
            }
        }
    }

    /**
     * Löscht eine Farm wenn keine Mitglieder mehr vorhanden sind
     */
    private function checkAndDeleteEmptyFarm(Farm $farm)
    {
        $memberCount = $farm->users()->wherePivot('invite_acception', true)->count();
        
        if ($memberCount === 0) {
            // Farm hat keine aktiven Mitglieder mehr
            $farm->delete();
            \Log::info("Farm {$farm->id} ({$farm->name}) deleted - no active members remaining");
        }
    }

}
