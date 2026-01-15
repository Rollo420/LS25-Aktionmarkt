# Aktien Sortierung Implementation

## Aufgabe
Implementierung von Sortierfunktion für die Aktientabelle mit folgenden Anforderungen:
- Klick auf "Sektor" → Sortierung A-Z, Doppelklick → Z-A
- Klick auf "Aktueller Preis" → günstig zu teuer, Doppelklick → teuer zu günstig  
- Klick auf "Dividende" → niedrig zu hoch, Doppelklick → hoch zu niedrig

## Schritte

### 1. JavaScript Sortierung implementieren
- [x] Sortierungslogik in der Blade-Datei hinzufügen
- [x] Click-Handler für Tabellenspalten implementieren
- [x] Sortier-Indikatoren (Pfeile) hinzufügen
- [x] Client-side Sortierung der vorhandenen Daten

### 2. Tabellenspalten erweitern
- [x] Sortierbare Header mit Click-Handlers erstellen
- [x] CSS für Sortier-Indikatoren hinzufügen
- [x] Daten-Attribute für Sortierung hinzufügen

### 3. Backend-Sortierung (optional)
- [x] StockController erweitern um Sortier-Parameter zu verarbeiten (nicht benötigt für Client-side)
- [x] AJAX-Endpunkt für serverseitige Sortierung (nicht benötigt für Client-side)

### 4. Testing
- [x] Sortierung testen für alle drei Spalten
- [x] Doppelklick-Verhalten testen
- [x] Responsive Design prüfen

## Technische Details
- Client-side Sortierung mit JavaScript
- Verwendung von data-Attributen für Sortierung
- Alpine.js oder Vanilla JavaScript
- CSS-Transitions für smooth UX
