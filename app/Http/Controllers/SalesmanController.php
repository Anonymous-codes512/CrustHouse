<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Deal;
use App\Models\handler;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Branch;
use App\Models\PaymentMethod;
use App\Models\OnlineNotification;
use App\Models\DineInTable;
use App\Models\Product;
use App\Models\ThemeSetting;
use App\Models\User;
use App\Models\tax;
use App\Models\Discount;
use App\Models\Recipe;
use App\Models\Stock;
use Dompdf\Dompdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\Paginator;

class SalesmanController extends Controller
{
    // public function viewSalesmanDashboard($id, $branch_id)
    // {
    //     if (!session()->has('salesman')) {
    //         return redirect()->route('viewLoginPage');
    //     }

    //     // Start time measurement
    //     $startTime = microtime(true);

    //     // Cache key prefix
    //     $cacheKeyPrefix = "salesman_dashboard_{$id}_{$branch_id}";

    //     // Cache settings
    //     $settings = Cache::remember("{$cacheKeyPrefix}_settings", 60, function () use ($branch_id) {
    //         return ThemeSetting::where('branch_id', $branch_id)->with(['branch.users'])->first();
    //     });

    //     $products = Cache::remember("{$cacheKeyPrefix}_products", 60, function () use ($branch_id) {
    //         return Product::where('branch_id', $branch_id)->paginate(10);
    //     });

    //     $categories = Cache::remember("{$cacheKeyPrefix}_categories", 60, function () use ($branch_id) {
    //         return Category::where('branch_id', $branch_id)->get();
    //     });

    //     $branch = Cache::remember("{$cacheKeyPrefix}_branch", 60, function () use ($branch_id) {
    //         return Branch::find($branch_id);
    //     });

    //     $discounts = Cache::remember("{$cacheKeyPrefix}_discounts", 60, function () use ($branch_id) {
    //         return Discount::where('branch_id', $branch_id)->get();
    //     });

    //     $taxes = Cache::remember("{$cacheKeyPrefix}_taxes", 60, function () use ($branch_id) {
    //         return Tax::where('branch_id', $branch_id)->get();
    //     });

    //     $payment_methods = Cache::remember("{$cacheKeyPrefix}_payment_methods", 60, function () use ($branch_id) {
    //         return PaymentMethod::where('branch_id', $branch_id)->get();
    //     });

    //     // Queries that should not be cached
    //     $tables = DineInTable::where('branch_id', $branch_id)->get();
    //     $allOrders = Order::with(['salesman', 'items'])->where('branch_id', $branch_id)->where('salesman_id', $id)->get();
    //     $onlineOrders = Order::with(['items', 'customers'])->where('ordertype', 'online')->get();
    //     $cartproducts = Cart::with('dineInTable')->where('salesman_id', $id)->get();

    //     $deals = Cache::remember("{$cacheKeyPrefix}_deals", 60, function () use ($branch_id) {
    //         return Handler::where(function ($query) use ($branch_id) {
    //             $query->whereHas('product', function ($query) use ($branch_id) {
    //                 $query->where('branch_id', $branch_id);
    //             })
    //             ->orWhereHas('deal', function ($query) use ($branch_id) {
    //                 $query->where('branch_id', $branch_id);
    //             });
    //         })
    //         ->with([
    //             'product' => function ($query) use ($branch_id) {
    //                 $query->where('branch_id', $branch_id);
    //             }
    //         ])
    //         ->with('deal')
    //         ->paginate(10);
    //     });

    //     $filteredCategories = $categories->filter(function ($category) use ($products) {
    //         return $products->contains('category_id', $category->id) && $category->categoryName !== 'Addons';
    //     });

    //     $filteredProducts = $products->reject(function ($product) {
    //         return $product->category_name === 'Addons';
    //     });

    //     // End time measurement
    //     $endTime = microtime(true);
    //     $executionTime = $endTime - $startTime;
    //     $formattedExecutionTime = number_format($executionTime, 2) . 's';

    //     // Log the execution time
    //     Log::info('DataBase execution time: ' .  $formattedExecutionTime . ' seconds');

    //     return view('Sale Assistant.Dashboard')->with([
    //         'Products' => $filteredProducts,
    //         'Deals' => $deals,
    //         'Categories' => $filteredCategories,
    //         'AllProducts' => $products,
    //         'staff_id' => $id,
    //         'branch_id' => $branch_id,
    //         'cartProducts' => $cartproducts,
    //         'taxes' => $taxes,
    //         'discounts' => $discounts,
    //         'payment_methods' => $payment_methods,
    //         'branch_data' => $branch,
    //         'orders' => $allOrders,
    //         'ThemeSettings' => $settings,
    //         'dineInTables' => $tables,
    //         'onlineOrders' => $onlineOrders,
    //         'executionTime' => $formattedExecutionTime
    //     ]);
    // }
    public function viewSalesmanDashboard($id, $branch_id){
        if (!session()->has('salesman')) {
            return redirect()->route('viewLoginPage');
        }
    
        $startTime = microtime(true);
        $settings = ThemeSetting::where('branch_id', $branch_id)->with(['branch.users'])->first();
        $products = Product::where('branch_id', $branch_id)->paginate(40); // Paginate products here 
        $addons =  Product::where('branch_id', $branch_id)->whereIn('category_name', ['Addons', 'addons', 'Addon', 'addon'])->get();
        $categories = Category::where('branch_id', $branch_id)->whereNotIn('categoryName', ['Addons', 'addons', 'Addon', 'addon'])->get();
        $branch = Branch::find($branch_id);
        $discounts = Discount::where('branch_id', $branch_id)->get();
        $taxes = tax::where('branch_id', $branch_id)->get();
        $payment_methods = PaymentMethod::where('branch_id', $branch_id)->get();
    
        $tables = DineInTable::where('branch_id', $branch_id)->get();
        $allOrders = Order::with(['salesman', 'items'])->where('branch_id', $branch_id)->where('salesman_id', $id)->get();
        $onlineOrders = Order::with(['items', 'customers'])->where('ordertype', 'online')->get();
        $cartproducts = Cart::with('dineInTable')->where('salesman_id', $id)->get();
    
        $deals = handler::where(function ($query) use ($branch_id) {
            $query->whereHas('product', function ($query) use ($branch_id) {
                $query->where('branch_id', $branch_id);
            })
            ->orWhereHas('deal', function ($query) use ($branch_id) {
                $query->where('branch_id', $branch_id);
            });
        })
        ->with([
            'product' => function ($query) use ($branch_id) {
                $query->where('branch_id', $branch_id);
            }
        ])
        ->with('deal')
        ->get();
    
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        $formattedExecutionTime = number_format($executionTime, 2) . 's';
    
        Log::info('DataBase execution time: ' .  $formattedExecutionTime . ' seconds');
    
        return view('Sale Assistant.Dashboard')->with([
            'Products' => $products, // Pass the paginated products directly
            'Deals' => $deals,
            'Categories' => $categories,
            'addons' =>  $addons,
            // 'AllProducts' => $products,
            'staff_id' => $id,
            'branch_id' => $branch_id,
            'cartProducts' => $cartproducts,
            'taxes' => $taxes,
            'discounts' => $discounts,
            'payment_methods' => $payment_methods,
            'branch_data' => $branch,
            'orders' => $allOrders,
            'ThemeSettings' => $settings,
            'dineInTables' => $tables,
            'onlineOrders' => $onlineOrders,
            'executionTime' => $formattedExecutionTime
        ]);
    }
    
