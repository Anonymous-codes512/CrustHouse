<?php

namespace App\Console\Commands;

use App\Models\DineInTable;
use App\Models\Discount;
use App\Models\PaymentMethod;
use App\Models\Tax;
use App\Models\ThemeSetting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SyncOtherData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:otherData';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize local database Other table like(dine-in-table, discount, tax, etc.) with the remote database Other table.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->syncOtherData();
    }
    private function syncOtherData(){
        $dineInTables = DineInTable::all();
        $discounts = Discount::all();
        $tax = Tax::all();
        $themeSettings = ThemeSetting::all();
        $paymentMethods = PaymentMethod::all();

        try {
            $response = Http::timeout(180)->post('https://crusthouse.com.pk/api/sync-other-record', [
                'DineInTable' => $dineInTables,
                'Discount' => $discounts,
                'Tax' => $tax,
                'ThemeSetting' => $themeSettings,
                'PaymentMethod' => $paymentMethods
            ]);

            if ($response->successful()) {
                $this->info("Successfully synced batch for DineInTable, Discount, Tax and ThemeSetting.");
            } else {
                $this->error("Failed to sync batch for DineInTable, Discount, Tax and ThemeSetting. Response: " . $response->body());
            }
        } catch (\Exception $e) {
            $this->error("Error syncing batch for DineInTable, Discount, Tax and ThemeSetting: " . $e->getMessage());
        }

    }
}
