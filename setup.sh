#!/bin/bash
set -e

# Variablen
sail="./vendor/bin/sail"

echo "🚀 Starte Setup für LS25-Aktionmarkt..."

# 1️⃣ Builden und vorbereiten des Setup-Containers
echo "🔨 Baue Setup-Container..."
docker compose -f docker-compose-setup.yml up --build --remove-orphans

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
    docker compose run --rm --no-deps --user root --entrypoint "" laravel.test php artisan env:decrypt --key="base64:tuVKEBcQpMuBo6bcttk0LaPLNjZB4NV1cy7yKFO2JR0"
else
    echo "ℹ .env-Datei existiert bereits, überspringe Entschlüsselung."
fi

# 4️⃣ Laravel-Container starten
echo "🚀 Starte Laravel-Container..."
$sail up -d --remove-orphans

# 5️⃣ Rechte im Laravel-Container korrigieren
echo "🔧 Setze Rechte für Laravel..."
$sail exec laravel.test chown -R $(id -u):$(id -g) /var/www/html
$sail exec laravel.test chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
$sail exec laravel.test mkdir -p /var/www/html/storage/framework/views
$sail exec laravel.test chmod 664 /var/www/html/.env

# 6️⃣ Laravel Artisan Befehle ausführen
echo "🛠 Konfiguration und DB vorbereiten..."
$sail artisan config:clear
$sail artisan key:generate
$sail artisan migrate:fresh --seed

# 7️⃣ Node/Vite vorbereiten und Assets bauen
echo "📦 Bereite Node/Vite vor..."
$sail exec -T laravel.test bash -c "mkdir -p /app/node_modules"
$sail exec laravel.test npm install
$sail exec laravel.test npm run build

# --- Permanenter Sail-Alias nur auf Linux ---
if [[ "$OSTYPE" == "linux-gnu"* ]]; then
    SHELL_RC="$HOME/.bashrc"
    ALIAS_CMD="alias sail='[ -f \$PWD/vendor/bin/sail ] && \$PWD/vendor/bin/sail || echo \"Sail nicht gefunden\"'"

    if ! grep -Fxq "$ALIAS_CMD" "$SHELL_RC"; then
        echo "🔧 Füge permanenten Sail-Alias zu $SHELL_RC hinzu..."
        echo "" >> "$SHELL_RC"
        echo "# Permanenter Sail-Alias für Laravel Sail" >> "$SHELL_RC"
        echo "$ALIAS_CMD" >> "$SHELL_RC"
        echo "✅ Alias hinzugefügt! Lade die Shell neu mit: source $SHELL_RC"
    else
        echo "ℹ Sail-Alias existiert bereits in $SHELL_RC, überspringe."
    fi
fi

echo "🎉 Setup abgeschlossen! Du kannst nun 'sail' verwenden."