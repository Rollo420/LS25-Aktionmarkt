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
    <h2 class="font-semibold text-lg sm:text-xl md:text-2xl lg:text-3xl xl:text-4xl 2xl:text-5xl 3xl:text-6xl 4xl:text-7xl 5xl:text-8xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Firm Details') }}
    </h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">

        <div>
            <h3 class="font-medium text-sm sm:text-base text-gray-700 dark:text-gray-300">Firma</h3>
            <p class="text-sm sm:text-base text-gray-900 dark:text-gray-100">{{ $firmenDetails->frima }}</p>
        </div>

        <div>
            <h3 class="font-medium text-sm sm:text-base text-gray-700 dark:text-gray-300">Sector</h3>
            <p class="text-sm sm:text-base text-gray-900 dark:text-gray-100">{{ $firmenDetails->sector }}</p>
        </div>

        <div>
            <h3 class="font-medium text-sm sm:text-base text-gray-700 dark:text-gray-300">Land</h3>
            <p class="text-sm sm:text-base text-gray-900 dark:text-gray-100">{{ $firmenDetails->land }}</p>
        </div>

        <div>
            <h3 class="font-medium text-sm sm:text-base text-gray-700 dark:text-gray-300">Description</h3>
            <p class="text-sm sm:text-base text-gray-900 dark:text-gray-100">{{ $firmenDetails->description }}</p>
        </div>
    </div>
</div>
