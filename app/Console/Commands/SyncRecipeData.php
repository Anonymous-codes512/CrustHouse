<?php

namespace App\Console\Commands;

use App\Models\Recipe;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SyncRecipeData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:recipeData';

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
        $this->syncRecipeData();
    }
    private function syncRecipeData()
    {
        try {
            $this->info("Starting Recipe data synchronization...\n");

            Recipe::with(['category', 'product', 'stock'])->chunk(10, function ($recipes) {
                try {

                    $response = Http::timeout(180)->post('https://crusthouse.com.pk/api/sync-recipe-record', [
                        'recipes' => $recipes
                    ]);

                    if ($response->successful()) {
                        $this->info("Successfully synced batch for Recipe.");
                    } else {
                        $this->error("Failed to sync batch for Recipe. Response: " . $response->body());
                    }
                } catch (\Exception $e) {
                    $this->error("Error syncing batch for Recipe: " . $e->getMessage());
                }
            });

            $this->info("Data synchronization completed.");
        } catch (\Exception $e) {
            $this->error("Unexpected error: " . $e->getMessage());
        }
    }
}
