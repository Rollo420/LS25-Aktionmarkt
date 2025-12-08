# TODO: Sprachauswahl im Profil hinzufügen

## Schritte zur Implementierung

1. **Niederländische Sprachdateien erstellen**
   - `resources/lang/nl/messages.php` mit niederländischen Übersetzungen für alle Schlüssel aus `en/messages.php` und `de/messages.php`
   - `resources/lang/nl/validation.php` mit niederländischen Validierungsnachrichten

2. **Konfiguration aktualisieren**
   - `config/app.php` aktualisieren, um 'nl' als unterstützte Sprache hinzuzufügen

3. **Datenbank-Migration für Benutzer-Locale**
   - Migration erstellen, um 'locale'-Spalte zur users-Tabelle hinzuzufügen (falls nicht vorhanden)

4. **Middleware für Locale-Setzung**
   - Neue Middleware erstellen, um die Sprache basierend auf Benutzerpräferenz zu setzen

5. **ProfileController aktualisieren**
   - `app/Http/Controllers/ProfileController.php` aktualisieren, um Sprachauswahl zu handhaben

6. **Profil-Bearbeitungsformular aktualisieren**
   - `resources/views/profile/partials/update-profile-information-form.blade.php` aktualisieren, um Sprachauswahl-Dropdown hinzuzufügen

7. **Routes aktualisieren**
   - Middleware zu relevanten Routes hinzufügen

8. **Testen**
   - Sprachumschaltung in der UI testen
   - Alle Übersetzungen auf Vollständigkeit und Korrektheit überprüfen