    public function salesmanCategoryDashboard($categoryName, $id, $branch_id)
    {
        if (!session()->has('salesman')) {
            return redirect()->route('viewLoginPage');
        }

        $settings = ThemeSetting::where('branch_id', $branch_id)->with(['branch.users'])->first();
        $allProducts = Product::where('branch_id', $branch_id)->paginate(50); 
        $addons =  Product::where('branch_id', $branch_id)->whereIn('category_name', ['Addons', 'addons', 'Addon', 'addon'])->get();
        $categories = Category::where('branch_id', $branch_id)->whereNotIn('categoryName', ['Addons', 'addons', 'Addon', 'addon'])->get();

        $cartproducts = Cart::with('dineInTable')->where('salesman_id', $id)->get();
        $branch = Branch::find($branch_id);

        $tables = DineInTable::where('branch_id', $branch_id)->get();
        $discounts = Discount::where('branch_id', $branch_id)->get();
        $taxes = tax::where('branch_id', $branch_id)->get();
        $payment_methods = PaymentMethod::where('branch_id', $branch_id)->get();
        $allOrders = Order::with(['salesman', 'items'])->where('branch_id', $branch_id)->where('salesman_id', $id)->get();
        $onlineOrders = Order::with(['items', 'customers'])->where('ordertype', 'online')->get();

        $deals = $this->deals($branch_id);

        if ($categoryName != 'Addons') {

            if ($categoryName == 'Deals') {
                return view('Sale Assistant.Dashboard')->with([
                    'Products' => null,
                    'Deals' => $deals,
                    'addons' =>  $addons,
                    'Categories' => $categories,
                    // 'AllProducts' => $allProducts,
                    'staff_id' => $id,
                    'branch_id' => $branch_id,
                    'cartProducts' => $cartproducts,
                    'taxes' => $taxes,
                    'discounts' => $discounts,
                    'payment_methods' => $payment_methods,
                    'branch_data' => $branch,
                    'orders' => $allOrders,
                    'ThemeSettings' => $settings,
                    'dineInTables' => $tables,
                    'onlineOrders' => $onlineOrders
                ]);
            } else {
                $products = Product::where('branch_id', $branch_id)->where('category_name', $categoryName)->paginate(50);
                
                return view('Sale Assistant.Dashboard')->with([
                    'Products' => $products,
                    'Deals' => $deals,
                    'addons' =>  $addons,
                    'Categories' => $categories,
                    // 'AllProducts' => $allProducts,
                    'staff_id' => $id,
                    'branch_id' => $branch_id,
                    'cartProducts' => $cartproducts,
                    'taxes' => $taxes,
                    'discounts' => $discounts,
                    'payment_methods' => $payment_methods,
                    'branch_data' => $branch,
                    'orders' => $allOrders,
                    'ThemeSettings' => $settings,
                    'dineInTables' => $tables,
                    'onlineOrders' => $onlineOrders
                ]);
            }
        }
    }
    public function deals($branch_id)
    {
        $deals = handler::where(function ($query) use ($branch_id) {
            $query->whereHas('product', function ($query) use ($branch_id) {
                $query->where('branch_id', $branch_id);
            })
                ->orWhereHas('deal', function ($query) use ($branch_id) {
                    $query->where('branch_id', $branch_id);
                });
        })
            ->with([
                'product' => function ($query) use ($branch_id) {
                    $query->where('branch_id', $branch_id);
                }
            ])
            ->with('deal')
            ->get();

        return $deals;
    }

    public function addNewProductToDineInOrder($order_Number, $table_id)
    {
        Cart::whereNull('order_status')
            ->where('table_id', null)
            ->where('order_number', null)
            ->delete();

        $table = DineInTable::find($table_id);
        if ($table) {
            if ($table->table_status !== 1) {
                $table->table_status = 1;
                $table->save();
            }
        } else {
            return redirect()->back()->with('error', 'Table not found.');
        }

        $cartedProducts = Cart::where('order_number', $order_Number)->get();

        if ($cartedProducts->isEmpty()) {
            return redirect()->back()->with('error', 'No products found for this order number.');
        }

        foreach ($cartedProducts as $product) {
            $product->order_status = null;
            $product->save();
        }

        return redirect()->back();
    }

