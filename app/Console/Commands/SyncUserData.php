<?php

namespace App\Console\Commands;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SyncUserData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:userData';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize local database User table with the remote database User table.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->syncUserData();
    }

    public function syncUserData()
    {
        try {
            $this->info("Starting Branch data synchronization...");

            Branch::chunk(10, function ($branches) {
                try {
                    $response = Http::timeout(180)->post('https://crusthouse.com.pk/api/sync-branch-record', [
                        'branch' => $branches
                    ]);

                    if ($response->successful()) {
                        $this->info("Successfully synced batch for Branch.");
                    } else {
                        $this->error("Failed to sync batch for Branch. Response: " . $response->body());
                    }
                } catch (\Exception $e) {
                    $this->error("Error syncing batch for Branch: " . $e->getMessage());
                }
            });

            $this->warn("\nPreparing for User data synchronization...");

            User::chunk(10, function ($users) {
                // $users = [
                //     "profile_picture" => null,
                //     "name" => "Ejaz Khan",
                //     "email" => "ejazKhan@gmail.com",
                //     "phone_number" => "+923015170229",
                //     "role" => null,
                //     "branch_id" => 1
                // ];

                try {
                    $response = Http::timeout(180)->post('https://crusthouse.com.pk/api/sync-user-record', [
                        'data' => $users
                    ]);

                    if ($response->successful()) {
                        $this->info("Successfully synced batch for User.");
                    } else {
                        $this->error("Failed to sync batch for User. Response: " . $response->body());
                    }
                } catch (\Exception $e) {
                    $this->error("Error syncing batch for User: " . $e->getMessage());
                }
            });

            $this->info("Data synchronization completed.");
        } catch (\Exception $e) {
            $this->error("Unexpected error: " . $e->getMessage());
        }
    }
}
