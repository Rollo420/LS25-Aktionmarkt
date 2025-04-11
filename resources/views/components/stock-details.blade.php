<!-- Aufgabe 
Kennzahl	Beschreibung
Aktueller Kurs	Der Preis der Aktie zum aktuellen Zeitpunkt
Kursveränderung (absolut / %)	Z. B. +1,23 € (+2,3 %)
Dividendenrendite	Ausschüttung in % vom Kurs
EPS (Earnings per Share)	Gewinn je Aktie
Prozentuale Entwicklung	z. B. +43 % in den letzten 6 Monaten
Dividendentermine	Nächster Zahlungstermin

 Infos zum Unternehmen
 Name	Apple Inc.
 Sektor / Branche	Tech, Energie, Finanzen…
 Standort	Hauptsitz (Land, Stadt)
 Beschreibung	Kurztext zur Firma
-->

@props(['stocks'])

<div class="mt-4">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Stock Details') }}
    </h2>
    {{ dd($stocks)}}
<div>