    public function placeOrder($salesman_id, Request $request)
    {
        if (!session()->has('salesman')) {
            return redirect()->route('viewLoginPage');
        }

        $table_id = $request->input('table_number');
        $branch_id = $request->input('branch_id');
        $cartItems = json_decode($request->input('cartItems'), true);

        $cartItems['salesman_id'] = $salesman_id;
        $cartItems['branch_id'] = $branch_id;
        $cartItems['table_id'] = $table_id;
        $this->saveToCart($cartItems);

        $servingProducts = Cart::where('salesman_id', $salesman_id)
            ->whereNull('order_status')
            ->whereNotNull('order_number')
            ->whereNotNull('table_id')
            ->get();

        $order_number = $servingProducts->groupBy('order_number')->keys()->first();
        $table_id = $servingProducts->groupBy('table_id')->keys()->first();
        $ordertype = $request->input('orderType');
        $cartedProducts = Cart::where('salesman_id', $salesman_id)->get();

        if ($request->input('table_number') == 0 && $request->input('orderType') == 'Dine-In') {
            $existingOrder = Order::where('order_number', $order_number)
                ->first();

            if ($existingOrder) {
                $existingOrder->total_bill = $request->input('totalbill');
                $existingOrder->taxes = $request->input('totaltaxes');
                $existingOrder->discount = $request->input('discount');
                $existingOrder->discount_reason = $request->input('discount_reason');
                $existingOrder->discount_type = $request->input('discount_type');
                $existingOrder->received_cash = $request->input('recievecash');
                $existingOrder->return_change = $request->input('change');
                $existingOrder->status = 1;

                $existingOrder->save();

                foreach ($servingProducts as $product) {
                    $product->delete();
                }
            }
            $pdfFileName = $this->generateReceipt($existingOrder->id, $existingOrder->order_number);
            return redirect()->back()->with([
                'success' => 'Order finalized successfully.',
                'pdf_filename' => $pdfFileName
            ]);
        }


        if ($request->input('table_number') != 0) {

            $existingOrder = Order::where('order_number', $order_number)
                ->where('table_id', $table_id)
                ->first();
            if ($ordertype == 'Dine-In') {
                $table_id = $request->input('table_number');
                $table = DineInTable::find($table_id);
                $table->table_status = 0;
                $table->save();

                if (empty($table_id)) {
                    return redirect()->back()->with('error', 'Table number is required for Dine-In orders.');
                }
            }


            if ($ordertype !== 'Dine-In' && $cartedProducts->whereNull('order_status')->isEmpty()) {
                return redirect()->back()->with('error', 'Select Product First');
            }

            if ($existingOrder) {
                foreach ($servingProducts as $cartItem) {
                    $existingOrderItem = OrderItem::where('order_id', $existingOrder->id)
                        ->where('product_id', $cartItem->product_id)
                        ->where('product_variation', $cartItem->productVariation)
                        ->first();

                    if ($existingOrderItem) {
                        $existingOrderItem->product_quantity = $cartItem->productQuantity;
                        $existingOrderItem->total_price = floatval($cartItem->totalPrice);
                        $existingOrderItem->save();
                    } else {
                        $orderItem = new OrderItem();
                        $orderItem->order_id = $existingOrder->id;
                        $orderItem->order_number = $existingOrder->order_number;
                        $orderItem->product_id = $cartItem->product_id;
                        $orderItem->product_name = $cartItem->productName;
                        $orderItem->product_variation = $cartItem->productVariation;
                        $orderItem->addons = $cartItem->productAddon;
                        $orderItem->product_price = $cartItem->totalPrice;
                        $orderItem->product_quantity = $cartItem->productQuantity;
                        $orderItem->total_price = $cartItem->totalPrice;
                        $orderItem->save();
                    }
                }

                $newTotalFromServingProducts = array_sum($servingProducts->pluck('totalPrice')->map(function ($price) {
                    return floatval($price);
                })->toArray());

                $existingOrder->total_bill = $newTotalFromServingProducts;
                $existingOrder->save();

                Cart::whereIn('id', $servingProducts->pluck('id'))->update([
                    'order_number' => $existingOrder->order_number,
                    'order_status' => 0,
                ]);

                $pdfFileName = $this->generateReceipt($existingOrder->id, $existingOrder->order_number);
                return redirect()->back()->with([
                    'success' => 'Order updated successfully.',
                    'pdf_filename' => $pdfFileName
                ]);
            }
        }

        $user = User::with('branch')->find($salesman_id);
        $branch_initial = $user->branch->branch_initial;
        $lastOrder = Order::where('branch_id', $user->branch_id)->orderBy('id', 'desc')->first();
        $newOrderNumber = $this->generateOrderNumber($lastOrder, $branch_initial);
        $order = new Order();
        $order->order_number = $newOrderNumber;
        $order->salesman_id = $salesman_id;
        $order->branch_id = $user->branch_id;
        $order->total_bill = 0.0;
        $order->taxes = $request->input('totaltaxes');
        $order->discount = $request->input('discount');
        $order->discount_reason = $request->input('discount_reason');
        $order->discount_type = $request->input('discount_type');
        $order->payment_method = $request->input('payment_method');

        if ($ordertype == 'Dine-In') {
            $order->table_id = $table_id;
            $order->received_cash = null;
        } else {
            $order->total_bill = $request->input('totalbill');
            $order->received_cash = $request->input('recievecash');
            $order->return_change = $request->input('change');
        }

        $order->ordertype = $ordertype;
        $order->save();

        foreach ($cartedProducts as $cartItem) {
            $totalProductPrice = floatval($cartItem->totalPrice);
            $quantity = intval($cartItem->productQuantity);

            if ($ordertype == 'Dine-In') {
                Cart::whereNull('order_number')->update([
                    'table_id' => $table_id,
                    'order_number' => $newOrderNumber,
                    'order_status' => 0,
                ]);
            }

            $orderItem = new OrderItem();
            $orderItem->order_id = $order->id;
            $orderItem->order_number = $newOrderNumber;
            $orderItem->product_id = $cartItem->product_id;
            $orderItem->product_name = $cartItem->productName;
            $orderItem->product_variation = $cartItem->productVariation;
            $orderItem->addons = $cartItem->productAddon;
            $orderItem->product_price = $totalProductPrice / $quantity;
            $orderItem->product_quantity = $quantity;
            $orderItem->total_price = $cartItem->totalPrice;
            $orderItem->save();
        }

        if ($ordertype !== 'Dine-In') {
            foreach ($cartedProducts as $cartItem) {
                if ($cartItem->order_status !== 0) {
                    $cartItem->delete();
                }
            }
        }

        $this->deductStock($order->id);
        $pdfFileName = $this->generateReceipt($order->id, $order->order_number);
        return redirect()->back()->with([
            'success' => 'Order placed successfully.',
            'pdf_filename' => $pdfFileName
        ]);
    }

