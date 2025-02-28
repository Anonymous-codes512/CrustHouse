<?php

namespace App\Console\Commands;

use App\Models\BranchCategory;
use App\Models\Category;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SyncCategoryData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:categoryData';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize local database Category table with the remote database Category table.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->syncCategoryData();
    }

    private function syncCategoryData()
    {
        try {
            $this->info("Starting Category data synchronization...");

            BranchCategory::with('categories')->chunk(10, function ($branchCategories) {
                try {
                    $response = Http::timeout(180)->post('https://crusthouse.com.pk/api/sync-category-record', [
                        'branchCategories' => $branchCategories
                    ]);

                    if ($response->successful()) {
                        $this->info("Successfully synced batch for Category.");
                    } else {
                        $this->error("Failed to sync batch for Category. Response: " . $response->body());
                    }
                } catch (\Exception $e) {
                    $this->error("Error syncing batch for Category: " . $e->getMessage());
                }
            });

            $this->info("Data synchronization completed.");
        } catch (\Exception $e) {
            $this->error("Unexpected error: " . $e->getMessage());
        }
    }
}
