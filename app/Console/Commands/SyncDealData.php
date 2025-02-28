<?php

namespace App\Console\Commands;

use App\Models\Handler;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SyncDealData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:dealData';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize local database Deals with the remote database Deals.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->syncDealData();
    }
    private function syncDealData()
    {
        try {
            $this->info("Starting Deals data synchronization...");

            Handler::with(['deal', 'product'])->get()->groupBy('deal_id')->each(function ($handlers, $dealId) {
                try {
                    $deal = $handlers->first()->deal;
                    $products = $handlers->map(function ($handler) {
                        return [
                            'product_id' => $handler->product->id,
                            'product_name' => $handler->product->productName,
                            'product_variation' => $handler->product->productVariation,
                            'product_price' => $handler->product->productPrice,
                            'product_quantity' => $handler->product_quantity,
                            'product_total_price' => $handler->product_total_price,
                        ];
                    });

                    $response = Http::timeout(180)->post('https://crusthouse.com.pk/api/sync-dealsWithDealsItems-record', [
                        'deal' => [
                            'deal_id' => $deal->id,
                            'deal_image' => $deal->dealImage,
                            'deal_title' => $deal->dealTitle,
                            'deal_status' => $deal->dealStatus,
                            'deal_discounted_price' => $deal->dealDiscountedPrice,
                            'deal_actual_price' => $deal->dealActualPrice,
                            'deal_IsForever' => $deal->IsForever,
                            'deal_end_date' => $deal->dealEndDate,
                            'branch_id' => $deal->branch_id,
                            'products' => $products,
                        ]
                    ]);

                    if ($response->successful()) {
                        $this->info("Successfully synced deal: " . $deal->dealTitle);
                    } else {
                        $this->error("Failed to sync deal: " . $deal->dealTitle . " Response: " . $response->body());
                    }
                } catch (\Exception $e) {
                    $this->error("Error syncing deal: " . $e->getMessage());
                }
            });

            $this->info("Data synchronization completed.");
        } catch (\Exception $e) {
            $this->error("Unexpected error: " . $e->getMessage());
        }
    }
}