    private function generateOrderNumber($lastOrder, $branch_initial)
    {
        if ($lastOrder) {
            $lastOrderNumber = intval(substr($lastOrder->order_number, 3));
            return $branch_initial . '-' . sprintf('%03d', $lastOrderNumber + 1);
        }
        return "$branch_initial-100";
    }

    protected function generateReceipt($orderId, $orderNumber)
    {
        $orderData = Order::with(['salesman.branch'])->find($orderId);
        $products = OrderItem::where('order_id', $orderId)->get();

        $customerRecipt = view('reciept', ['products' => $products, 'orderData' => $orderData])->render();
        $dompdf = new Dompdf();
        $dompdf->loadHtml($customerRecipt);
        $height = $dompdf->getCanvas()->get_height();
        $dompdf->setPaper([0, 0, 300, $height / 2], 'portrait');
        $dompdf->render();

        $output = $dompdf->output();
        $pdfFileName = $orderNumber;
        $pdfFilePath = public_path('PDF/' . $pdfFileName);
        file_put_contents($pdfFilePath, $output);
        return  $pdfFileName;
    }
    public function saveToCart($CartedData)
    {
        if (!session()->has('salesman')) {
            return redirect()->route('viewLoginPage');
        }

        $salesman_id = $CartedData['salesman_id'] ?? null;
        $branch_id = $CartedData['branch_id'] ?? null;
        $table_id = $CartedData['table_id'] ?? null;
        // Fetch currently serving products for the given table and salesman
        $servingProducts = Cart::where('table_id', $table_id)
        ->where('salesman_id', $salesman_id)
        ->whereNull('order_status')
        ->get();

        $order_number = $servingProducts->isNotEmpty() ? $servingProducts->first()->order_number : null;
        foreach ($CartedData as $item) {
            if (!is_array($item)) {
                continue;
            }

            $product_id = $item['product_id'];
            $productName = $item['name'];
            $productPrice = $item['price'];
            $productQuantity = (int) $item['quantity'];
            $productVariation = $item['variation'] ?? null;
            $variationPrice = $item['variationPrice'] ?? null;
            $productAddon = $item['addons'] ?? 'No Addons';
            $addonPrice = $item['addonsPrice'] ?? 0;
            $totalPrice = (float) $productPrice;

            $isAlreadyServed = $servingProducts->contains(function ($servingProduct) use (
                $product_id,
                $productName,
                $productAddon,
                $productVariation
            ) {
                return $servingProduct->product_id == $product_id &&
                $servingProduct->productName == $productName &&
                $servingProduct->productAddon == $productAddon &&
                $servingProduct->productVariation == $productVariation;
            });

            // If already in serving products, skip this product
            if ($isAlreadyServed) {
                continue;
            }
     
            // Create new product entry in the cart
            $productOrder = new Cart();
            $productOrder->salesman_id = $salesman_id;
            $productOrder->branch_id = $branch_id;
            $productOrder->product_id = $product_id;
            $productOrder->table_id = $table_id != 0 ? $table_id : null;
            $productOrder->order_number = $order_number;
            $productOrder->productName = $productName;
            $productOrder->productPrice = $productPrice;
            $productOrder->productAddon = $productAddon;
            $productOrder->addonPrice = $addonPrice;
            $productOrder->productVariation = $productVariation;
            $productOrder->variationPrice = $variationPrice;
            $productOrder->productQuantity = $productQuantity;
            $productOrder->totalPrice = $totalPrice;
            $productOrder->save();
        }
    }


    public function deductStock($order_id)
    {
        $order = Order::with('items')->find($order_id);
        $productQuantities = [];

        foreach ($order->items as $item) {
            $deals = Deal::with('handlers', 'products')->find($item->product_id);

            if ($deals && $deals->dealTitle === $item->product_name) {
                foreach ($deals->handlers as $dealHandler) {
                    if (!isset($productQuantities[$dealHandler->product_id])) {
                        $productQuantities[$dealHandler->product_id] = 0;
                    }
                    $productQuantities[$dealHandler->product_id] += $dealHandler->product_quantity * $item->product_quantity;
                }
            } else {
                if (!isset($productQuantities[$item->product_id])) {
                    $productQuantities[$item->product_id] = 0;
                }
                $productQuantities[$item->product_id] += $item->product_quantity;
            }
        }

        foreach ($productQuantities as $product_id => $totalQuantity) {
            $product = Product::find($product_id);
            if ($product) {
                $recipes = Recipe::where('product_id', $product->id)->get();

                foreach ($recipes as $recipeItem) {
                    $quantityToDeduct = floatval($this->convertToBaseUnit($recipeItem->quantity));
                    $stockItem = Stock::find($recipeItem->stock_id);

                    if ($stockItem) {
                        $currentQuantityInBaseUnit = $this->convertToBaseUnit($stockItem->itemQuantity);
                        $deductedQuantityInBaseUnit = $quantityToDeduct * $totalQuantity;
                        $newQuantityInBaseUnit = $currentQuantityInBaseUnit - $deductedQuantityInBaseUnit;
                        $newQuantity = $this->convertFromBaseUnit($newQuantityInBaseUnit, $stockItem->itemQuantity);
                        $stockItem->itemQuantity = $newQuantity;
                        $stockItem->save();
                    }
                }
            }
        }
    }

