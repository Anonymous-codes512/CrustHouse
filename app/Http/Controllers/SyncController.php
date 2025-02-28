<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Rider;
use App\Models\Order;
use App\Models\Stock;
use App\Models\StockHistory;
use App\Models\Branch;
use App\Models\BranchCategory;
use App\Models\Category;
use App\Models\Product;
use App\Models\Recipe;
use App\Models\DineInTable;
use App\Models\Discount;
use App\Models\Tax;
use App\Models\ThemeSetting;
use App\Models\PaymentMethod;
use App\Models\OrderItem;
use App\Models\Deal;
use App\Models\Handler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class SyncController extends Controller
{
    public function syncUserRecord(Request $request)
    {
        $data = $request->input('data');
    
        $updatedRecords = [];
        Log::info("Syncing model:User");
        
        foreach ($data as $record) {
            try {
                Log::info("Processing record: " . json_encode($record));
                if (isset($record['password'])) {
                    if (!Hash::needsRehash($record['password'])) {
                        Log::info("Password already hashed for: " . $record['email']);
                    } else {
                        $record['password'] = Hash::make($record['password']);
                        Log::info("Password hashed for: " . $record['email']);
                    }
                } else {
                    $record['password'] = Hash::make('12345678');
                    Log::info("Default password set for: " . $record['email']);
                }
    
                $model = User::updateOrCreate(
                    ['email' => $record['email']],
                    $record
                );
        
                Log::info("Processed record with email: " . $record['email']);
                $updatedRecords[] = $model; 
            } catch (\Exception $e) {
                Log::error("Error processing record: " . json_encode($record) . ". Error: " . $e->getMessage());
            }
        }
        
        return response()->json([
            'message' => 'Sync completed successfully',
            'updatedRecords' => $updatedRecords,
        ], 200);
    }
    public function syncRiderRecord(Request $request)
    {
        $riderData = $request->input('data', []);
        $updatedRiders = [];
    
        foreach ($riderData as $rider) {
            unset($rider['id'], $rider['created_at'], $rider['updated_at']);
    
            $existingRider = Rider::where('motorbike_number', $rider['motorbike_number'])->Where('license_number', $rider['license_number'])->first();
            if ($existingRider) {
                $existingRider->user_id = $rider['user_id'];
                $existingRider->motorbike_number = $rider['motorbike_number'];
                $existingRider->license_number = $rider['license_number'];
                $existingRider->status = $rider['status'];
                $existingRider->save();
    
                $updatedRiders[] = $existingRider->toArray();
            } else {
                $newRider = Rider::create([
                    'user_id' => $rider['user_id'],
                    'motorbike_number' => $rider['motorbike_number'],
                    'license_number' => $rider['license_number'],
                    'status' => $rider['status'],
                ]);
    
                $updatedRiders[] = $newRider->toArray();
            }
        }
    
        return response()->json([
            'message' => 'Sync completed successfully',
            'record' => $updatedRiders,
        ], 200);
    }
    public function syncOrderWithOrderItems(Request $request)
    {
        // Fetch incoming orders and their items from the request body
        $incomingOrders = $request->input('orders'); // Assuming 'orders' is the key
        $incomingStockData = $request->input('stock_data');
    
        // Get all existing orders with their associated items
        $existingOrders = Order::with('items')->get();
        $existingStocks = Stock::all();
    
        // Start a transaction to handle database insertions
        DB::beginTransaction();
    
        try {
            foreach ($incomingOrders as $incomingOrder) {
                $orderExists = false;
    
                // Compare each incoming order with existing orders
                foreach ($existingOrders as $existingOrder) {
                    // Check if all fields match except for the excluded ones (id, order_number, created_at, updated_at)
                    if (
                        $incomingOrder['customer_id'] == $existingOrder->customer_id &&
                        $incomingOrder['assign_to_rider'] == $existingOrder->assign_to_rider &&
                        $incomingOrder['salesman_id'] == $existingOrder->salesman_id &&
                        $incomingOrder['branch_id'] == $existingOrder->branch_id &&
                        $incomingOrder['table_id'] == $existingOrder->table_id &&
                        $incomingOrder['total_bill'] == $existingOrder->total_bill &&
                        $incomingOrder['taxes'] == $existingOrder->taxes &&
                        $incomingOrder['delivery_charge'] == $existingOrder->delivery_charge &&
                        $incomingOrder['discount'] == $existingOrder->discount &&
                        $incomingOrder['discount_reason'] == $existingOrder->discount_reason &&
                        $incomingOrder['discount_type'] == $existingOrder->discount_type &&
                        $incomingOrder['payment_method'] == $existingOrder->payment_method &&
                        $incomingOrder['received_cash'] == $existingOrder->received_cash &&
                        $incomingOrder['return_change'] == $existingOrder->return_change &&
                        $incomingOrder['ordertype'] == $existingOrder->ordertype &&
                        $incomingOrder['order_address'] == $existingOrder->order_address &&
                        $incomingOrder['status'] == $existingOrder->status &&
                        $incomingOrder['delivery_status'] == $existingOrder->delivery_status &&
                        $incomingOrder['order_cancel_by'] == $existingOrder->order_cancel_by &&
                        $incomingOrder['cancellation_reason'] == $existingOrder->cancellation_reason &&
                        // Compare order items using a helper function
                        $this->compareOrderItems($incomingOrder['items'], $existingOrder->items)
                    ) {
                        $orderExists = true;
                        break; // No need to insert a new order, exit the loop
                    }
                }
    
                // If no match is found, insert the new order with a new order number
                if (!$orderExists) {
                    // Get the last order number and increment it
                    $lastOrder = Order::orderBy('id', 'desc')->first();
                    $lastOrderNumber = $lastOrder ? $lastOrder->order_number : ($incomingOrder['ordertype'] === 'online' ? 'OL-ORD-000' : 'CH-000');
                    $newOrderNumber = $this->generateNewOrderNumber($lastOrderNumber, $incomingOrder['ordertype']);

    
                    // Create the new order
                    $newOrder = new Order();
                    $newOrder->order_number = $newOrderNumber;
                    $newOrder->customer_id = $incomingOrder['customer_id'];
                    $newOrder->assign_to_rider = $incomingOrder['assign_to_rider'];
                    $newOrder->salesman_id = $incomingOrder['salesman_id'];
                    $newOrder->branch_id = $incomingOrder['branch_id'];
                    $newOrder->table_id = $incomingOrder['table_id'];
                    $newOrder->total_bill = $incomingOrder['total_bill'];
                    $newOrder->taxes = $incomingOrder['taxes'];
                    $newOrder->delivery_charge = $incomingOrder['delivery_charge'];
                    $newOrder->discount = $incomingOrder['discount'];
                    $newOrder->discount_reason = $incomingOrder['discount_reason'];
                    $newOrder->discount_type = $incomingOrder['discount_type'];
                    $newOrder->payment_method = $incomingOrder['payment_method'];
                    $newOrder->received_cash = $incomingOrder['received_cash'];
                    $newOrder->return_change = $incomingOrder['return_change'];
                    $newOrder->ordertype = $incomingOrder['ordertype'];
                    $newOrder->order_address = $incomingOrder['order_address'];
                    $newOrder->status = $incomingOrder['status'];
                    $newOrder->delivery_status = $incomingOrder['delivery_status'];
                    $newOrder->order_cancel_by = $incomingOrder['order_cancel_by'];
                    $newOrder->cancellation_reason = $incomingOrder['cancellation_reason'];
                    $newOrder->save();
    
                    // Insert the associated order items
                    foreach ($incomingOrder['items'] as $item) {
                        $newOrderItem = new OrderItem();
                        $newOrderItem->order_id = $newOrder->id;
                        $newOrderItem->order_number = $newOrder->order_number;
                        $newOrderItem->product_id = $item['product_id'];
                        $newOrderItem->product_name = $item['product_name'];
                        $newOrderItem->product_variation = $item['product_variation'];
                        $newOrderItem->addons = $item['addons'];
                        $newOrderItem->product_price = $item['product_price'];
                        $newOrderItem->product_quantity = $item['product_quantity'];
                        $newOrderItem->total_price = $item['total_price'];
                        $newOrderItem->save();
                    }
                }
            }

            foreach ($incomingStockData as $incomingStock) {
                $existingStock = Stock::where('itemName', $incomingStock['itemName'])->where('unitPrice', $incomingStock['unitPrice'])->where('branch_id', $incomingStock['branch_id'])->first();
                if ($existingStock) {
                    $existingStock->itemQuantity = $incomingStock['itemQuantity'];
                    $existingStock->mimimumItemQuantity = $incomingStock['mimimumItemQuantity'];
                    $existingStock->save();
                } else {
                    $newStock = new Stock();
                    $newStock->itemName = $incomingStock['itemName'];
                    $newStock->itemQuantity = $incomingStock['itemQuantity'];
                    $newStock->mimimumItemQuantity = $incomingStock['mimimumItemQuantity'];
                    $newStock->unitPrice = $incomingStock['unitPrice'];
                    $newStock->branch_id = $incomingStock['branch_id'];
                    $newStock->save();
                }
            }
            
            // Commit the transaction
            DB::commit();
    
            return response()->json([
                'message' => 'Sync completed successfully',
            ], 200);
    
        } catch (\Exception $e) {
            // Rollback the transaction in case of an error
            DB::rollBack();
            return response()->json([
                'message' => 'Error occurred during sync',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    private function compareOrderItems($incomingItems, $existingItems)
    {
        if (count($incomingItems) !== count($existingItems)) {
            return false;
        }
    
        foreach ($incomingItems as $incomingItem) {
            $found = false;
            foreach ($existingItems as $existingItem) {
                if (
                    $incomingItem['product_id'] == $existingItem->product_id &&
                    $incomingItem['product_quantity'] == $existingItem->product_quantity
                ) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                return false;
            }
        }
    
        return true;
    }
    public function syncBranchRecord(Request $request)
    {
        $branchesData = $request->input('branch');
        try {
            foreach ($branchesData as $branchData) {

                Branch::updateOrCreate(['branch_code' => $branchData['branch_code']], $branchData);
            }
    
            return response()->json([
                'success' => true,
                'message' => 'Branches synchronized successfully.'
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error syncing branches: ' . $e->getMessage()
            ], 500);
        }
    }
    private function generateNewOrderNumber($lastOrderNumber, $orderType)
    {
        if ($orderType === 'online') {
            preg_match('/OL-ORD-(\d+)/', $lastOrderNumber, $matches);
            $lastNumber = isset($matches[1]) ? (int)$matches[1] : 0;
            $newNumber = $lastNumber + 1;
            return 'OL-ORD-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
        } else {
            preg_match('/CH_(\d+)/', $lastOrderNumber, $matches);
            $lastNumber = isset($matches[1]) ? (int)$matches[1] : 0;
            $newNumber = $lastNumber + 1;
            return 'CH-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
        }
    }
    public function syncCategoryRecord(Request $request)
    {
        try {
            $branchCategories = $request->input('branchCategories');
    
            if (empty($branchCategories)) {
                return response()->json(['message' => 'No categories provided'], 400);
            }
    
            foreach ($branchCategories as $branchcategory) {
                $categoryData = $branchcategory['categories'];
    
                // Check if category exists
                $existingCategory = Category::where('categoryName', $categoryData['categoryName'])
                    ->where('branch_id', $categoryData['branch_id'])
                    ->first();
    
                if (!$existingCategory) {
                    $newCategory = Category::create([
                        'categoryImage' => $categoryData['categoryImage'] ?? null,
                        'categoryName'  => $categoryData['categoryName'],
                        'branch_id'     => $categoryData['branch_id'],
                    ]);
    
                    BranchCategory::create([
                        'category_id' => $newCategory->id,
                        'branch_id'   => $newCategory->branch_id,
                    ]);
                }
            }
    
            return response()->json([
                'message' => 'Categories synced successfully',
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error syncing categories',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
    public function syncProductRecord(Request $request) {
        try {
            $products = $request->input('products');
    
            if (empty($products)) {
                return response()->json(['message' => 'No products provided'], 400);
            }
    
            foreach ($products as $product) {
                $productCategory = $product['category'];
    
                $existingProduct = Product::where('productName', $product['productName'])
                    ->where('productVariation', $product['productVariation'])
                    ->where('productPrice', $product['productPrice'])
                    ->where('branch_id', $product['branch_id'])
                    ->with('category')
                    ->first();
    
                if ($existingProduct) {
                    if ($existingProduct->category->categoryName !== $productCategory['categoryName']) {
                        $newCategory = Category::where('categoryName', $productCategory['categoryName'])
                            ->where('branch_id', $product['branch_id'])
                            ->first();
    
                        if ($newCategory) {
                            $existingProduct->update([
                                'category_id'   => $newCategory->id,
                                'category_name' => $newCategory->categoryName,
                            ]);
                        }

                    }
                } else {
                    $newCategory = Category::where('categoryName', $productCategory['categoryName'])
                        ->where('branch_id', $product['branch_id'])
                        ->first();
                        
                    if ($newCategory) {
                        Product::create([
                            'productImage'     => $product['productImage'],
                            'productName'     => $product['productName'],
                            'productVariation'=> $product['productVariation'],
                            'productPrice'    => $product['productPrice'],
                            'branch_id'       => $product['branch_id'],
                            'category_id'     => $newCategory->id,
                            'category_name'     => $newCategory->categoryName,
                        ]);
                    }
                }
            }
    
            return response()->json([
                'message' => 'Products synced successfully',
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error syncing products',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
    public function syncDealsWithDealsItemsRecord(Request $request)
    {
        DB::beginTransaction();
    
        try {
            $incomingDeal = $request->input('deal');
    
            if (empty($incomingDeal)) {
                return response()->json(['message' => 'No deal data provided'], 400);
            }
    
            // foreach ($incomingDeals as $incomingDeal) {
                $existingDeal = Deal::where('dealTitle', $incomingDeal['deal_title'])->first();
    
                if (!$existingDeal) {
                    // Create new deal if not found
                    $newDeal = Deal::create([
                        'dealImage' => $incomingDeal['deal_image'],
                        'dealTitle' => $incomingDeal['deal_title'],
                        'dealStatus' => $incomingDeal['deal_status'],
                        'dealDiscountedPrice' => $incomingDeal['deal_discounted_price'],
                        'dealActualPrice' => $incomingDeal['deal_actual_price'],
                        'dealEndDate' => $incomingDeal['deal_end_date'],
                        'IsForever' => $incomingDeal['deal_IsForever'],
                        'branch_id' => $incomingDeal['branch_id'],
                    ]);
                    $dealId = $newDeal->id;
                } else {
                    $dealId = $existingDeal->id;
                }
    
                // Fetch existing product IDs for this deal (if any)
                $existingProductIds = Handler::where('deal_id', $dealId)->pluck('product_id')->toArray();
    
                // Prepare products to be inserted
                $handlersToInsert = [];
                $handlersToUpdate = [];
    
                foreach ($incomingDeal['products'] as $productData) {
                    $product = Product::where('productName', $productData['product_name'])
                                      ->where('productVariation', $productData['product_variation'])
                                      ->where('branch_id', $incomingDeal['branch_id'])
                                      ->first();
    
                    if (!$product) {
                        return response()->json([
                            'message' => 'Product not found for name: ' . $productData['product_name'] . ' and variation: ' . $productData['product_variation']
                        ], 404);
                    }
    
                    // Check if the product is already associated with the current deal
                    if (!in_array($product->id, $existingProductIds)) {
                        // Add product to insert batch if not already linked to deal
                        $handlersToInsert[] = [
                            'deal_id' => $dealId,
                            'product_id' => $product->id,
                            'product_quantity' => $productData['product_quantity'],
                            'product_total_price' => $productData['product_total_price'],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    } else {
                        // Add product to update batch
                        $handlersToUpdate[] = [
                            'deal_id' => $dealId,
                            'product_id' => $product->id,
                            'product_quantity' => $productData['product_quantity'],
                            'product_total_price' => $productData['product_total_price'],
                        ];
                    }
                }
    
                // Insert all new handler records in one query
                if (!empty($handlersToInsert)) {
                    Handler::insert($handlersToInsert);
                }
    
                // Update existing handler records in one query (bulk update)
                foreach ($handlersToUpdate as $updateData) {
                    Handler::where('deal_id', $updateData['deal_id'])
                           ->where('product_id', $updateData['product_id'])
                           ->update([
                               'product_quantity' => $updateData['product_quantity'],
                               'product_total_price' => $updateData['product_total_price'],
                           ]);
                }
            // }
    
            DB::commit();
    
            return response()->json([
                'message' => 'Deal and product data synced successfully',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error syncing deal and product data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function syncRecipeRecord(Request $request)
    {
        try {
            $recipes = $request->input('recipes');
    
            if (empty($recipes)) {
                Log::error('No recipes provided', ['request' => $request->all()]);
                return response()->json(['message' => 'No recipes provided'], 400);
            }
    
            foreach ($recipes as $recipe) {
                // Check category existence
                $incomingCategory = $recipe['category']; // Assuming there's only one category per recipe
                $category = Category::where('categoryName', $incomingCategory['categoryName'])
                    ->where('branch_id', $incomingCategory['branch_id'])->first();
                if (!$category) {
                    Log::error('Category not found', [
                        'category' => $incomingCategory['categoryName'],
                        'branch_id' => $incomingCategory['branch_id'],
                        'request' => $request->all()
                    ]);
                    return response()->json(['message' => 'Category not found: ' . $incomingCategory['categoryName']], 404);
                }
    
                // Check product existence
                $incomingProduct = $recipe['product']; // Assuming there's only one product per recipe
                $product = Product::where('productName', $incomingProduct['productName'])
                    ->where('productVariation', $incomingProduct['productVariation'])
                    ->where('branch_id', $incomingProduct['branch_id'])->first();
                if (!$product) {
                    Log::error('Product not found', [
                        'product_name' => $incomingProduct['productName'],
                        'branch_id' => $incomingProduct['branch_id'],
                        'request' => $request->all()
                    ]);
                    return response()->json(['message' => 'Product not found: ' . $incomingProduct['productName']], 404);
                }
    
                // Check stock existence
                $incomingStock = $recipe['stock']; // Assuming there's only one stock per recipe
                $stock = Stock::where('itemName', $incomingStock['itemName'])
                    ->where('branch_id', $incomingStock['branch_id'])->first();
                if (!$stock) {
                    Log::error('Stock not found', [
                        'item_name' => $incomingStock['itemName'],
                        'branch_id' => $incomingStock['branch_id'],
                        'request' => $request->all()
                    ]);
                    return response()->json(['message' => 'Stock not found: ' . $incomingStock['itemName']], 404);
                }
    
                // Now check if the combination of category_id, product_id, and stock_id exists in the recipes table
                $existingRecipe = Recipe::where('category_id', $recipe['category_id'])
                    ->where('product_id', $recipe['product_id'])
                    ->where('stock_id', $recipe['stock_id'])->first();
    
                if ($existingRecipe) {
                    // Update existing recipe
                    $existingRecipe->update([
                        'quantity' => $recipe['quantity'],
                    ]);
                } else {
                    // Insert new recipe
                    Recipe::create([
                        'category_id' => $category->id,
                        'product_id' => $product->id,
                        'stock_id' => $stock->id,
                        'quantity' => $recipe['quantity'],
                    ]);
                }
            }
    
            return response()->json(['message' => 'Recipes processed successfully.'], 200);
    
        } catch (\Exception $e) {
            Log::error('Error syncing recipe records', [
                'error' => $e->getMessage(),
                'stack' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
    public function syncStockRecord(Request $request)
    {
        $incomingStocksHistory = $request->input('Stock History');
        $incomingStocks = $request->input('Stock');
    
        try {
            // Process Stocks
            foreach ($incomingStocks as $incomingStock) {
                $existingStock = Stock::where('itemName', $incomingStock['itemName'])->where('branch_id', $incomingStock['branch_id'])->first();
    
                if ($existingStock) {
                    // Update existing stock
                    $existingStock->itemQuantity = $incomingStock['itemQuantity'];
                    $existingStock->mimimumItemQuantity = $incomingStock['mimimumItemQuantity'];
                    $existingStock->save();
                } else {
                    // Create new stock record
                    $newStock = new Stock();
                    $newStock->itemName = $incomingStock['itemName'];
                    $newStock->itemQuantity = $incomingStock['itemQuantity'];
                    $newStock->mimimumItemQuantity = $incomingStock['mimimumItemQuantity'];
                    $newStock->unitPrice = $incomingStock['unitPrice'];
                    $newStock->branch_id = $incomingStock['branch_id'];
                    $newStock->save();
                }
            }
    
            // Process StockHistory (create new record and differentiate by formatted created_at date)
            foreach ($incomingStocksHistory as $incomingStockHistory) {
                // Format the current date like "August 05, 2024"
                $formattedDate = now()->format('F d, Y'); // Example: "August 05, 2024"
    
                // Check if StockHistory exists with same itemName, unitPrice, branch_id, and formatted created_at date
                $existingStockHistory = StockHistory::where('itemName', $incomingStockHistory['itemName'])->where('branch_id', $incomingStockHistory['branch_id'])->whereRaw('DATE_FORMAT(created_at, "%M %d, %Y") = ?', [$formattedDate])->first();
    
                if (!$existingStockHistory) {
                    // If no entry exists for that formatted date, create a new StockHistory record
                    $newStockHistory = new StockHistory();
                    $newStockHistory->itemName = $incomingStockHistory['itemName'];
                    $newStockHistory->itemQuantity = $incomingStockHistory['itemQuantity'];
                    $newStockHistory->mimimumItemQuantity = $incomingStockHistory['mimimumItemQuantity'];
                    $newStockHistory->unitPrice = $incomingStockHistory['unitPrice'];
                    $newStockHistory->branch_id = $incomingStockHistory['branch_id'];
                    $newStockHistory->save();
                }
            }
        } catch (\Exception $e) {
            Log::error("Error: " . $e->getMessage());
        }
    }
    public function syncRidersRecord(Request $request)
    {
        try {
            $riders = $request->input('riders');
            if (empty($riders)) {
                return response()->json(['message' => 'No riders to sync'], 400);
            }
    
            foreach ($riders as $incomingRider) {
                $user = $incomingRider['user'];
       
                $userEmail  = $user['email'];
    
                if (!$userEmail) {
                    continue;
                }
    
                $existingUser = User::where('email', $userEmail)->first();
    
                if ($existingUser) {
                    $existingRider = Rider::where('user_id', $existingUser->id)->where('license_number', $incomingRider['license_number'])->where('motorbike_number', $incomingRider['motorbike_number'])->first();
			

                    if ($existingRider) {
                        $existingRider->update([
                            'status' => $incomingRider['status'] ?? $existingRider->status,
                        ]);
                    }
                }else{
                     return response()->json(['error' => 'Rider data synced failed, User not found.'], 400);
                }
            }
            return response()->json(['message' => 'Rider data synced successfully.'], 200);
    
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to sync riders: ' . $e->getMessage()], 500);
        }
    }
    public function syncOtherRecord(Request $request){
        try{
            $dineInTables = $request->input('DineInTable');
            $discounts = $request->input('Discount');
            $taxes = $request->input('Tax');
            $themeSettings = $request->input('ThemeSetting');
            $paymentMethods = $request->input('PaymentMethod');
    
            // Sync Dine-In Tables
            if (!empty($dineInTables)) {
                foreach ($dineInTables as $table) {
                    DineInTable::updateOrCreate(['table_number' => $table['table_number']], $table);
                }
            }
    
            // Sync Discounts
            if (!empty($discounts)) {
                foreach ($discounts as $discount) {
                    Discount::updateOrCreate(['discount_reason' => $discount['discount_reason']], $discount);
                }
                
            }
    
            // Sync Taxes
            if (!empty($taxes)) {
                foreach ($taxes as $tax) {
                    Tax::updateOrCreate(['tax_name' => $tax['tax_name']], $tax);
                }
                
            }
    
            // Sync Theme Settings
            if (!empty($themeSettings)) {
                foreach ($themeSettings as $theme) {
                    ThemeSetting::updateOrCreate(['branch_id' => $theme['branch_id']], $theme);
                }
            }
            if (!empty($themeSettings)) {
                foreach ($paymentMethods as $method) {
                    PaymentMethod::updateOrCreate(
                        [
                            'payment_method' => $method['payment_method'],
                            'order_type' => $method['order_type'],
                            'discount_type' => $method['discount_type'],
                            'branch_id' => $method['branch_id'],
                        ],
                        $method
                    );
                }
            }
            return response()->json(['success' => 'DineInTable, Discount, Tax, and ThemeSetting synced successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to sync data: ' . $e->getMessage()], 500);
        }
    }

    public function syncRecord(Request $request){

        return response()->json([
            'message' => 'Sync completed successfully',
        ], 200);	
    }	
}


















