<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

// Bootstrap the application (console kernel) so Eloquent and services are available
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\ChartController;

$controller = new ChartController();
$stockId = 1;
$limits = [3,6,12,24];

foreach ($limits as $limit) {
    echo "--- LIMIT: $limit ---\n";
    try {
        $view = $controller->OneChart($stockId, $limit);
        if (is_object($view) && method_exists($view, 'getData')) {
            $data = $view->getData();
            $chart = $data['chartData'] ?? null;
            if ($chart) {
                echo "Labels: \n";
                print_r($chart['labels'] ?? []);
                echo "Data: \n";
                print_r($chart['datasets'][0]['data'] ?? []);
                echo "Last point: " . end($chart['datasets'][0]['data']) . "\n";
            } else {
                echo "No chart data returned.\n";
            }
        } else {
            echo "Controller did not return a view object.\n";
        }
    } catch (Exception $e) {
        echo "Exception: " . $e->getMessage() . "\n";
    }
    echo "\n";
}

echo "Done.\n";
