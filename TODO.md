# Problem-Analyse: Dashboard zeigt keine Dividendendiagramme im Farm-Modus

## Problem
Der Nutzer hat 2 Aktien (Back To 21), aber wenn der Farm-Modus aktiviert ist, werden die Dividendendiagramme nicht mehr angezeigt.

## Root Cause Analysis

### 1. Farm-Modus Funktionsweise
- `AuthHelper::user()` prüft `session('farmMode')` 
- Wenn aktiviert: gibt `Auth::user()->farms()->first()` zurück (Farm-Instanz)
- Sonst: gibt normalen `Auth::user()` zurück

### 2. Problem-Kette
1. **DashboardController** → ruft `getUserStocksWithStatistiks()` auf
2. **StockService** → `getUserStocks($user)` mit Farm-Instanz als `$user`
3. **Transaction-Abfrage** → `Transaction::where('user_id', $user->id)` → sucht nach Farm-ID statt echter User-ID
4. **Keine Daten** → Dividendendiagramme sind leer, da keine Transaktionen gefunden werden

### 3. Warum nur Dividendendiagramme?
- Andere Dashboard-Bereiche nutzen verschiedene Methoden
- Dividendendiagramme basieren auf Transaktionen von `$user` (Farm-Instanz)
- Aber die echten Aktien und Dividenden sind an den echten Benutzer gebunden

## Lösungsplan

### Option A: Farm-Transaktionen korrekt verknüpfen (Empfohlen)
**Problem**: Farm hat keine eigenen Transaktionen
**Lösung**: Farm-Transaktionen über Pivot-Tabelle oder erweiterte Querys

### Option B: AuthHelper anpassen für Dashboard
**Problem**: Dashboard braucht echte Benutzerdaten
**Lösung**: Separate Methode für Dashboard/Statistiken ohne Farm-Modus

### Option C: Transaktionen über Farm-Users aggregieren
**Problem**: Farm-Transaktionen sind über Members verteilt
**Lösung**: Query alle Farm-Member-Transaktionen

## Empfehlung: Option A (Farm-Transaktionen korrekt verknüpfen)
- Erweitere `getCurrentQuantity()` und `getUserStocks()` Methoden
- Farm kann eigene Transaktionen haben oder Member-Transaktionen aggregieren
- Bleibt konsistent mit der Farm-Architektur

## Nächste Schritte
1. **Verstehen**: Aktuelle Farm-Transaktionsstruktur analysieren
2. **Implementieren**: Korrekte Transaktion-Verknüpfung für Farm-Modus
3. **Testen**: Dashboard mit Farm-Modus aktiviert testen
4. **Validieren**: Alle Dashboard-Bereiche funktionieren korrekt
