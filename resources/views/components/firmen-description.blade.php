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

@props(['frimaDetails'])

<div class="mt-4">
    <h2 class="font-semibold text-xl text-gray-10 dark:text-gray-10 leading-tight">
        {{ __('Stock Details') }}
    </h2>
    
    <div class="grid grid-flow-col grid-rows-2 gap-4">
        <div class="row-span-3 grid grid-rows-subgrid gap-1">
            <p>Price €</p>
            <div class="row-start-3">
                <p>{{ $details['currentPrice'] }}€</p>
            </div>
        </div>

        <div class="row-span-2 grid grid-rows-subgrid gap-1">
            <p>Price Change</p>
            <div class="row-start-1">
                <p>{{ $details['priceChange'] }}€</p>
            </div>
        </div>    
    </div>
</div>