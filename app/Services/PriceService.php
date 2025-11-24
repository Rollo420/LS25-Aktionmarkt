<?php

namespace App\Services;

// Import der StockConfig (oder Config, je nachdem, wie Sie sie speichern)
use App\Models\Config;

class PriceService
{
    // =========================
    // Preisgenerierung modular
    // =========================
    /**
     * Berechnet den neuen Aktienpreis basierend auf dem letzten Preis und verschiedenen Effekten.
     * * @param float $lastPrice
     * @param int $monthIndex
     * @param object|null $stockConfig Das Config-Modell-Objekt der Aktie.
     * @return float
     */
    public function generatePrice(float $lastPrice, int $monthIndex, $stockConfig = null): float
    {
        // Nutze Stock-Config wenn vorhanden, sonst Default-Config
        $config = $this->getConfigFromModel($stockConfig);

        $price = $lastPrice;

        if ($config['useExcelRandom']) {
            $price = $this->applyExcelRandom($price, $config['excelRandomRange']);
        }

        if ($config['useCrashRally']) {
            $price = $this->applyCrashRally($price, $config['crashProbability'], $config['rallyProbability']);
        }

        if ($config['useSeasonalEffect']) {
            $price = $this->applySeasonalEffect($price, $monthIndex, $config['seasonalEffectRange']);
        }

        // Mindestpreis
        if ($price <= 0)
            $price = max(0.1, abs($lastPrice * 0.9));

        return round($price, 2);
    }

    // =========================
    // Config aus Model extrahieren (als private Methode des Service)
    // =========================
    protected function getConfigFromModel($stockConfig = null): array
    {
        // Standard-Konfiguration (wenn keine Stock-spezifische Config gefunden wurde)
        $defaultConfig = [
            'useExcelRandom' => true,
            'excelRandomRange' => 0.04,
            'useSeasonalEffect' => true,
            'seasonalEffectRange' => 0.026,
            'useCrashRally' => true,
            'crashProbability' => 1 / 240,
            'rallyProbability' => 1 / 360,
        ];

        if ($stockConfig) {
            return [
                'useExcelRandom' => true,
                'excelRandomRange' => $stockConfig->volatility_range ?? $defaultConfig['excelRandomRange'],
                'useSeasonalEffect' => true,
                'seasonalEffectRange' => $stockConfig->seasonal_effect_strength ?? $defaultConfig['seasonalEffectRange'],
                'useCrashRally' => true,
                'crashProbability' => 1 / ($stockConfig->crash_interval_months ?? 240),
                'rallyProbability' => 1 / ($stockConfig->rally_interval_months ?? 360),
            ];
        }

        // Fallback auf Default-Config
        return $defaultConfig;
    }

    // =========================
    // Excel-artige Zufallsbewegung
    // =========================
    protected function applyExcelRandom(float $price, float $range): float
    {
        $randomFactor = (mt_rand() / mt_getrandmax() - 0.5) * 2 * $range; // [-range, +range]
        return $price * (1 + $randomFactor);
    }

    // =========================
    // Crash/Rallye Simulation
    // =========================
    protected function applyCrashRally(float $price, float $crashProb = null, float $rallyProb = null): float
    {
        $config = $this->getConfigFromModel(); // Holen der Default-Werte, falls $prob nicht übergeben

        $crashProb = $crashProb ?? $config['crashProbability'];
        $rallyProb = $rallyProb ?? $config['rallyProbability'];

        $rand = mt_rand() / mt_getrandmax();
        if ($rand < $crashProb) {
            $price *= 1 - mt_rand(20, 50) / 100; // Crash -20% bis -50%
        } elseif ($rand < $crashProb + $rallyProb) {
            $price *= 1 + mt_rand(20, 50) / 100; // Rally +20% bis +50%
        }
        return $price;
    }

    // =========================
    // Saisonaler Effekt
    // =========================
    protected function applySeasonalEffect(float $price, int $monthIndex, float $range): float
    {
        // M_PI ist die PHP-Konstante für PI
        $effect = sin(($monthIndex / 12) * 2 * M_PI) * $range; // ±range
        return $price * (1 + $effect);
    }
}
