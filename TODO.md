# TODO: Fix Foreign Key Constraint Violation in AdminAccountSeeder

## Problem Analysis
- **Error**: SQLSTATE[23000]: Integrity constraint violation: 1452 Cannot add or update a child row: a foreign key constraint fails (`LS25_Aktienmarkt`.`transactions`, CONSTRAINT `transactions_stock_id_foreign` FOREIGN KEY (`stock_id`) REFERENCES `stocks` (`id`) ON DELETE CASCADE)
- **Location**: database/seeders/AdminAccountSeeder.php, Zeile 57 (beim Erstellen von Transaktionen für User 'woodly')
- **Ursache**: Der Seeder versucht, Transaktionen mit `stock_id` 4 zu erstellen, aber diese Aktie existiert noch nicht in der `stocks`-Tabelle.
- **Warum passiert das?**: 
  - In `DatabaseSeeder.php` wird `StockSeeder` vor `AdminAccountSeeder` aufgerufen, was korrekt ist.
  - In `StockSeeder` wird `BT21StockSeeder` aufgerufen, das 2 Aktien erstellt (IDs 1 und 2).
  - Dann sollte `Stock::factory()->createMany()` 3 weitere Aktien erstellen (IDs 3, 4, 5).
  - **Problem**: `createMany()` ohne Argumente ist ungültig und erstellt keine Aktien. Daher fehlen die Aktien mit IDs 3, 4, 5.
- **Weitere potenzielle Probleme**:
  - `game_time_id` in Transaktionen könnte nicht existieren, wenn das aktuelle Datum nicht in den geseedeten GameTimes liegt (2000-2010).
  - Aber der primäre Fehler ist der fehlende `stock_id`.

## Geplante Lösungen
- [ ] `StockSeeder.php` korrigieren: `Stock::factory()->createMany()` zu `Stock::factory(3)->create()` ändern, um 3 zusätzliche Aktien zu erstellen.
- [ ] Nach der Korrektur den Seeder testen, um sicherzustellen, dass alle Aktien (IDs 1-5) erstellt werden.
- [ ] Falls weitere FK-Fehler auftreten (z.B. game_time_id), diese beheben.
- [ ] Dokumentation aktualisieren, falls nötig.

## Abhängigkeiten
- Keine neuen Abhängigkeiten erforderlich.
- Änderungen nur in `database/seeders/StockSeeder.php`.

## Follow-up Schritte
- [ ] Seeder ausführen: `php artisan db:seed --class=StockSeeder` und dann `php artisan db:seed --class=AdminAccountSeeder`.
- [ ] Datenbank prüfen: Sicherstellen, dass Aktien 1-5 existieren.
- [ ] Bei Erfolg: Vollständigen Seed (`php artisan db:seed`) testen.
