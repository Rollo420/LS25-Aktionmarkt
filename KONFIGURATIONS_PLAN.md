# Laravel Projekt Anpassungsplan

## Aufgabe
Anpassung der Laravel API und des Projekts an die gegebenen Konfigurationseinstellungen:

```json
{
  "Logging": {
    "LogLevel": {
      "Default": "Information",
      "Microsoft.Hosting.Lifetime": "Information"
    }
  },
  "MarketSettings": {
    "ApiUrl": "http://localhost/api/market",
    "ApiToken": "dein_secret_token",
    "ExportPath": "./output/market.xml",
    "UpdateIntervalSeconds": 60
  }
}
```

## Analyse der aktuellen Situation
- ✅ Laravel-Projekt mit Standard-Konfigurationsstruktur vorhanden
- ✅ logging.php bereits konfiguriert (Standard Laravel)
- ✅ app.php mit deutschen Lokalisierungseinstellungen
- ❌ Keine bestehende Market-Funktionalität gefunden
- ❌ Keine custom logging configuration für Information level

## Plan zur Umsetzung

### 1. Logging-Konfiguration anpassen
**Datei:** `config/logging.php`
- LogLevel auf "Information" setzen
- Microsoft.Hosting.Lifetime Log-Level konfigurieren
- Neue Kanäle für detaillierteres Logging hinzufügen

### 2. Market-Konfiguration erstellen
**Datei:** `config/market.php`
- Neue Konfigurationsdatei für Market-Settings
- ApiUrl, ApiToken, ExportPath, UpdateIntervalSeconds definieren
- Umgebungsvariablen-Integration

### 3. Market-Service implementieren
**Datei:** `app/Services/MarketService.php`
- Service-Klasse für Market-API-Integration
- Methoden für API-Aufrufe und Export-Funktionen
- Automatische Updates basierend auf UpdateIntervalSeconds

### 4. Market-Controller erstellen
**Datei:** `app/Http/Controllers/Api/MarketController.php`
- API-Endpoints für Market-Funktionen
- GET /api/market - Market-Daten abrufen
- POST /api/market/export - Daten exportieren

### 5. Market-Routes hinzufügen
**Datei:** `routes/api.php`
- Neue API-Routen für Market-Funktionalität
- Middleware für API-Token-Validierung

### 6. Scheduled Task konfigurieren
**Datei:** `app/Console/Kernel.php`
- Artisan-Schedule für automatische Market-Updates
- Cron-Job für periodische Datenabfrage

### 7. Environment-Variablen
**Datei:** `.env` (nicht editierbar, aber Dokumentation)
- MARKET_API_URL
- MARKET_API_TOKEN
- MARKET_EXPORT_PATH
- MARKET_UPDATE_INTERVAL

### 8. Export-Verzeichnis erstellen
**Pfad:** `./output/`
- Verzeichnis für exportierte Market-Daten erstellen
- Berechtigungen für Schreibzugriff setzen

## Implementierungsschritte
1. ✅ Plan erstellt
2. 🔄 Logging-Konfiguration anpassen
3. 🔄 Market-Konfigurationsdatei erstellen
4. 🔄 Market-Service implementieren
5. 🔄 Market-Controller erstellen
6. 🔄 API-Routen hinzufügen
7. 🔄 Scheduled Task konfigurieren
8. 🔄 Verzeichnisstruktur erstellen

## Abhängigkeiten
- Keine neuen Composer-Pakete erforderlich
- Nutzt Laravel's built-in HTTP Client
- Verwendet Laravel's Task Scheduling

## Sicherheitsaspekte
- API-Token über Umgebungsvariablen
- Input-Validierung für alle API-Endpunkte
- Rate Limiting für API-Aufrufe

## Testing
- PHPUnit Tests für MarketService
- Feature Tests für API-Endpunkte
- Manuelle Tests für Export-Funktionalität
