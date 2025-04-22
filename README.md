# LS25-Aktionmarkt

## Inhaltsverzeichnis
1. [Projektbeschreibung](#projektbeschreibung)
2. [Alias sail & Voraussetzungen](#alias-sail--voraussetzungen)
3. [Features](#features)
4. [Architektur-Überblick](#architektur-überblick)
5. [Projektstruktur](#projektstruktur)
6. [Setup & Installation](#setup--installation)
7. [Starten der Anwendung](#starten-der-anwendung)
8. [Datenbankmodell](#datenbankmodell)
9. [Routen- & API-Übersicht](#routen--api-übersicht)
10. [Funktionsübersicht](#funktionsübersicht)
11. [Beispiel-Workflows](#beispiel-workflows)
12. [Code-Kommentare & Dokumentationsstil](#code-kommentare--dokumentationsstil)
13. [Entwicklerhinweise](#entwicklerhinweise)
14. [Testing](#testing)
15. [Troubleshooting / FAQ](#troubleshooting--faq)
16. [Contributing Guide](#contributing-guide)
17. [Changelog](#changelog)
18. [Lizenz](#lizenz)
19. [Credits / Danksagungen](#credits--danksagungen)

---

## Projektbeschreibung
LS25-Aktionmarkt ist eine vollwertige Laravel-Webanwendung für den Handel und die Verwaltung von Aktien, Bankkonten und Benutzerprofilen. Zielgruppe sind Entwickler, die eine moderne, containerisierte Finanzanwendung mit Fokus auf Clean Code, Dokumentation und Erweiterbarkeit suchen.

---

## Alias sail & Voraussetzungen

Laravel Sail ist ein leichtgewichtiges CLI-Interface für Docker, das die lokale Entwicklung vereinfacht. Um Sail komfortabel zu nutzen, empfiehlt es sich, einen Alias zu setzen:

```sh
alias sail="./vendor/bin/sail"
```

**Wichtig:**
- Diesen Befehl in dein Terminal eingeben, bevor du Sail-Befehle nutzt.
- Optional kannst du den Alias in deine `~/.bashrc` oder `~/.zshrc` eintragen, damit er dauerhaft verfügbar ist.

**Ab dann kannst du alle Sail-Kommandos wie folgt ausführen:**

```sh
sail up -d                # Container starten
sail artisan migrate      # Migrationen ausführen
sail npm run dev          # Assets im Dev-Modus bauen
sail artisan test         # Tests ausführen
sail composer install     # Composer-Abhängigkeiten installieren
```

**Beispiel für ein komplettes Setup:**
```sh
alias sail="./vendor/bin/sail"
sail up -d
sail composer install
sail npm install
sail artisan key:generate
sail artisan migrate --seed
sail npm run build
sail npm run dev
```

**Hinweis:** In allen weiteren Codebeispielen und Anleitungen wird der Alias `sail` verwendet.

**Voraussetzungen:**
- Docker & Docker Compose installiert
- Linux/macOS/Windows mit WSL2

---

## Features
- Benutzerregistrierung und -authentifizierung
- Automatische Bankkonto-Erstellung bei Registrierung
- Verwaltung von Aktien, Preisen, Transaktionen
- Zeitsteuerung (Monatswechsel, Simulation)
- Session-Management für Benutzereinstellungen (z.B. Chart-Zeitraum)
- Admin-Funktionen
- Responsive UI mit SCSS und Tailwind
- Docker-basierte Entwicklung mit Laravel Sail
- Umfangreiche Tests (Feature & Unit)
- Ausführliche Code-Kommentare und Dokumentation

---

## Architektur-Überblick
- **Controller**: Steuern die Business-Logik (z.B. ChartController, StockController, SessionController, TimeController, AccountController, AdminController, ProfileController, LoginController)
- **Models**: Abbildung der Datenbanktabellen (User, Bank, Credit, Role, Account, Stock, Price, Transaction, Product_type)
- **Views**: Blade-Templates für die Benutzeroberfläche (z.B. Aktienübersicht, Chart-Ansicht, Account-Formulare, Authentifizierung)
- **Components**: Wiederverwendbare UI-Bausteine (z.B. Chart-Komponenten, Details, Buttons, Timeline)
- **Middleware**: Zugriffskontrolle, Authentifizierung, Session-Handling
- **Requests**: Validierung und Authorisierung von Formulardaten
- **Datenbank**: Migrationen, Seeder, Factories für Testdaten
- **Assets**: SCSS, JS, Tailwind, Vite für modernes Frontend-Building
- **Docker**: Containerisierung für lokale Entwicklung und Deployment

---

## Projektstruktur
```
. (Projektroot)
├── app/                # Hauptlogik (Controller, Models, Middleware, View Components)
│   ├── Http/Controllers/   # Controller für alle Anwendungsbereiche
│   ├── Models/             # Eloquent-Modelle (inkl. Unterordner für Domains)
│   ├── View/Components/    # Blade-Komponenten
│   └── ...
├── bootstrap/          # Bootstrap-Dateien für Laravel
├── config/             # Konfigurationsdateien (z.B. Datenbank, Mail, Auth)
├── database/           # Migrationen, Seeder, Factories
│   ├── migrations/         # Tabellenstruktur
│   ├── seeders/            # Seed-Daten
│   └── factories/          # Testdaten-Generatoren
├── docker/             # Dockerfiles und Hilfsskripte
├── public/             # Öffentliche Assets (index.php, CSS, JS, Favicon)
├── resources/          # Views, SCSS, JS, Sprachdateien
│   ├── views/              # Blade-Templates
│   ├── sass/               # SCSS-Styles
│   ├── js/                 # JavaScript
│   └── lang/               # Übersetzungen
├── routes/              # Routen (web.php, api.php, auth.php, ...)
├── storage/             # Logs, Cache, User-Dateien
├── tests/               # Feature- und Unit-Tests
├── vendor/              # Composer-Abhängigkeiten
├── setup.sh             # Automatisiertes Setup-Skript
├── start.sh             # Start-Skript
├── docker-compose.yml   # Docker-Setup
├── vite.config.js       # Vite-Konfiguration
├── tailwind.config.js   # Tailwind-Konfiguration
├── ...
```
Jeder Ordner und jede Datei ist im Quellcode und in dieser README dokumentiert.

---

## Setup & Installation

### Automatisiertes Setup mit setup.sh
Das Skript `setup.sh` automatisiert die wichtigsten Setup-Schritte:

```sh
./setup.sh
```

Das Skript führt aus:
- Startet die Docker-Container (Laravel Sail)
- Setzt den Application Key
- Führt Migrationen und Seeder aus
- Baut und startet die Assets

### Manuelles Setup (alternativ)
1. Repository klonen:
   ```sh
   git clone <repo-url>
   cd LS25-Aktionmarkt
   ```
2. Docker-Container bauen und starten:
   ```sh
   sail up -d
   ```
3. Abhängigkeiten installieren:
   ```sh
   sail composer install
   sail npm install
   ```
4. Assets bauen:
   ```sh
   sail npm run build
   sail npm run dev
   ```
5. Application Key generieren:
   ```sh
   sail artisan key:generate
   ```
6. Migrationen & Seeder ausführen:
   ```sh
   sail artisan migrate --seed
   ```

---

## Starten der Anwendung
```sh
./start.sh
```
Oder manuell:
```sh
sail up -d
sail npm run dev
```

Die Anwendung ist dann unter http://localhost erreichbar.

---

## .env Konfiguration

Die Datei `.env` enthält alle wichtigen Umgebungsvariablen für die lokale Entwicklung und Produktion. Sie wird beim ersten Setup automatisch aus der Datei `.env.example` kopiert. Du kannst sie nach deinen Bedürfnissen anpassen.

**Wichtige Einstellungen:**
- `APP_NAME` – Name der Anwendung
- `APP_ENV` – Umgebung (local, production, etc.)
- `APP_KEY` – Anwendungsschlüssel (wird mit `sail artisan key:generate` gesetzt)
- `APP_DEBUG` – Debug-Modus (true/false)
- `APP_URL` – Basis-URL der Anwendung
- `DB_CONNECTION` – Datenbank-Treiber (meist `mysql`)
- `DB_HOST` – Datenbank-Host (bei Sail meist `mysql`)
- `DB_PORT` – Datenbank-Port (Standard: 3306)
- `DB_DATABASE` – Name der Datenbank
- `DB_USERNAME` – Datenbank-Benutzer
- `DB_PASSWORD` – Datenbank-Passwort
- `MAIL_MAILER`, `MAIL_HOST`, ... – Mail-Konfiguration
- `QUEUE_CONNECTION` – Warteschlangen-Treiber
- u.v.m.

**Beispiel für eine lokale .env (Ausschnitt):**
```
APP_NAME=LS25-Aktionmarkt
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=ls25
DB_USERNAME=sail
DB_PASSWORD=password
```

**Wichtig:**
- Passe die Datenbank-Zugangsdaten ggf. an deine Umgebung an.
- Nach Änderungen an der `.env` immer den Cache leeren:
  ```sh
  sail artisan config:clear
  ```
- Die `.env` darf niemals ins Git-Repository eingecheckt werden (steht in `.gitignore`).

---

## Datenbankmodell
- **users**: Benutzerkonten
- **accounts**: Bankkonten der Nutzer
- **stocks**: Aktien
- **prices**: Historische Preise zu Aktien
- **transactions**: Kauf-/Verkaufsaktionen

---

## Routen- & API-Übersicht
| Route                        | Methode | Name/Controller-Methode                        | Beschreibung                                 |
|------------------------------|---------|-----------------------------------------------|----------------------------------------------|
| /                            | GET     | dashboard                                     | Dashboard/Startseite                         |
| /admin                       | GET     | admin › AdminController@index                 | Admin-Bereich                                |
| /chart                       | GET     | chart.show › ChartController@show             | Chart-Ansicht                                |
| /confirm-password            | GET     | password.confirm › Auth\ConfirmablePasswordController@show | Passwort bestätigen-Formular      |
| /confirm-password            | POST    | Auth\ConfirmablePasswordController@store      | Passwort bestätigen (Absenden)               |
| /dashboard                   | GET     | dashboard                                     | Dashboard                                    |
| /email/verification-notification | POST | verification.send › Auth\EmailVerificationNotificationController@store | E-Mail-Bestätigung erneut senden |
| /forgot-password             | GET     | password.request › Auth\PasswordResetLinkController@create | Passwort vergessen-Formular         |
| /forgot-password             | POST    | password.email › Auth\PasswordResetLinkController@store | Passwort-Reset-Link anfordern        |
| /login                       | GET     | login › Auth\AuthenticatedSessionController@create | Login-Formular                        |
| /login                       | POST    | Auth\AuthenticatedSessionController@store     | Login absenden                                |
| /logout                      | POST    | logout › Auth\AuthenticatedSessionController@destroy | Logout                                 |
| /password                    | PUT     | password.update › Auth\PasswordController@update | Passwort ändern                         |
| /profile                     | GET     | profile.edit › ProfileController@edit          | Profil anzeigen/bearbeiten                   |
| /profile                     | PATCH   | profile.update › ProfileController@update      | Profil aktualisieren                         |
| /profile                     | DELETE  | profile.destroy › ProfileController@destroy    | Profil löschen                               |
| /register                    | GET     | register › Auth\RegisteredUserController@create | Registrierungsformular                   |
| /register                    | POST    | Auth\RegisteredUserController@store           | Registrierung absenden                       |
| /reset-password              | POST    | password.store › Auth\NewPasswordController@store | Neues Passwort speichern                |
| /reset-password/{token}      | GET     | password.reset › Auth\NewPasswordController@create | Passwort zurücksetzen-Formular         |
| /stock                       | GET     | stock.index › StockController@index            | Aktienübersicht                              |
| /stock/{id}                  | GET     | stock.store › ChartController@OneChart         | Einzelaktie mit Chart                        |
| /storage/{path}              | GET     | storage.local                                  | Zugriff auf Storage-Dateien                  |
| /time                        | GET     | time.index › TimeController@index              | Zeitsteuerung/Monatsauswahl                  |
| /time                        | POST    | time.update › TimeController@update            | Monat wechseln                               |
| /update-month                | POST    | update.monthTimeline › SessionController@setTimeLineMonth | Chart-Zeitraum setzen             |
| /up                          | GET     |                                               | Health-Check                                 |
| /verify-email                | GET     | verification.notice › Auth\EmailVerificationPromptController | E-Mail-Bestätigung anzeigen         |
| /verify-email/{id}/{hash}    | GET     | verification.verify › Auth\VerifyEmailController | E-Mail-Bestätigung durchführen           |

---

## Funktionsübersicht
- **Session-Handling:** Einstellungen wie Chart-Zeitraum werden in der Session gespeichert (SessionController)
- **Bankkonto-Erstellung:** Beim Anlegen eines neuen Users wird automatisch ein Bankkonto erstellt
- **Zeitsteuerung:** Monatswechsel und Simulation über TimeController
- **SCSS-Architektur:** Styles modular in resources/sass/app.scss
- **Docker:** Entwicklung erfolgt containerisiert mit Laravel Sail

---

## Beispiel-Workflows

### Beispiel-Workflow: Aktie anzeigen
1. Nutzer klickt auf eine Aktie in der Übersicht (StockController@index, View: Stock/index.blade.php)
2. Detailansicht mit Chart und Kennzahlen wird geladen (ChartController@OneChart, View: Stock/store.blade.php)

---

## Code-Kommentare & Dokumentationsstil
- Alle zentralen Controller und Models sind mit PHPDoc-Kommentaren versehen (Zweck, Parameter, Rückgabewerte)
- Blade-Templates enthalten Abschnittskommentare zur besseren Orientierung

---

## Troubleshooting / FAQ
- **Fehler: Port bereits belegt**: Prüfe, ob ein anderer Container läuft (`docker ps`)
- **Migration schlägt fehl**: Datenbank zurücksetzen mit `sail artisan migrate:fresh --seed`
- **Assets werden nicht gebaut**: Stelle sicher, dass Node-Module installiert sind (`sail npm install`)

---

## Lizenz
MIT

---

> Stand: April 2025 – Dokumentation automatisch generiert und gepflegt.