<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

use App\Models\Stock\Stock;
use App\Services\DividendeService;

class ProcessDividendPayout implements ShouldQueue
{
    use Queueable;

    protected $stockId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $stockId)
    {
        $this->stockId = $stockId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $stock = Stock::find($this->stockId);
        if (!$stock) {
            Log::error("ProcessDividendPayout: Stock with ID {$this->stockId} not found");
            return;
        }

        Log::info("Processing dividend payout for stock: {$stock->name} (ID: {$stock->id})");

        $dividendeService = new DividendeService();
        $dividendeService->shareDividendeToUsers($stock);

        Log::info("Dividend payout job completed for stock: {$stock->name}");
    }
}
