# Aktien-Sortierung Implementation TODO

## Ziel
Implementierung von Sortier- und Filterfunktionen für die Aktienübersicht mit klickbaren Spaltenüberschriften.

## Sortier-Spezifikationen
- **Sektor**: 1. Klick = ABC aufwärts (A-Z), 2. Klick = ABC abwärts (Z-A)
- **Aktueller Preis**: 1. Klick = günstig nach teuer, 2. Klick = teuer nach günstig
- **Dividende**: 1. Klick = günstig nach teuer, 2. Klick = teuer nach günstig

## Implementierungsschritte

### 1. Backend - StockController erweitern ✅
- [x] Sortierparameter (sort_by, sort_order) zur index() Methode hinzugefügt
- [x] Parameter-Validierung implementiert
- [x] Logik für Sortierung nach Sektor, Preis und Dividende hinzugefügt
- [x] currentSort Array an View übergeben

### 2. Frontend - Blade Template anpassen ✅
- [x] Spaltenüberschriften zu klickbaren Links mit @click="sort('field')" gemacht
- [x] Sortier-Indikatoren (Pfeil-Icons) für aufsteigend/absteigend hinzugefügt
- [x] Hover-Effekte für bessere UX implementiert
- [x] Alpine.js x-data="stockSorting()" Container hinzugefügt

### 3. JavaScript/Alpine.js für Sortierung ✅
- [x] stockSorting() Alpine.js Funktion erstellt
- [x] sort() Methode für URL-basierte Sortierung implementiert
- [x] Toggle-Logik für Sortierrichtung (1. Klick asc, 2. Klick desc)
- [x] Bestehende Suchfunktion beibehalten

### 4. Testing und Verifikation
- [ ] Funktionalität testen (verschiedene Sortier-Kombinationen)
- [ ] URL-Parameter Überprüfung
- [ ] Responsive Design testen
- [ ] Browser-Kompatibilität prüfen

### 5. Feinschliff
- [ ] CSS-Verbesserungen für Sortier-Indikatoren
- [ ] Performance-Optimierung bei großen Aktienlisten
- [ ] Accessibility-Verbesserungen (ARIA-Labels)

## Technische Details
- **URL-Parameter**: ?sort_by=sektor&sort_order=asc
- **Erlaubte Sortierfelder**: name, sektor, price, dividend_amount
- **Erlaubte Sortierrichtungen**: asc, desc
- **JavaScript Framework**: Alpine.js
- **Fallback**: Standard-Sortierung nach Name (aufsteigend)

## Status: ✅ Implementierung abgeschlossen, Testing ausstehend
