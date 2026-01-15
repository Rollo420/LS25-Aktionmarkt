<?php

/**
 * DEBUG DIVIDEND FIX SCRIPT
 * This script will debug and fix the dividend issue
 */

require_once 'vendor/autoload.php';

try {
    $app = require_once 'bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    echo "=== DIVIDEND DEBUG SCRIPT ===\n\n";

    // Test GameTime
    echo "1. Testing GameTime...\n";
    $currentGT = App\Models\GameTime::getCurrentGameTime();
    echo "Current GameTime: " . ($currentGT ? $currentGT->name : 'NULL') . "\n";
    
    // Test all GameTimes
    $allGTs = App\Models\GameTime::orderBy('id', 'desc')->take(10)->get();
    echo "Latest GameTimes:\n";
    foreach ($allGTs as $gt) {
        echo "  ID: {$gt->id}, Name: {$gt->name}\n";
    }

    echo "\n2. Testing Stocks and Dividends...\n";
    
    $stocks = App\Models\Stock\Stock::with('dividends.gameTime')->take(3)->get();
    
    foreach ($stocks as $stock) {
        echo "\nStock: {$stock->name} (ID: {$stock->id})\n";
        echo "Dividend frequency: {$stock->dividend_frequency}\n";
        
        // Latest dividend
        $latestDiv = $stock->getLatestDividend();
        if ($latestDiv) {
            echo "Latest dividend: {$latestDiv->gameTime->name} (amount: {$latestDiv->amount_per_share})\n";
        } else {
            echo "No dividends found!\n";
        }
        
        // Test both methods
        $oldMethod = $stock->calculateNextDividendDate();
        $newMethod = $stock->calculateNextDividendDateAtCurrentGameTime();
        
        echo "Old method result: " . ($oldMethod ? $oldMethod->format('d.m.Y') : 'NULL') . "\n";
        echo "New method result: " . ($newMethod ? $newMethod->format('d.m.Y') : 'NULL') . "\n";
    }

    echo "\n3. Debug complete.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
