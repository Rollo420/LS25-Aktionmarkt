<?php

/**
 * Payment Routes Validation Script
 * This script validates that the payment routes are correctly defined
 */

echo "=== Payment Routes Validation ===\n\n";

$routesFile = '/home/woodly/Coding/Laravel/LS25-Aktionmarkt/routes/web.php';

if (!file_exists($routesFile)) {
    echo "❌ Error: routes/web.php not found\n";
    exit(1);
}

$content = file_get_contents($routesFile);

// Define what we expect to find
$expectedPatterns = [
    'payment.index' => 'Route::get.*payment.*name.*payment\.index',
    'payment.payin' => 'Route::post.*payment.*payin.*name.*payment\.payin',
    'payment.payout' => 'Route::post.*payment.*payout.*name.*payment\.payout',
    'payment.transfer' => 'Route::post.*payment.*transfer.*name.*payment\.transfer',
    'payment.transaction' => 'Route::post.*payment.*transaction.*name.*payment\.transaction',
];

// Define what we should NOT find (conflicting routes)
$problematicPatterns = [
    'POST /payment route' => 'Route::post.*\/payment.*name.*payment\.store',
];

echo "✅ Checking for required routes:\n";
foreach ($expectedPatterns as $routeName => $pattern) {
    if (preg_match('/' . $pattern . '/i', $content)) {
        echo "  ✅ {$routeName} - FOUND\n";
    } else {
        echo "  ❌ {$routeName} - MISSING\n";
    }
}

echo "\n❌ Checking for problematic routes:\n";
foreach ($problematicPatterns as $description => $pattern) {
    if (preg_match('/' . $pattern . '/i', $content)) {
        echo "  ❌ {$description} - FOUND (should be removed)\n";
    } else {
        echo "  ✅ {$description} - CORRECTLY REMOVED\n";
    }
}

echo "\n=== Summary ===\n";

// Count matches
$requiredCount = 0;
foreach ($expectedPatterns as $pattern) {
    if (preg_match('/' . $pattern . '/i', $content)) {
        $requiredCount++;
    }
}

$problemCount = 0;
foreach ($problematicPatterns as $pattern) {
    if (preg_match('/' . $pattern . '/i', $content)) {
        $problemCount++;
    }
}

echo "Required routes found: {$requiredCount}/" . count($expectedPatterns) . "\n";
echo "Problematic routes found: {$problemCount}\n";

if ($requiredCount == count($expectedPatterns) && $problemCount == 0) {
    echo "\n🎉 SUCCESS: All payment routes are correctly defined!\n";
    echo "The 'POST method not supported' error should now be fixed.\n";
} else {
    echo "\n⚠️  ISSUES DETECTED:\n";
    if ($requiredCount < count($expectedPatterns)) {
        echo "- Some required routes are missing\n";
    }
    if ($problemCount > 0) {
        echo "- Problematic routes still exist\n";
    }
}

echo "\n=== Next Steps ===\n";
echo "1. Restart your Laravel development server\n";
echo "2. Test the payment forms in the browser\n";
echo "3. Clear Laravel cache if needed: php artisan route:clear\n";
echo "4. Verify no more 'Method Not Supported' errors occur\n";

echo "\n=== Validation Complete ===\n";
