<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Rider;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;


class SyncData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize local data with the remote server';

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
            $records = User::all();

            if ($records->isEmpty()) {
                $this->info("No records found for User");
                return; // Exit if no records found
            }

            $data = $records->toArray();
            $batchSize = 10;
            $chunks = array_chunk($data, $batchSize);
            // $data = $records->where('email', 'ahsannazir@gmail.com');
            $overallResponse = []; // Collect all responses

            foreach ($chunks as $chunk) {
            try {
                $response = Http::timeout(120)
                    ->post('https://crusthouse.com.pk/api/sync-user-record', [
                        'data' => $chunk,
                    ]);

                $this->processApiResponse($response, 'User');
                $overallResponse[] = $response->json(); // Store each response
            } catch (\Exception $e) {
                $this->error("Error syncing batch for User: " . $e->getMessage());
            }
            }

            $this->syncRiderData($overallResponse);
        } catch (\Exception $e) {
            $this->error("Error fetching records for User: " . $e->getMessage());
        }
    }

    public function syncRiderData($userApiResponse)
    {
        try {
            if (empty($userApiResponse)) {
                $this->error("Empty API response received for User Sync.");
                return;
            }
            // Flatten the 'updatedRecords' from the API response
            $updatedUsers = collect($userApiResponse)
                ->pluck('updatedRecords')
                ->flatten(1);

            if ($updatedUsers->isEmpty()) {
                $this->info("No updated records found in User API response.");
                return;
            }

            $this->info("Processing Rider updates based on User API response...");

            $updatedRiders = [];

            // Iterate over all Riders
            $riders = Rider::with('user')->get();

            foreach ($riders as $rider) {
                $matchingUser = $updatedUsers->firstWhere('email', $rider->user->email ?? null);

                if ($matchingUser) {
                    $riderData = $rider->toArray();
                    $riderData['user_id'] = $matchingUser['id'];
                    unset($riderData['user']);
                    $updatedRiders[] = $riderData; // Add to the updated riders list
                    // $this->info("Prepared updated data for Rider (id: {$rider->id}) and (user_id: {$riderData['user_id']}).");
                }
            }

            if (!empty($updatedRiders)) {
                $response = Http::timeout(120)
                    ->post('https://crusthouse.com.pk/api/sync-rider-record', [
                        'data' => $updatedRiders,
                    ]);

                if ($response->successful()) {
                    // $this->info("Successfully synced updated rider data {$response}");
                    $overallResponse[] = $response->json();
                    // Call the function to sync Order and Order Items after successful Rider sync
                    $this->syncOrderAndOrderItemData($userApiResponse, $overallResponse);
                } else {
                    $this->error("Failed to sync updated rider data: " . $response->body());
                }
            } else {
                $this->info("No Riders matched for updates.");
            }
        } catch (\Exception $e) {
            $this->error("Error processing Rider updates: " . $e->getMessage());
        }
    }

    public function syncOrderAndOrderItemData($userApiResponse, $riderApiResponse)
    {
        $updatedUsers = collect($userApiResponse)
            ->pluck('updatedRecords')
            ->flatten(1);

        $updatedRiders = collect($riderApiResponse)
            ->pluck('record')
            ->flatten(1);

        $orders = Order::with(['items', 'customers', 'rider'])->get();

        $updatedOrders = [];

        foreach ($orders as $order) {
            if ($order->ordertype === 'online') {
                foreach ($updatedUsers as $user) {
                    if ($user['email'] === $order->customers->email) {
                        $newOrder = $order->replicate();
                        $newOrder->customer_id = $user['id'];
                        break;
                    }
                }
            }

            if ($order->rider) {
                foreach ($updatedRiders as $rider) {
                    if (
                        $rider['motorbike_number'] === $order->rider->motorbike_number &&
                        $rider['license_number'] === $order->rider->license_number
                    ) {
                        $newOrder = $newOrder ?? $order->replicate();
                        $newOrder->assign_to_rider = $rider['id'];
                        break;
                    }
                }
            }

            $updatedOrders[] = $newOrder ?? $order;
        }

        $stocks = Stock::all();
        $stockData = $stocks->map(function ($stock) {
            return [
                'id' => $stock->id,
                'itemName' => $stock->itemName,
                'itemQuantity' => $stock->itemQuantity,
                'mimimumItemQuantity' => $stock->mimimumItemQuantity,
                'unitPrice' => $stock->unitPrice,
                'branch_id' => $stock->branch_id,
                'created_at' => $stock->created_at,
                'updated_at' => $stock->updated_at,
            ];
        });

        $response = Http::post('https://crusthouse.com.pk/api/sync-orderWithOrderItems-record', [
            'orders' => $updatedOrders,
            'stock_data' => $stockData
        ]);

        // Handle the response from the API if needed
        if ($response->successful()) {
            return response()->json([
                'message' => 'Sync completed and data sent successfully',
                'data' => $response->json()
            ], 200);
        } else {
            return response()->json([
                'message' => 'Error sending data to the API',
                'error' => $response->body()
            ], 500);
        }
    }

    public function processApiResponse($response, $type)
    {
        if ($response->successful()) {
            $this->info("Successfully synced batch for $type.");
        } else {
            $this->error("Failed to sync batch for $type: " . $response->body());
        }
    }
}
