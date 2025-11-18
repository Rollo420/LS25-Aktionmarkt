# TODO: Dividenden-Auszahlung beheben und Timeskip-Refresh für alle User-Clients verbessern

## Problemstellung
- Dividenden werden nicht immer korrekt ausgeschüttet (z.B. bei Timeskip).
- Nach Timeskip werden nicht alle User-Clients im Browser refreshed, sodass nicht alle User die neuesten Daten (Preise, Dividenden, Kontostände) sofort sehen.

## Ziel
- Dividenden zuverlässig auszahlen, indem vorhandene Funktionen (DividendeService::shareDividendeToUsers, ProcessDividendPayout Job) genutzt und verbessert werden.
- Alle User-Clients nach Timeskip automatisch refreshen, damit alle User direkt die neuesten Daten haben (ohne manuelles Reload).

## Detaillierter Plan zur Dividenden-Auszahlung
1. **Analyse der vorhandenen Logik in TimeController::skipTime()**:
   - Für jede neue GameTime (bei Timeskip) wird für jede Stock geprüft, ob eine Dividende fällig ist (calculateNextDividendDate()).
   - Wenn fällig, wird der ProcessDividendPayout Job synchron ausgeführt (nicht dispatched, um sofort zu laufen).
   - Job ruft DividendeService::shareDividendeToUsers() auf, die:
     - Für jeden User mit Holdings die Dividende berechnet (quantity * dividend_per_share).
     - Bankkontostand erhöht (addBankAccountBalance).
     - Transaktion erstellt (type: 'dividend').
   - Neue Dividende in DB gespeichert (Dividend::create).

2. **Verbesserungen für zuverlässige Auszahlung**:
   - Logging in shareDividendeToUsers() erweitern: Pro User loggen, ob Auszahlung erfolgreich (Bankupdate + Transaktion).
   - Prüfen, ob calculateNextDividendDate() korrekt arbeitet (basierend auf dividend_frequency).
   - Sicherstellen, dass nur einmal pro Monat/Stock ausgeschüttet wird (exists-Check ist da).
   - Test: Simuliere Timeskip und prüfe Logs + DB für Dividenden-Transaktionen.

3. **Nutzung vorhandener Funktionen**:
   - DividendeService::shareDividendeToUsers() bleibt Kernfunktion.
   - ProcessDividendPayout Job als Wrapper beibehalten (für potentielle Async-Nutzung später).
   - Keine neuen Services erstellen – vorhandene erweitern.

## Detaillierter Plan zum Timeskip-Refresh für alle User-Clients
1. **Analyse der vorhandenen Logik**:
   - Nach Timeskip wird TimeskipCompleted Event gebroadcastet (broadcast(new TimeskipCompleted())).
   - Event geht an Channel 'timeskip' mit broadcastAs 'timeskip.completed'.
   - JS (timeskip-listener.js) hört auf Channel 'timeskip' und triggert window.location.reload() bei Event.
   - Broadcasting ist konfiguriert (config/broadcasting.php, wahrscheinlich Redis/Pusher).

2. **Verbesserungen für zuverlässigen Refresh aller Clients**:
   - Sicherstellen, dass Broadcasting für alle connected Clients funktioniert (nicht nur 'toOthers()', da 'toOthers()' entfernt wurde).
   - JS-Listener verbessern: Statt full reload, nur relevante Daten fetchen (z.B. via AJAX Charts, Dashboard, Holdings refreshen), um smoother zu sein.
   - Fallback: Wenn Broadcasting fehlschlägt, Timer-basierten Refresh nach 5-10 Sek nach Timeskip.
   - Logging: Broadcast-Event loggen, JS-Console logs prüfen.
   - Test: Mehrere Browser-Tabs/User-Sessions öffnen, Timeskip auslösen, prüfen ob alle refreshen.

3. **Nutzung vorhandener Funktionen**:
   - TimeskipCompleted Event beibehalten.
   - JS-Listener erweitern, aber nicht neu schreiben.
   - Broadcasting-Konfig nutzen.

## Schritte zur Umsetzung
1. Logging in DividendeService erweitern.
2. calculateNextDividendDate() debuggen (Logs hinzufügen).
3. JS-Listener verbessern (AJAX-Refresh statt full reload).
4. Fallback-Timer in JS hinzufügen.
5. Tests durchführen: Timeskip simulieren, Logs prüfen, Multi-Tab-Test.

## Followup
- Nach Änderungen: Laravel Sail starten, Timeskip testen, Logs prüfen, Browser-DevTools für JS.
- Wenn Probleme: Broadcasting-Konfig prüfen (Redis laufen?), JS-Fehler logs.
