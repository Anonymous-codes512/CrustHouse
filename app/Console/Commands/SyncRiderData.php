<?php

namespace App\Console\Commands;

use App\Models\Rider;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SyncRiderData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:riderData';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize local database Rider table with the remote database Rider table.';


    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->syncRiderData();
    }

    private function syncRiderData(){
        try {
            $this->info("Starting Rider data synchronization...\n");

            Rider::with('user')->chunk(10, function ($riders) {
                try {

                    $response = Http::timeout(180)->post('https://crusthouse.com.pk/api/sync-riders', [
                        'riders' => $riders
                    ]);

                    if ($response->successful()) {
                        $this->info("Successfully synced batch for Rider.");
                    } else {
                        $this->error("Failed to sync batch for Rider. Response: " . $response->body());
                    }
                } catch (\Exception $e) {
                    $this->error("Error syncing batch for Rider: " . $e->getMessage());
                }
            });

            $this->info("Data synchronization completed.");
        } catch (\Exception $e) {
            $this->error("Unexpected error: " . $e->getMessage());
        }
    }
}
