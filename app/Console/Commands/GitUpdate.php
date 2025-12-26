<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Log;

class GitUpdate extends Command
{
    protected $signature = 'git:update';
    protected $description = 'Prüft auf Updates und installiert diese automatisch';

    public function handle()
    {
        $this->info('Prüfe GitHub auf neue Versionen...');
        
        // 1. Fetch (Gucken was neu ist)
        Process::run('git fetch origin');

        // 2. Prüfen ob wir hinterherhinken
        $check = Process::run('git status -uno');
        
        if (str_contains($check->output(), 'Your branch is behind')) {
            $this->warn('Update gefunden! Lade Daten herunter...');
            
            // 3. Pull (Das eigentliche Update)
            $pull = Process::run('git pull origin main');
            
            if ($pull->successful()) {
                // 4. Laravel aufräumen
                $this->info('Update geladen. Aktualisiere Datenbank...');
                $this->call('migrate', ['--force' => true]);
                $this->call('optimize:clear');
                
                Log::info('Automatisches Git-Update erfolgreich durchgeführt.');
                $this->info('System ist jetzt auf dem neuesten Stand!');
            } else {
                Log::error('Git Update Fehler: ' . $pull->errorOutput());
                $this->error('Fehler beim Update!');
            }
        } else {
            $this->info('Alles aktuell. Kein Update nötig.');
        }
    }
}