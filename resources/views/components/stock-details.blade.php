<!-- Aufgabe 
Aktueller Kurs	Der Preis der Aktie zum aktuellen Zeitpunkt
Kursveränderung (absolut / %)	Z. B. +1,23 € (+2,3 %) 
    // Formel: Kursveränderung absolut = Aktueller Kurs - Vorheriger Kurs
    // Formel: Kursveränderung % = (Kursveränderung absolut / Vorheriger Kurs) * 100
EPS (Earnings per Share)	Gewinn je Aktie
        // Formel: EPS = Gesamter Gewinn / Anzahl der ausgegebenen Aktien
Dividendenrendite	Ausschüttung in % vom Kurs
    // Formel: Dividendenrendite = (Dividende je Aktie / Aktueller Kurs) * 100
Prozentuale Entwicklung	z. B. +43 % in den letzten 6 Monaten
    // Formel: Prozentuale Entwicklung = ((Aktueller Kurs - Kurs vor 6 Monaten) / Kurs vor 6 Monaten) * 100
Dividendentermine	Nächster Zahlungstermin
    // Keine Formel, wird direkt aus den Daten des Unternehmens entnommen

 Infos zum Unternehmen
 Name	Apple Inc.
 Sektor / Branche	Tech, Energie, Finanzen…
 Standort	Hauptsitz (Land, Stadt)
 Beschreibung	Kurztext zur Firma
-->

@props(['stockDetails'])

<div class="mt-4">
    <h2 class="font-semibold text-xl text-gray-10 dark:text-gray-10 leading-tight">
        {{ __('Stock Details') }}
    </h2>

    <div class="tow-grid">
        <h3>Price</h3>
        <h5>{{ number_format($stockDetails['currentPrice'], 2, ",", " ") }} €</h5>

        <h3>Price Development (€)</h3>
        <h5 class="{{ $stockDetails['priceDevelopment'] >= 0 ? 'text-green-500' : 'text-red-500' }}">
            {{ $stockDetails['priceDevelopment'] >= 0 ? '+' : '' }}
            {{ number_format($stockDetails["priceDevelopment"], 6, ",", " ") }} €
        </h5>

        <h3>Percentage Development (%)</h3>
        <h5 class="{{ $stockDetails['percentageDevelopment'] >= 0 ? 'text-green-500' : 'text-red-500' }}">
            {{ $stockDetails['percentageDevelopment'] >= 0 ? '+' : '' }}
            {{ number_format($stockDetails["percentageDevelopment"], 6, ",", " ") }} %
        </h5>

        <h3>EPS (Earnings per Share)</h3>
        <h5>{{ $stockDetails["eps"]}} €</h5>

        <h3>Payout Ratio (%)</h3>
        <h5>{{ number_format($stockDetails["payoutRatio"], 6, ",", " ") }} %</h5>

        <h3>PER (Price-to-earnings ratio)</h3>
        <h5>{{ number_format($stockDetails["kgv"], 6, ",", " ") }}</h5>

        <h3 class="font-semibold">Dividend per Share (€)</h3>
        <h5>{{ number_format($stockDetails['dividende']['dividendPerShare'], 2, ",", " ") }} €</h5>

        <h3 class="font-semibold">Dividend Yiel (%)</h3>
        <h5>{{ number_format($stockDetails['dividende']['dividendYield'], 2, ",", " ") }} €</h5>

        <h3 class="font-semibold">Next Dividend Date</h3>
        <h5>{{ $stockDetails['dividende']['nextDividendDate'] }}</h5>
    </div>
</div>