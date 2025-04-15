@props(['firmenDetails'])
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

<div class="mt-4">
    <h2 class="font-semibold text-xl text-gray-10 dark:text-gray-10 leading-tight">
        {{ __('Firm Details') }}
    </h2>

    <div class="tow-grid">

        <h3>Firma</h2>
            <h6> {{ $firmenDetails->get()[0]->frima }}</h6>
            <h3>Sector</h3>
            <h4>{{ $firmenDetails->get()[0]->sector }}</h4>

            <h3>Land</h3>
            <h4>{{ $firmenDetails->get()[0]->land }}</h4>
            <h3>Description</h>
            <h4>{{ $firmenDetails->get()[0]->description }}</h4>
    </div>
</div>