    private function convertToBaseUnit($quantity)
    {
        preg_match('/(\d+(\.\d+)?)\s*(\w+)/', $quantity, $matches);
        $quantityValue = floatval($matches[1]);
        $unit = strtolower($matches[3]);

        switch ($unit) {
            case 'g':
            case 'ml':
                return $quantityValue;
            case 'kg':
                return $quantityValue * 1000;
            case 'liter':
                return $quantityValue * 1000;
            case 'lbs':
                return $quantityValue * 453.592;
            case 'oz':
                return $quantityValue * 28.3495;
            default:
                return $quantityValue;
        }
    }

    private function convertFromBaseUnit($quantity, $originalUnit)
    {
        preg_match('/(\d+(\.\d+)?)\s*(\w+)/', $originalUnit, $matches);
        $unit = strtolower($matches[3]);

        switch ($unit) {
            case 'kg':
                return ($quantity / 1000) . ' kg';
            case 'g':
                return $quantity . ' g';
            case 'liter':
                return ($quantity / 1000) . ' liter';
            case 'ml':
                return $quantity . ' ml';
            case 'lbs':
                return ($quantity / 453.592) . ' lbs';
            case 'oz':
                return ($quantity / 28.3495) . ' oz';
            default:
                return $quantity . ' ' . $unit;
        }
    }

    public function clearCart($salesman_id)
    {
        if (!session()->has('salesman')) {
            return redirect()->route('viewLoginPage');
        }

        $cartedProducts = Cart::where('salesman_id', $salesman_id)->whereNull('order_number')->get();
        foreach ($cartedProducts as $cartItem) {
            $cartItem->delete();
        }

        return redirect()->back();
    }

    public function removeOneProduct($id, $salesman_id, $branch_id)
    {
        if (!session()->has('salesman')) {
            return redirect()->route('viewLoginPage');
        }

        $cartedProduct = Cart::where('id', $id)->where('salesman_id', $salesman_id)->first();
        if ($cartedProduct) {
            $cartedProduct->delete();
        }
        return redirect()->route('salesman_dashboard', ['id' => $salesman_id, 'branch_id' => $branch_id]);
    }

    public function increaseQuantity($id, $salesman_id, $branch_id)
    {
        if (!session()->has('salesman')) {
            return redirect()->route('viewLoginPage');
        }
        $cartedProduct = Cart::find($id);
        $productPrice = $cartedProduct->totalPrice;

        preg_match('/\d+(\.\d+)?/', $productPrice, $matches);
        $numericPart = $matches[0];
        $productPrice = floatval($numericPart);
        $singleProductPrice = floatval($numericPart) / intval($cartedProduct->productQuantity);

        $cartedProduct->totalPrice = 'Rs. ' . ($productPrice + $singleProductPrice);
        $cartedProduct->productQuantity = intval($cartedProduct->productQuantity) + 1;
        $cartedProduct->save();

        return redirect()->route('salesman_dashboard', ['id' => $salesman_id, 'branch_id' => $branch_id]);
    }

    public function decreaseQuantity($id, $salesman_id, $branch_id)
    {
        if (!session()->has('salesman')) {
            return redirect()->route('viewLoginPage');
        }

        $cartedProduct = Cart::find($id);
        if ($cartedProduct->productQuantity > 1) {
            $productPrice = $cartedProduct->totalPrice;

            preg_match('/\d+(\.\d+)?/', $productPrice, $matches);
            $numericPart = $matches[0];
            $productPrice = floatval($numericPart);
            $quantity = intval($cartedProduct->productQuantity);

            if ($quantity > 1) {
                $singleProductPrice = $productPrice / $quantity;
                $cartedProduct->totalPrice = 'Rs. ' . ($productPrice - $singleProductPrice);
                $cartedProduct->productQuantity = $quantity - 1;
            } else {
                return redirect()->route('salesman_dashboard', ['id' => $salesman_id, 'branch_id' => $branch_id]);
            }

            $cartedProduct->save();
        }

        return redirect()->route('salesman_dashboard', ['id' => $salesman_id, 'branch_id' => $branch_id]);
    }

    public function deleteReceiptPDF($file_name)
    {
        $filePath = public_path('PDF/' . $file_name);
        File::delete($filePath);
        return redirect()->back();
    }

    public function confirmOnlineOrder($branch_id, $salesman_id, $order_id)
    {
        $order = Order::find($order_id);
        $order->salesman_id = $salesman_id;
        $order->status = 4;
        $order->branch_id = $branch_id;
        if ($order->save()) {
            return redirect()->back()->with('success', 'Order confirm successfully');
        } else {
            return redirect()->back()->with('error', 'Order not confirmed');
        }
    }

