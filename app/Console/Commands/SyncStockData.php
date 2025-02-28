<?php

namespace App\Console\Commands;

use App\Models\Stock;
use App\Models\StockHistory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SyncStockData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:stockData';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize local database Stocks table with the remote database Stocks table.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->syncStockData();
    }

    private function syncStockData()
    {
        try {
            $this->info("Starting Stock data synchronization...\n");

            $stockHistories = StockHistory::all();
            $stocks = Stock::all();
            try {
                $response = Http::timeout(180)->post('https://crusthouse.com.pk/api/sync-stock-record', [
                    'Stock History' => $stockHistories,
                    'Stock' => $stocks
                ]);

                if ($response->successful()) {
                    $this->info("Successfully synced batch for Stock and StockHistory.");
                } else {
                    $this->error("Failed to sync batch for Stock and StockHistory. Response: " . $response->body());
                }
            } catch (\Exception $e) {
                $this->error("Error syncing batch for Stock and StockHistory: " . $e->getMessage());
            }

            $this->info("Data synchronization completed.");
        } catch (\Exception $e) {
            $this->error("Unexpected error: " . $e->getMessage());
        }
    }
}
