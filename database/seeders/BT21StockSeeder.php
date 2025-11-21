<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Stock\Stock;
use Illuminate\Database\Eloquent\Collection;

class BT21StockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): ?Collection 
    {

        $stocks = Stock::factory()->createMany([

            [

            'name' => 'Aktien Name',

            'firma' => 'Frirmen namen',

            'sektor' => 'sektor(MIlch, Hanf)',

            'land' => 'land',

            'description' => 'beschreibung ja dui weißt',

            'net_income' => 1255000,

            'dividend_frequency' => 0//einer von 1, 2, 4, wann es im jahr dividendne gibt 

        ],

        ['name' => 'Back To 21 AG',

            'firma' => 'Back To 21 Holding AG',

            'sektor' => 'Multiunternehmen',

            'land' => 'USA',

            'description' => 'Die Back To 21 AG ist eine US-amerikanische Holdinggesellschaft mit einem diversifizierten Portfolio von Schwesterunternehmen. Das Kerngeschäft basiert auf einer vertikal integrierten Landwirtschaft in den USA: Der Anbau von Grass dient primär zur Versorgung der eigenen Biogasanlagen (BGA) und für den Handel. Dieses Modell sichert dem Multiunternehmen eine stabile Wertschöpfungskette von der Agrarproduktion bis zur Bioenergie und bietet Investoren ein diversifiziertes Engagement im wachsenden US-Markt.',

            'net_income' => 2255000,

            'dividend_frequency' => 3

            // JEtzt bist du dran! :)

    ],

    [

        'name' => 'PrivatBank',

        'firma' => 'Try Soft Holding AG',

        'sektor' => 'Finanzdienstleistungen',

        'land' => 'Multi-National',

        'description' => 'Die PrivatBank ist das mächtigste Finanzinstitut der Welt und dient als zentrale Staatsbank für acht souveräne Nationen. Diese haben ihre komplette Finanzverwaltung, alle öffentlichen Dienste und sämtliche staatlichen Auszahlungen vollständig an die Bank übertragen. Damit steuert die PrivatBank ganze Volkswirtschaften und agiert faktisch wie ein eigener Staat.

Als einziges Finanzinstitut weltweit besitzt sie staatlich legitimierte Vollstreckungsbeamte, eigene Ermittlungsbehörden und umfassende Eingriffsrechte. Öffentliche Zahlungen, Infrastrukturprojekte, Gutachten, Versicherungen und sicherheitsrelevante Finanzkontrollen laufen exklusiv über sie.',

        'net_income' =>22500000,

        'dividend_frequency' => 2

    ],

    [

        'name' => 'TryS AG',

        'firma' => 'Try Soft Holding AG',

        'sektor' => 'Technologie',

        'land' => 'Schweiz',

        'description' => 'Die Try Soft Holding AG ist das größte und mächtigste Unternehmen der Welt. Als Muttergesellschaft der PrivatBank kontrolliert sie über Verträge mit acht Staaten große Teile deren Verwaltung, Finanzsysteme und öffentlicher Dienste.

Zur Holding gehören globale Spitzenkonzerne wie Valhalla Defense Industries, BlackRiver Oil Corporation, SafeFarm Insurance AG und Aureon Dynamics SE – wodurch Try Soft Einfluss auf Rüstung, Energie, Versicherungen, Technologie und staatliche Infrastruktur besitzt.

Mit ihrem umfassenden Macht-Netzwerk gilt Try Soft als wirtschaftliche Supermacht, deren Entscheidungen ganze Märkte und Regierungen formen.',

        'net_income' => 51000000,

        'dividend_frequency' => 1,

    ],

    [

        'name' => 'Valhalla Defense Industries',

        'firma' => 'Try Soft Holding AG',

        'sektor' => 'Rüstungsindustrie',

        'land' => 'Germany',

        'description' => 'Valhalla Defense Industries ist ein führender globaler Rüstungskonzern mit Sitz in den USA. Das Unternehmen entwickelt und produziert eine breite Palette von Verteidigungstechnologien, darunter fortschrittliche Waffensysteme, militärische Fahrzeuge, Überwachungstechnologien und Cyberabwehrlösungen.',

        'net_income' => 3200000,

        'dividend_frequency' => 1,

    ],

    [

        'name' => 'BlackRiver Oil Corporation',

        'firma' => 'Try Soft Holding AG',

        'sektor' => 'Energie',

        'land' => 'Canada',

        'description' => 'Die BlackRiver Oil Corporation ist ein global tätiges Energieunternehmen mit Schwerpunkt auf der Exploration, Förderung und Vermarktung von Erdöl und Erdgas. Das Unternehmen betreibt umfangreiche Förderanlagen und Pipelines in Nordamerika, Südamerika, Europa und Asien.',

        'net_income' => 4200000,

        'dividend_frequency' => 1,

    ],

    [

        'name' => 'SafeFarm Insurance AG',

        'firma' => 'Try Soft Holding AG',

        'sektor' => 'Versicherungen',

        'land' => 'Switzerland',

        'description' => 'Die SafeFarm Insurance AG ist ein führendes Versicherungsunternehmen mit einem breiten Portfolio an Versicherungsprodukten für Privatkunden und Unternehmen. Das Unternehmen bietet Lebensversicherungen, Krankenversicherungen, Sachversicherungen und spezialisierte Versicherungslösungen für landwirtschaftliche Betriebe an.',

        'net_income' => 2900000,

        'dividend_frequency' => 1,

    ],

    [

        'name' => 'Aureon Dynamics SE',

        'firma' => 'Try Soft Holding AG',

        'sektor' => 'Technologie',

        'land' => 'USA',

        'description' => 'Die Aureon Dynamics SE ist ein innovatives Technologieunternehmen, das sich auf die Entwicklung fortschrittlicher Softwarelösungen und digitaler Plattformen spezialisiert hat. Das Unternehmen bietet Produkte und Dienstleistungen in den Bereichen Künstliche Intelligenz, Cloud-Computing, Big Data und Internet der Dinge (IoT) an.',

        'net_income' => 3750000,

        'dividend_frequency' => 1,

    ],

    [

        'name' => 'Tommynock',

        'firma' => 'Try Soft Holding AG',

        'sektor' => 'Gaming',

        'land' => 'Germany',

        'description' => 'Tommynock ist ein führendes Unternehmen in der Gaming-Branche, das sich auf die Entwicklung und Veröffentlichung von Videospielen für verschiedene Plattformen spezialisiert hat. Das Unternehmen ist bekannt für seine innovativen Spielkonzepte, hochwertigen Grafiken und fesselnden Spielerlebnisse.',

        'net_income' => 1850000,

        'dividend_frequency' => 1,

    ],

    [

        'name' => 'MedWeed',

        'firma' => 'CannaHealth Group',

        'sektor' => 'Hanfherstellung',

        'land' => 'Germany',

        'description' => 'MedWeed ist ein führendes Unternehmen in der Hanfherstellungsbranche, das sich auf die Produktion und den Vertrieb von medizinischem Cannabis und Hanfprodukten spezialisiert hat. Das Unternehmen betreibt moderne Anbauanlagen und Forschungseinrichtungen, um hochwertige Produkte für Patienten und Verbraucher bereitzustellen.',

        'net_income' => 200000,

        'dividend_frequency' => 1,

    ],

    [

        'name' => 'GreenBale',

        'firma' => 'MeadowGroe Inc.',

        'sektor' => 'Grasssilage Herstellung',

        'land' => 'USA',

        'description' => '',

        'net_income' => 2250000,

        'dividend_frequency' => 1,

    ],

    [

        'name' => 'SolarWind Dynamics',

        'firma' => 'Back To 21 Holding AG',

        'sektor' => 'Erneuerbare Energien',

        'land' => 'Canada',

        'description' => '',

        'net_income' => 1100000,

        'dividend_frequency' => 1,

    ],

    [

    'name' => 'DDC',

        'firma' => 'Deutsche Doggen Club AG',

        'sektor' => 'Hundezucht',

        'land' => 'Germany',

        'description' => 'DDC ist ein renommiertes Unternehmen, das sich auf die Zucht, Ausbildung und den Verkauf von Deutschen Doggen spezialisiert hat. Mit langjähriger Erfahrung und einem engagierten Team von Züchtern und Trainern bietet DDC hochwertige Hunde für Familien, Züchter und Hundeliebhaber weltweit an.',

        'net_income' => 11000000,

        'dividend_frequency' => 1

    ],

    [

        'name' => 'AgroTech AG',

        'firma' => 'Back To 21 Holding AG',

        'sektor' => 'Landwirtschaftstechnologie',

        'land' => 'Germany',

        'description' => 'AgroTech Solutions ist ein innovatives Unternehmen, das sich auf die Entwicklung und Implementierung modernster Technologien für die Landwirtschaft spezialisiert hat. Das Unternehmen bietet Lösungen wie Präzisionslandwirtschaft, automatisierte Bewässerungssysteme und Drohnentechnologie zur Optimierung landwirtschaftlicher Prozesse an.',

        'net_income' => 950000,

        'dividend_frequency' => 2

    ],  

    [

        'name' => 'InsureSafe und Co.',

        'firma' => 'Try Soft Holding AG',

        'sektor' => 'Versicherungen',

        'land' => 'USA',

        'description' => '',

        'net_income' => 1300000,

        'dividend_frequency' => 3

    ],

    [

        'name' => 'TechNova Inc.',

        'firma' => 'Try Soft Holding AG',

        'sektor' => 'Technologie',

        'land' => 'Canada',

        'description' => '',

        'net_income' => 2700000,

        'dividend_frequency' => 4

    ],

    [

        'name' => 'Fendt Group',

        'firma' => 'Fentd Holding SbR',

        'sektor' => 'Landwirtschaftliche Maschinen',

        'land' => 'Germany',

        'description' => 'Fendt Group ist ein weltweit führender Hersteller von landwirtschaftlichen Maschinen und Ausrüstungen. Das Unternehmen bietet eine',

        'net_income' => 1600000,

        'dividend_frequency' => 1

    ],

    [

        'name' => 'Case IH Corporation',

        'firma' => 'Fentd Holding SbR',

        'sektor' => 'Landwirtschaftliche Maschinen',

        'land' => 'Germany',

        'description' => 'Case IH Corporation ist ein global tätiges Unternehmen, das sich auf die Herstellung und den Vertrieb von landwirtschaftlichen Maschinen und Ausrüstungen spezialisiert hat. Das Unternehmen bietet eine breite Palette von Produkten an, darunter Traktoren, Mähdrescher, Pflüge und Sämaschinen, die für verschiedene landwirtschaftliche Anwendungen entwickelt wurden.',

        'net_income' => 1400000,

        'dividend_frequency' => 1

    ],

    [

        'name' => 'Claas AG',

        'firma' => 'Fentd Holding SbR',

        'sektor' => 'Landwirtschaftliche Maschinen',

        'land' => 'USA',

        'description' => 'Claas AG ist ein führender Hersteller von landwirtschaftlichen Maschinen und Ausrüstungen mit Sitz in Deutschland. Das Unternehmen bietet eine breite Palette von Produkten an, darunter Mähdrescher, Traktoren, Feldhäcksler und Rundballenpressen, die für verschiedene landwirtschaftliche Anwendungen entwickelt wurden.',

        'net_income' => 1800000,

        'dividend_frequency' => 1

    ],

    [

        'name' => 'John Deere GmbH',

        'firma' => 'Fentd Holding SbR',

        'sektor' => 'Landwirtschaftliche Maschinen',

        'land' => 'USA',

        'description' => 'John Deere GmbH ist die deutsche Tochtergesellschaft des weltweit führenden Herstellers von Landmaschinen, John Deere. Das Unternehmen bietet eine breite Palette von Produkten an, darunter Traktoren, Mähdrescher, Pflüge und Sämaschinen, die für verschiedene landwirtschaftliche Anwendungen entwickelt wurden.',

        'net_income' => 2000000,

        'dividend_frequency' => 1

    ],

    [

        'name' => 'New Holland Agriculture',

        'firma' => 'Fentd Holding SbR',

        'sektor' => 'Landwirtschaftliche Maschinen',

        'land' => 'Italy',

        'description' => 'New Holland Agriculture ist ein global tätiges Unternehmen, das sich auf die Herstellung und den Vertrieb von landwirtschaftlichen Maschinen und Ausrüstungen spezialisiert hat. Das Unternehmen bietet eine breite Palette von Produkten an, darunter Traktoren, Mähdrescher, Pflüge und Sämaschinen, die für verschiedene landwirtschaftliche Anwendungen entwickelt wurden.',

        'net_income' => 1500000,

        'dividend_frequency' => 1

    ],

    [

        'name' => 'AGCO Corporation',

        'firma' => 'Fentd Holding SbR',

        'sektor' => 'Landwirtschaftliche Maschinen',

        'land' => 'USA',

        'description' => 'AGCO Corporation ist ein global tätiges Unternehmen, das sich auf die Herstellung und den Vertrieb von landwirtschaftlichen Maschinen und Ausrüstungen spezialisiert hat. Das Unternehmen bietet eine breite Palette von Produkten an, darunter Traktoren, Mähdrescher, Pflüge und Sämaschinen, die für verschiedene landwirtschaftliche Anwendungen entwickelt wurden.',

        'net_income' => 1700000,

        'dividend_frequency' => 1

    ],

    [

        'name' => 'Kubota Deutschland GmbH',

        'firma' => 'Fentd Holding SbR',

        'sektor' => 'Landwirtschaftliche Maschinen',

        'land' => 'Germany',

        'description' => 'Kubota Deutschland GmbH ist die deutsche Tochtergesellschaft des weltweit führenden Herstellers von Landmaschinen, Kubota Corporation. Das Unternehmen bietet eine breite Palette von Produkten an, darunter Traktoren, Mähdrescher, Pflüge und Sämaschinen, die für verschiedene landwirtschaftliche Anwendungen entwickelt wurden.',

        'net_income' => 1300000,

        'dividend_frequency' => 1

    ],

    [

        'name' => 'Deutz-Fahr GmbH',

        'firma' => 'Fentd Holding SbR',

        'sektor' => 'Landwirtschaftliche Maschinen',

        'land' => 'Germany',

        'description' => 'Deutz-Fahr GmbH ist ein führender Hersteller von Landmaschinen mit Sitz in Deutschland. Das Unternehmen bietet eine',

        'net_income' => 1200000,

        'dividend_frequency' => 1  

    ],

    [

        'name' => 'Lemken GmbH & Co. KG',

        'firma' => 'Fentd Holding SbR',

        'sektor' => 'Landwirtschaftliche Maschinen',

        'land' => 'Germany',

        'description' => 'Lemken GmbH & Co. KG ist ein führender Hersteller von Landmaschinen mit Sitz in Deutschland. Das Unternehmen bietet eine',

        'net_income' => 1100000,

        'dividend_frequency' => 1

    ],

    [

        'name' => 'Horsch Maschinen GmbH',

        'firma' => 'Fentd Holding SbR',

        'sektor' => 'Landwirtschaftliche Maschinen',

        'land' => 'Germany',

        'description' => 'Horsch Maschinen GmbH ist ein führender Hersteller von Landmaschinen mit Sitz in Deutschland. Das Unternehmen bietet eine',

        'net_income' => 1000000,

        'dividend_frequency' => 1     

    ],

    [

        'name' => 'Chimera foundation',

        'firma' => 'Chimera foundation',

        'sektor' => 'Non-Profit',

        'land' => 'Global',

        'description' => 'Die Chimera Foundation ist eine globale Non-Profit-Organisation, die sich der Förderung von Bildung, Gesundheit und nachhaltiger Entwicklung in benachteiligten Gemeinschaften weltweit widmet. Durch Partnerschaften mit lokalen Organisationen und internationalen Institutionen setzt sich die Stiftung für positive Veränderungen ein und unterstützt Projekte, die das Leben von Menschen verbessern und langfristige Entwicklungschancen schaffen.',

        'net_income' => 0,

        'dividend_frequency' => 1,

    ],

    [

        'name' => 'Spezi Org.',

        'firma' => 'MeadowGroe Inc.',

        'sektor' => 'Lebensmittel',

        'land' => 'Global',

        'description' => 'Spezi Org. ist ein weltweit tätiges Unternehmen, das sich auf die Herstellung und den Vertrieb von Erfrischungsgetränken spezialisiert hat. Mit einer breiten Palette von Produkten, die von klassischen Limonaden bis hin zu innovativen Geschmacksrichtungen reichen, bedient Spezi Org. Kunden in verschiedenen Märkten und Kulturen. Das Unternehmen legt großen Wert auf Qualität, Nachhaltigkeit und Kundenzufriedenheit und strebt danach, ein führender Akteur in der globalen Getränkeindustrie zu sein.',

        'net_income' => 500000,

        'dividend_frequency' => 1,

    ],

    [

        'name' => 'GlobalTech Solutions',

        'firma' => 'Try Soft Holding AG',

        'sektor' => 'Technologie',

        'land' => 'USA',

        'description' => 'GlobalTech Solutions ist ein führendes Technologieunternehmen, das innovative Softwarelösungen und IT-Dienstleistungen für Unternehmen weltweit anbietet. Mit einem Fokus auf Digitalisierung, Cloud-Computing und Cybersicherheit unterstützt GlobalTech Solutions seine Kunden dabei, ihre Geschäftsprozesse zu optimieren und wettbewerbsfähig zu bleiben.',

        'net_income' => 3500000,

        'dividend_frequency' => 2

    ],

    [

        'name' => 'NutriFrance S.A.',

        'firma' => 'Ventaris Group',

        'sektor' => 'Massenproduktion Tiernahrung',

        'land' => 'USA',

        'description' => 'NutriFrance S.A. ist ein führendes Unternehmen in der Massenproduktion von Tiernahrung mit Sitz in den USA. Das Unternehmen produziert eine breite Palette von hochwertigen Futtermitteln für Haustiere und Nutztiere, die auf die spezifischen Ernährungsbedürfnisse verschiedener Tierarten abgestimmt sind. Mit modernster Technologie und strengen Qualitätskontrollen stellt NutriFrance S.A. sicher, dass seine Produkte den höchsten Standards entsprechen und zur Gesundheit und zum Wohlbefinden der Tiere beitragen.',

        'net_income' => 800000,

        'dividend_frequency' => 1,

    ],

    [

        'name' => 'AquaPure Inc.',

        'firma' => 'Ventaris Group',

        'sektor' => 'Wasseraufbereitung',

        'land' => 'Canada',

        'description' => 'AquaPure Inc. ist ein führendes Unternehmen im Bereich der Wasseraufbereitung mit Sitz in Kanada. Das Unternehmen entwickelt und vertreibt innovative Technologien und Lösungen zur Reinigung und Aufbereitung von Trinkwasser für Haushalte, Industrie und kommunale Einrichtungen. Mit einem Fokus auf Nachhaltigkeit und Umweltschutz trägt AquaPure Inc. dazu bei, den Zugang zu sauberem Wasser weltweit zu verbessern.',

        'net_income' => 950000,

        'dividend_frequency' => 1,

    ],

    [

        'name' => 'EcoRüstung GmbH',

        'firma' => 'Ventaris Group',

        'sektor' => 'Rüstungsindustrie',

        'land' => 'Belgium',

        'description' => 'EcoRüstung GmbH ist ein innovatives Unternehmen in der Rüstungsindustrie mit Sitz in Belgien. Das Unternehmen spezialisiert sich auf die Entwicklung und Produktion umweltfreundlicher und nachhaltiger Verteidigungstechnologien, die den ökologischen Fußabdruck militärischer Operationen minimieren. EcoRüstung GmbH setzt auf modernste Forschung und Entwicklung, um Lösungen zu bieten, die sowohl den Sicherheitsanforderungen als auch den Umweltstandards gerecht werden.',

        'net_income' => 700000,

        'dividend_frequency' => 1,

    ],

    [

        'name' => '7',

        'net_income' => 1396988330,

        'dividend_frequency' => 1

    ]

    ]);
    return $stocks;
}

}