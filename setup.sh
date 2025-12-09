#!/bin/bash
set -euo pipefail

# Defaults and environment
# Use current user/group ids as defaults so Dockerfiles that expect WWWUSER/WWWGROUP
# don't fail when the variables are missing.
export WWWUSER="${WWWUSER:-$(id -u)}"
export WWWGROUP="${WWWGROUP:-$(id -g)}"

# Detect available docker-compose command
if docker compose version >/dev/null 2>&1; then
    COMPOSE_CMD="docker compose"
elif command -v docker-compose >/dev/null 2>&1; then
    COMPOSE_CMD="docker-compose"
else
    echo "⚠️  Kein 'docker compose' oder 'docker-compose' gefunden. Bitte installiere Docker Compose." >&2
    exit 1
fi

# Prefer local Sail binary if present, otherwise fall back to system 'sail' or use docker compose as last resort
if [ -x ./vendor/bin/sail ]; then
    sail="./vendor/bin/sail"
elif command -v sail >/dev/null 2>&1; then
    sail="sail"
else
    # Fall back to using docker compose commands directly for basic operations
    sail=""
fi

echo "🚀 Starte Setup für LS25-Aktionmarkt..."
echo "ℹ Using COMPOSE_CMD=${COMPOSE_CMD}, WWWUSER=${WWWUSER}, WWWGROUP=${WWWGROUP}"

# 1️⃣ Builden und vorbereiten des Setup-Containers
echo "🔨 Baue Setup-Container..."
$COMPOSE_CMD -f docker-compose-setup.yml up --build --remove-orphans

# 2️⃣ Sicherstellen, dass das DB-Init-Script ausführbar ist
if [ -f ./docker/mysql/create-testing-database.sh ]; then
    echo "🔧 Setze Rechte für MySQL Init-Script..."
    chmod +x ./docker/mysql/create-testing-database.sh
else
    echo "⚠ Warnung: create-testing-database.sh nicht gefunden!"
fi

# 3️⃣ Entschlüsseln der .env-Datei falls nicht vorhanden
if [ ! -f .env ]; then
    echo "🔑 Entschlüssele .env-Datei..."
    $COMPOSE_CMD run --rm --no-deps --user root --entrypoint "" laravel.test php artisan env:decrypt --key="base64:4swmEQ6ibCyYTKqPGN0MkWy0odH1J6W5971Q0whbTu8=
"
else
    echo "ℹ .env-Datei existiert bereits, überspringe Entschlüsselung."
fi

# 4️⃣ Laravel-Container starten
echo "🚀 Starte Laravel-Container..."
if [ -n "${sail}" ]; then
    $sail up -d --remove-orphans
else
    $COMPOSE_CMD up -d --remove-orphans
fi

# 5️⃣ Rechte im Laravel-Container korrigieren
echo "🔧 Setze Rechte für Laravel..."
if [ -n "${sail}" ]; then
    $sail exec laravel.test chown -R $(id -u):$(id -g) /var/www/html
    $sail exec laravel.test chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
    $sail exec laravel.test chmod 664 /var/www/html/.env
else
    $COMPOSE_CMD exec laravel.test chown -R $(id -u):$(id -g) /var/www/html
    $COMPOSE_CMD exec laravel.test chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
    $COMPOSE_CMD exec laravel.test chmod 664 /var/www/html/.env
fi

# 6️⃣ Laravel Artisan Befehle ausführen
echo "🛠 Konfiguration und DB vorbereiten..."
if [ -n "${sail}" ]; then
    $sail artisan config:clear
    $sail artisan key:generate
    $sail artisan vendor:publish --provider="LaravelScoutScoutServiceProvider"
    $sail artisan migrate:fresh --seed
    $sail artisan scout:import "App\Models\User"
    $sail artisan scout:import "App\Models\Stock/Stock"
else
    $COMPOSE_CMD exec laravel.test php artisan config:clear
    $COMPOSE_CMD exec laravel.test php artisan key:generate
    $COMPOSE_CMD exec laravle.test php artisan vendor:publish --provider="LaravelScoutScoutServiceProvider"
    $COMPOSE_CMD exec laravel.test php artisan migrate:fresh --seed

    $COMPOSE_CMD exec laravel.test php scout:import "App\Models\User"
    $COMPOSE_CMD exec laravel.test php scout:import "App\Models\Stock/Stock"
fi

# 7️⃣ Node/Vite vorbereiten und Assets bauen
echo "📦 Bereite Node/Vite vor..."
if [ -n "${sail}" ]; then
    $sail exec -T laravel.test bash -c "mkdir -p /app/node_modules"
    $sail npm run build
else
    $COMPOSE_CMD exec -T laravel.test bash -c "mkdir -p /app/node_modules"
    $COMPOSE_CMD exec -T laravel.test bash -c "npm run build"
fi

# --- Permanenter Sail-Alias nur auf Linux ---
if [[ "$OSTYPE" == "linux-gnu"* ]]; then
    echo "alias sail='sh $([ -f sail ] && echo sail || echo vendor/bin/sail)'" >> ~/.bashrc
    source ~/.bashrc

fi

echo "🎉 Setup abgeschlossen! Du kannst nun 'sail' verwenden."