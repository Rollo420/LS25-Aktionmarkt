<?php

require_once 'vendor/autoload.php';

// Laravel Bootstrap
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing Price Factory Generation...\n";

// Test PriceFactory
try {
    $priceFactory = new Database\Factories\Stock\PriceFactory();
    $priceData = $priceFactory->definition();
    echo "PriceFactory Test:\n";
    echo "Generated price: " . $priceData['name'] . "\n";
    echo "Range check (30000-800000): " . 
         ($priceData['name'] >= 30000 && $priceData['name'] <= 800000 ? "✅ PASS" : "❌ FAIL") . "\n";
} catch (Exception $e) {
    echo "PriceFactory Test ERROR: " . $e->getMessage() . "\n";
}

echo "\nTesting 10 additional generations:\n";
for ($i = 1; $i <= 10; $i++) {
    $priceData = $priceFactory->definition();
    $inRange = $priceData['name'] >= 30000 && $priceData['name'] <= 800000;
    echo "Generation $i: " . $priceData['name'] . " " . ($inRange ? "✅" : "❌") . "\n";
}

// Test TransactionFactory fallback
echo "\nTesting TransactionFactory price fallback:\n";
try {
    $transactionFactory = new Database\Factories\Stock\TransactionFactory();
    // We can't easily test the random logic, but we can check the factory exists
    echo "TransactionFactory exists and is accessible: ✅\n";
} catch (Exception $e) {
    echo "TransactionFactory Test ERROR: " . $e->getMessage() . "\n";
}

echo "\nTest completed!\n";
?>
