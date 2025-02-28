<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SyncProductData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:productData';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize local database Product table with the remote database Product table.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->syncProducts();
    }

    private function syncProducts()
    {
        try {
            $this->info("Starting Product data synchronization...");

            // $products = Product::with('category')->where('productName', 'Zinger Burger')->get();
            Product::with('category')->chunk(10, function ($products) {
            try {
                $response = Http::timeout(180)->post('https://crusthouse.com.pk/api/sync-product-record', [
                    'products' => $products
                ]);

                if ($response->successful()) {
                    $this->info("Successfully synced batch for Product.");
                } else {
                    $this->error("Failed to sync batch for Product. Response: " . $response->body());
                }
            } catch (\Exception $e) {
                $this->error("Error syncing batch for Product: " . $e->getMessage());
            }
            });

            $this->info("Data synchronization completed.");
        } catch (\Exception $e) {
            $this->error("Unexpected error: " . $e->getMessage());
        }
    }
}
