#!/bin/bash
set -euo pipefail

export WWWUSER="${WWWUSER:-$(id -u)}"
export WWWGROUP="${WWWGROUP:-$(id -g)}"

echo "🚀 Starte Fix-Setup für LS25-Aktionmarkt..."

# 1. Host-Rechte & Cleanup
sudo chown -R $USER:$USER .
[ -d "public/build" ] && sudo rm -rf public/build
chmod -R 775 storage bootstrap/cache public

SAIL="./vendor/bin/sail"
[ ! -x "$SAIL" ] && SAIL="bash vendor/bin/sail"

# 2. Starten
$SAIL up -d --remove-orphans

# --- NEU: Warten auf die Datenbank ---
echo "⏳ Warte auf die Datenbank (MariaDB)..."
RETRIES=30
until $SAIL exec mariadb mariadb-admin ping -h localhost --silent || [ $RETRIES -eq 0 ]; do
    echo "   ... Datenbank ist noch nicht bereit ($RETRIES Versuche übrig)"
    sleep 2
    RETRIES=$((RETRIES-1))
done

if [ $RETRIES -eq 0 ]; then
    echo "❌ Fehler: Datenbank konnte nicht rechtzeitig gestartet werden."
    exit 1
fi
echo "✅ Datenbank ist bereit!"

# 3. Rechte im Container
echo "🔧 Harmonisiere Rechte..."
$SAIL exec laravel.test chown -R sail:sail /var/www/html
$SAIL exec laravel.test chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 4. Laravel Setup
echo "🛠 Führe Laravel-Setup aus..."
$SAIL artisan config:clear
$SAIL artisan key:generate

# Migration mit Retry, falls die Verbindung noch zickt
echo "🗄 Starte Migrationen..."
$SAIL artisan migrate:fresh --seed || (sleep 5 && $SAIL artisan migrate:fresh --seed)

# 5. Frontend
echo "📦 Baue Assets..."
$SAIL exec laravel.test mkdir -p public/build
$SAIL npm install
$SAIL npm run build

# 6. Such-Index
echo "🔍 Indiziere Scout..."
$SAIL artisan scout:import "App\Models\User"
$SAIL artisan scout:import "App\Models\Stock\Stock"

echo "🎉 Fertig! System ist online."