# TODO: Reparatur der createChartData-Methode im DashboardController

## Aufgaben

- [x] createChartData-Methode reparieren:
  - [x] Undefinierte Variablen $latestGameTime und $firstGameTime entfernt und durch $currentGameTime ersetzt
  - [x] Konsistent GameTime::getCurrentGameTime() verwendet
  - [x] GameTimeService genutzt, um simulierte Monate zu bauen
  - [x] PriceResolverService für Preisauflösung verwendet
  - [x] Sicherstellen, dass alle Berechnungen auf Ingame-Zeit basieren

- [x] Testen mit Laravel Sail:
  - [x] GameTime::getCurrentGameTime() funktioniert
  - [x] Simulierte Monate korrekt berechnet (letzte 12 GameTime-Monate)
  - [x] Route /dashboard existiert
