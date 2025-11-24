<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Stock\Stock;

class TestDividendRelation extends Command
{
    protected $signature = 'test:dividendrelation';

    protected $description = 'Test relation between Stock and Dividend and print dividend amount and frequency for each stock';

    public function handle()
    {
        $stocks = Stock::with('dividends')->get();

        foreach ($stocks as $stock) {
            $latestDividend = $stock->dividends()->orderByDesc('game_time_id')->first();
            $dividendAmount = $latestDividend ? $latestDividend->amount_per_share : 'No dividend';
            $this->info("Stock ID: {$stock->id}, Name: {$stock->name}");
            $this->info("Dividend Frequency: {$stock->dividend_frequency}");
            $this->info("Latest Dividend Amount: {$dividendAmount}");
            $this->info('-----------------------------');
        }

        return 0;
    }
}