    public function getNotificationData()
    {
        try {
            $messages = OnlineNotification::all();
            $toast = [];
            return response()->json([
                'collection' => $messages,
                'toast' => $toast,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching data: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    public function deleteNotification($id)
    {
        try {
            $notification = OnlineNotification::findOrFail($id);
            $notification->delete();
            return response()->json(['message' => 'Notification deleted successfully.'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete notification.'], 500);
        }
    }
}


//PDF Combine code.

/*$customerRecipt = view('reciept', ['products' => $products, 'orderData' => $orderData])->render();
$dompdf1 = new Dompdf();
$dompdf1->loadHtml($customerRecipt);
$dompdf1->setPaper([0, 0, 300, 675, 'portrait']);
$dompdf1->render();
$customerPdfContent = $dompdf1->output();

$KitchenRecipt = view('KitchenRecipt', ['products' => $products, 'orderData' => $orderData])->render();
$dompdf2 = new Dompdf();
$dompdf2->loadHtml($KitchenRecipt);
$dompdf2->setPaper([0, 0, 300, 675, 'portrait']);
$dompdf2->render();
$kitchenPdfContent = $dompdf2->output();

$customerPdfPath = storage_path('app/public/') . $newOrderNumber . '_customer.pdf';
$kitchenPdfPath = storage_path('app/public/') . $newOrderNumber . '_kitchen.pdf';
file_put_contents($customerPdfPath, $customerPdfContent);
file_put_contents($kitchenPdfPath, $kitchenPdfContent);

$pdf = new Fpdi();
$pdf->AddPage('P', [105, 180]);
$pdf->setSourceFile($customerPdfPath);
$tplId = $pdf->importPage(1);
$pdf->useTemplate($tplId);

$pdf->AddPage('P', [105, 105]);
$pdf->setSourceFile($kitchenPdfPath);
$tplId = $pdf->importPage(1);
$pdf->useTemplate($tplId);

$combinedPdfPath = storage_path('app/public/') . $newOrderNumber . '_combined.pdf';
$pdf->Output($combinedPdfPath, 'F');

unlink($customerPdfPath);
unlink($kitchenPdfPath);

return response()->download($combinedPdfPath)->deleteFileAfterSend(true);*/


// public function viewSalesmanDashboard($id, $branch_id){
//     if (!session()->has('salesman')) {
//         return redirect()->route('viewLoginPage');
//     }
//   $startTime = microtime(true);
//     $settings = ThemeSetting::where('branch_id', $branch_id)->with(['branch.users'])->first();
//     $products = Product::where('branch_id', $branch_id)->get();
//     $categories = Category::where('branch_id', $branch_id)->get();
//     $branch = Branch::find($branch_id);
//     $discounts = Discount::where('branch_id', $branch_id)->get();
//     $taxes = tax::where('branch_id', $branch_id)->get();
//     $payment_methods = PaymentMethod::where('branch_id', $branch_id)->get();

//     $tables = DineInTable::where('branch_id', $branch_id)->get();
//     $allOrders = Order::with(['salesman', 'items'])->where('branch_id', $branch_id)->where('salesman_id', $id)->get();
//     $onlineOrders = Order::with(['items', 'customers'])->where('ordertype', 'online')->get();
//     $cartproducts = Cart::with('dineInTable')->where('salesman_id', $id)->get();

//     $deals = handler::where(function ($query) use ($branch_id) {
//         $query->whereHas('product', function ($query) use ($branch_id) {
//             $query->where('branch_id', $branch_id);
//         })
//             ->orWhereHas('deal', function ($query) use ($branch_id) {
//                 $query->where('branch_id', $branch_id);
//             });
//     })
//         ->with([
//             'product' => function ($query) use ($branch_id) {
//                 $query->where('branch_id', $branch_id);
//             }
//         ])
//         ->with('deal')
//         ->get();

//     $filteredCategories = $categories->filter(function ($category) use ($products) {
//         return $products->contains('category_id', $category->id) && $category->categoryName !== 'Addons';
//     });

//     $filteredProducts = $products->reject(function ($product) {
//         return $product->category_name === 'Addons';
//     });

//     $endTime = microtime(true);
//     $executionTime = $endTime - $startTime;
//     $formattedExecutionTime = number_format($executionTime, 2) . 's';

//     Log::info('DataBase execution time: ' .  $formattedExecutionTime . ' seconds');

//     return view('Sale Assistant.Dashboard')->with([
//         'Products' => $filteredProducts,
//         'Deals' => $deals,
//         'Categories' => $filteredCategories,
//         'AllProducts' => $products,
//         'staff_id' => $id,
//         'branch_id' => $branch_id,
//         'cartProducts' => $cartproducts,
//         'taxes' => $taxes,
//         'discounts' => $discounts,
//         'payment_methods' => $payment_methods,
//         'branch_data' => $branch,
//         'orders' => $allOrders,
//         'ThemeSettings' => $settings,
//         'dineInTables' => $tables,
//         'onlineOrders' => $onlineOrders,
//         'executionTime' => $formattedExecutionTime
//     ]);
// }



// public function placeOrder($salesman_id, Request $request)
// {
//     if (!session()->has('salesman')) {
//         return redirect()->route('viewLoginPage');
//     }

//     $servingProducts = Cart::where('salesman_id', $salesman_id)->whereNull('order_status')->whereNotNull('order_number')->whereNotNull('table_id')->get();
//     if ($servingProducts->isEmpty()) {
//         return redirect()->back()->with('error', 'No products currently being served.');
//     }

//     $order_number = $servingProducts->groupBy('order_number')->keys()->first();
//     $table_id = $servingProducts->groupBy('table_id')->keys()->first();

//     dd($request->all(), $order_number, $table_id, $servingProducts);

//     $ordertype = $request->input('orderType');
//     $table_id = null;

//     if ($ordertype == 'Dine-In') {
//         $table_id = $request->input('table_number');
//         $table = DineInTable::find($table_id);
//         $table->table_status = 0;
//         $table->save();

//         if (empty($table_id)) {
//             return redirect()->back()->with('error', 'Table number is required for Dine-In orders.');
//         }
//     }

//     $cartedProducts = Cart::where('salesman_id', $salesman_id)->get();
//     if ($ordertype !== 'Dine-In' && $cartedProducts->whereNull('order_status')->isEmpty()) {
//         return redirect()->back()->with('error', 'Select Product First');
//     }

//     // $servingProducts = Cart::whereNull('order_status')->whereNotNull('order_number')->whereNotNull('table_id')->get();
//     // if ($servingProducts->isEmpty()) {
//     //     return redirect()->back()->with('error', 'No products currently being served.');
//     // }


//     $user = User::with('branch')->find($salesman_id);
//     $branch_initial = $user->branch->branch_initials;
//     $lastOrder = Order::where('branch_id', $user->branch_id)->orderBy('id', 'desc')->first();

//     $newOrderNumber = $this->generateOrderNumber($lastOrder, $branch_initial);

//     $order = new Order();
//     $order->order_number = $newOrderNumber;
//     $order->salesman_id = $salesman_id;
//     $order->branch_id = $user->branch_id;
//     $order->total_bill = 0.0;
//     $order->taxes = $request->input('totaltaxes');
//     $order->discount = $request->input('discount');
//     $order->discount_reason = $request->input('discount_reason');
//     $order->discount_type = $request->input('discount_type');
//     $order->payment_method = $request->input('payment_method');

//     if ($ordertype == 'Dine-In') {
//         $order->table_id = $table_id;
//         $order->received_cash = null;
//     } else {
//         $order->received_cash = $request->input('recievecash');
//     }

//     $order->ordertype = $ordertype;
//     $order->save();

//     $totalBill = 0.0;
//     foreach ($cartedProducts as $cartItem) {
//         $totalProductPrice = floatval(str_replace('Rs. ', '', $cartItem->totalPrice));
//         $quantity = intval($cartItem->productQuantity);
//         $totalBill += $totalProductPrice;

//         if ($ordertype == 'Dine-In') {
//             Cart::whereNull('order_number')->update([
//                 'table_id' => $table_id,
//                 'order_number' => $newOrderNumber,
//                 'order_status' => 0,
//             ]);
//         }

//         $orderItem = new OrderItem();
//         $orderItem->order_id = $order->id;
//         $orderItem->order_number = $newOrderNumber;
//         $orderItem->product_id = $cartItem->product_id;
//         $orderItem->product_name = $cartItem->productName;
//         $orderItem->product_variation = $cartItem->productVariation;
//         $orderItem->addons = $cartItem->productAddon;
//         $orderItem->product_price = 'Rs. ' . ($totalProductPrice / $quantity);
//         $orderItem->product_quantity = $quantity;
//         $orderItem->total_price = $cartItem->totalPrice;
//         $orderItem->save();
//     }

//     $discount = $request->input('discount');
//     $discount_type = $request->input('discount_type');
//     if ($discount_type == "%") {
//         $discountValue = (float) (($discount / 100) * $totalBill);
//         $totalBill = $totalBill - $discountValue;
//     } elseif ($discount_type == "-") {
//         $totalBill = $totalBill - $discount;
//     }
//     $totalBill += $request->input('totaltaxes');
//     $order->total_bill = 'Rs. ' . $totalBill;
//     $order->save();

//     if ($ordertype !== 'Dine-In') {
//         foreach ($cartedProducts as $cartItem) {
//             if ($cartItem->order_status !== 0) {
//                 $cartItem->delete();
//             }
//         }
//     }
//     $this->deductStock($order->id);
//     $orderData = Order::with(['salesman.branch'])->find($order->id);

//     $products = OrderItem::where('order_id', $order->id)->get();
//     $customerRecipt = view('reciept', ['products' => $products, 'orderData' => $orderData])->render();
//     $dompdf = new Dompdf();
//     $dompdf->loadHtml($customerRecipt);
//     $height = $dompdf->getCanvas()->get_height();
//     $dompdf->setPaper([0, 0, 300, $height / 2], 'portrait');
//     $dompdf->render();
//     $output = $dompdf->output();
//     $pdfFileName = $order->order_number . '.pdf';
//     $pdfFilePath = public_path('PDF/' . $pdfFileName);
//     file_put_contents($pdfFilePath, $output);
//     return redirect()->back()->with([
//         'success' => 'Order placed successfully.',
//         'pdf_filename' => $pdfFileName
//     ]);
// }





// public function placeOrder($salesman_id, Request $request)
// {
//     if (!session()->has('salesman')) {
//         return redirect()->route('viewLoginPage');
//     }
//     $newOrderNumber = 0;
//     $user = User::with('branch')->find($salesman_id);
//     $branch_initial = $user->branch->branch_initials;
//     $lastOrder = Order::where('branch_id', $user->branch_id)->orderBy('id', 'desc')->first();
//     if ($lastOrder) {
//         $lastOrderNumber = intval(substr($lastOrder->order_number, 3));
//         $newOrderNumber = $branch_initial . '-' . sprintf('%03d', $lastOrderNumber + 1);
//     } else {
//         $newOrderNumber = "$branch_initial-100";
//     }

//     $order = new Order();
//     $cartedProducts = Cart::with('salesman')->where('salesman_id', $salesman_id)->get();
//     if (!$cartedProducts->isEmpty()) {
//         $user = User::find($salesman_id);
//         $totalBill = 0.0;

//         $order->order_number = $newOrderNumber;
//         $order->salesman_id = $salesman_id;
//         $order->branch_id = $user->branch_id;
//         $order->total_bill = $totalBill;
//         $order->taxes = $request->input('totaltaxes');
//         $order->discount = $request->input('discount');
//         $order->discount_reason = $request->input('discount_reason');
//         $order->discount_type = $request->input('discount_type');
//         $order->payment_method = $request->input('payment_method');
//         $order->received_cash = $request->input('recievecash');
//         $order->return_change = $request->input('change');
//         $order->ordertype = $request->input('orderType');
//         $order->save();

//         foreach ($cartedProducts as $cartItem) {
//             preg_match('/\d+(\.\d+)?/', $cartItem->totalPrice, $matches);
//             $numericPart = $matches[0];
//             $totalProductPrice = floatval($numericPart);
//             $quantity = intval($cartItem->productQuantity);
//             $totalBill += $totalProductPrice;

//             $orderItem = new OrderItem();
//             $orderItem->order_id = $order->id;
//             $orderItem->order_number = $newOrderNumber;
//             $orderItem->product_id = $cartItem->product_id;
//             $orderItem->product_name = $cartItem->productName;
//             $orderItem->product_variation = $cartItem->productVariation;
//             $orderItem->addons = $cartItem->productAddon;
//             $orderItem->product_price = 'Rs. ' . ($totalProductPrice / $quantity);
//             $orderItem->product_quantity = $quantity;
//             $orderItem->total_price = $cartItem->totalPrice;
//             $orderItem->save();
//         }

//         foreach ($cartedProducts as $cartItem) {
//             $cartItem->delete();
//         }

//         $discount = $request->input('discount');
//         $discount_type = $request->input('discount_type');
//         if ($discount_type == "%") {
//             $discountValue = (float) (($discount / 100) * $totalBill);
//             $totalBill = $totalBill - $discountValue;
//         } elseif ($discount_type == "-") {
//             $totalBill = $totalBill - $discount;
//         }
//         $totalBill += $request->input('totaltaxes');
//         $order->total_bill = 'Rs. ' . $totalBill;
//         $order->save();

//         $this->deductStock($order->id);
//         $orderData = Order::with(['salesman.branch'])->find($order->id);

//         $products = OrderItem::where('order_id', $order->id)->get();
//         $customerRecipt = view('reciept', ['products' => $products, 'orderData' => $orderData])->render();
//         $dompdf = new Dompdf();
//         $dompdf->loadHtml($customerRecipt);
//         $height = $dompdf->getCanvas()->get_height();
//         $dompdf->setPaper([0, 0, 300, $height / 2], 'portrait');
//         $dompdf->render();
//         $output = $dompdf->output();
//         $pdfFileName = $order->order_number . '.pdf';
//         $pdfFilePath = public_path('PDF/' . $pdfFileName);
//         file_put_contents($pdfFilePath, $output);
//         return redirect()->back()->with([
//             'success' => 'Order placed successfully.',
//             'pdf_filename' => $pdfFileName
//         ]);
//     } else {
//         return redirect()->back()->with('error', 'Select Product First');
//     }
// }




//     public function saveToCart($CartedData)
//     {
//         if (!session()->has('salesman')) {
//             return redirect()->route('viewLoginPage');
//         }
// dd($CartedData);
//         $savedOrder = Cart::whereNotNull(['table_id', 'order_number'])
//         ->whereNull('order_status')
//         ->get();

//         $order_Number = null;
//         $table_id = null;

//         if (!$savedOrder->isEmpty()) {
//             foreach ($savedOrder as $order) {
//                 $order_Number = $order->order_number;
//                 $table_id = $order->table_id;
//             }
//         }

//         $salesman_id = $request->input('salesman_id');
//         $branch_id = $request->input('branch_id');

//         $drinkFlavour = explode(' (Rs. ', rtrim($request->input('drinkFlavour'), ')'));
//         $addon = explode(' (Rs. ', rtrim($request->input('addOn'), ')'));
//         $variations = explode(' (Rs. ', rtrim($request->input('prodVariation'), ')'));

//         $product_id = $request->input('product_id');
//         $productName = $request->input('productname');
//         $productPrice = $request->input('productprice');
//         $productAddon = $addon[0] ?? null;
//         $addonPrice = isset($addon[1]) ? 'Rs. ' . $addon[1] : null;
//         $parts = explode('-', $variations[0]);
//         $productVariation = $parts[1] ?? null;
//         $variationPrice = isset($variations[1]) ? 'Rs. ' . $variations[1] : null;
//         $drinkFlavour = $drinkFlavour[0] ?? null;
//         $drinkFlavourPrice = isset($drinkFlavour[1]) ? 'Rs. ' . $drinkFlavour[1] : null;
//         $productQuantity = (int) $request->input('prodQuantity');

//         $totalPrice = (float) str_replace('Rs. ', '', $request->input('totalprice'));

//         $existingProductOrder = Cart::where('salesman_id', $salesman_id)
//             ->where('branch_id', $branch_id)
//             ->where('product_id', $product_id)
//             ->where('table_id', $table_id)
//             ->where('order_number', $order_Number)
//             ->where('productName', $productName)
//             ->where('productPrice', $productPrice)
//             ->where('productAddon', $productAddon)
//             ->where('addonPrice', $addonPrice)
//             ->where('productVariation', $productVariation)
//             ->where('variationPrice', $variationPrice)
//             ->where('drinkFlavour', $drinkFlavour)
//             ->where('drinkFlavourPrice', $drinkFlavourPrice)
//             ->first();

//         if ($existingProductOrder) {
//             $existingTotalPrice = (float) str_replace('Rs. ', '', $existingProductOrder->totalPrice);
//             $existingProductOrder->productQuantity += $productQuantity;
//             $existingProductOrder->totalPrice = 'Rs. ' . ($existingTotalPrice + $totalPrice);
//             $existingProductOrder->save();
//         } else {
//             $productOrder = new Cart();
//             $productOrder->salesman_id = $salesman_id;
//             $productOrder->branch_id = $branch_id;
//             $productOrder->product_id = $product_id;

//             $productOrder->table_id = $table_id;
//             $productOrder->order_number = $order_Number;

//             $productOrder->productName = $productName;
//             $productOrder->productPrice = $productPrice;
//             $productOrder->productAddon = $productAddon;
//             $productOrder->addonPrice = $addonPrice;
//             $productOrder->productVariation = $productVariation;
//             $productOrder->variationPrice = $variationPrice;
//             $productOrder->drinkFlavour = $drinkFlavour;
//             $productOrder->drinkFlavourPrice = $drinkFlavourPrice;
//             $productOrder->productQuantity = $productQuantity;
//             $productOrder->totalPrice = 'Rs. ' . $totalPrice;
//             $productOrder->save();
//         }

//         return redirect()->route('salesman_dashboard', ['id' => $salesman_id, 'branch_id' => $branch_id]);
//     }
