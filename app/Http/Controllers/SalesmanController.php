<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Deal;
use App\Models\Handler;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Branch;
use App\Models\PaymentMethod;
use App\Models\OnlineNotification;
use App\Models\DineInTable;
use App\Models\Product;
use App\Models\ThemeSetting;
use App\Models\User;
use App\Models\Tax;
use App\Models\Discount;
use App\Models\Recipe;
use App\Models\Stock;
use Dompdf\Dompdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;


class SalesmanController extends Controller
{
    public function viewSalesmanDashboard($id, $branch_id)
    {
        if (!session()->has('salesman')) {
            return redirect()->route('viewLoginPage');
        }

        $settings = ThemeSetting::where('branch_id', $branch_id)->with(['branch.users'])->first();
        $products = Product::where('branch_id', $branch_id)->get();
        $categories = Category::where('branch_id', $branch_id)->get();
        $branch = Branch::find($branch_id);
        $discounts = Discount::where('branch_id', $branch_id)->get();
        $taxes = Tax::where('branch_id', $branch_id)->get();
        $payment_methods = PaymentMethod::where('branch_id', $branch_id)->get();
        $tables = DineInTable::where('branch_id', $branch_id)->get();
        $allOrders = Order::with(['salesman', 'items'])->where('branch_id', $branch_id)->where('salesman_id', $id)->get();

        $onlineOrders = Order::with(['items', 'customers'])->where('ordertype', 'online')->get();

        $deals = Handler::where(function ($query) use ($branch_id) {
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

        $cartproducts = Cart::where('salesman_id', $id)->get();

        // $filteredCategories = $categories->reject(function ($category) {
        //     return $category->categoryName === 'Addons';
        // });

        $filteredCategories = $categories->filter(function ($category) use ($products) {
            return $products->contains('category_id', $category->id) && $category->categoryName !== 'Addons';
        });

        $filteredProducts = $products->reject(function ($product) {
            return $product->category_name === 'Addons';
        });

        return view('Sale Assistant.Dashboard')->with([
            'Products' => $filteredProducts,
            'Deals' => $deals,
            'Categories' => $filteredCategories,
            'AllProducts' => $products,
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
    public function salesmanCategoryDashboard($categoryName, $id, $branch_id)
    {
        if (!session()->has('salesman')) {
            return redirect()->route('viewLoginPage');
        }

        $settings = ThemeSetting::where('branch_id', $branch_id)->with(['branch.users'])->first();
        $allProducts = Product::where('branch_id', $branch_id)->get();
        $categories = Category::where('branch_id', $branch_id)->get();

        $cartproducts = Cart::where('salesman_id', $id)->get();
        $branch = Branch::find($branch_id);

        $tables = DineInTable::where('branch_id', $branch_id)->get();
        $discounts = Discount::where('branch_id', $branch_id)->get();
        $taxes = Tax::where('branch_id', $branch_id)->get();
        $payment_methods = PaymentMethod::where('branch_id', $branch_id)->get();
        $allOrders = Order::with(['salesman', 'items'])->where('branch_id', $branch_id)->where('salesman_id', $id)->get();
        $onlineOrders = Order::with(['items', 'customers'])->where('ordertype', 'online')->get();
        // $filteredCategories = $categories->reject(function ($category) {
        //     return $category->categoryName === 'Addons';
        // });

        $filteredCategories = $categories->filter(function ($category) use ($allProducts) {
            return $allProducts->contains('category_id', $category->id) && $category->categoryName !== 'Addons';
        });
        $deals = $this->deals($branch_id);

        if ($categoryName != 'Addons') {

            if ($categoryName == 'Deals') {
                return view('Sale Assistant.Dashboard')->with([
                    'Products' => null,
                    'Deals' => $deals,
                    'Categories' => $filteredCategories,
                    'AllProducts' => $allProducts,
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
                $products = Product::where('category_name', $categoryName)->get();
                return view('Sale Assistant.Dashboard')->with([
                    'Products' => $products,
                    'Deals' => $deals,
                    'Categories' => $filteredCategories,
                    'AllProducts' => $allProducts,
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
        $deals = Handler::where(function ($query) use ($branch_id) {
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

    public function placeOrder($salesman_id, Request $request)
    {
        if (!session()->has('salesman')) {
            return redirect()->route('viewLoginPage');
        }
        $newOrderNumber = 0;
        $user = User::with('branch')->find($salesman_id);
        $branch_initials = $user->branch->branch_initials;
        $lastOrder = Order::where('branch_id', $user->branch_id)->orderBy('id', 'desc')->first();
        if ($lastOrder) {
            $lastOrderNumber = intval(substr($lastOrder->order_number, 3));
            $newOrderNumber = $branch_initials . '-' . sprintf('%03d', $lastOrderNumber + 1);
        } else {
            $newOrderNumber = "$branch_initials-100";
        }

        $order = new Order();
        $cartedProducts = Cart::with('salesman')->where('salesman_id', $salesman_id)->get();
        if (!$cartedProducts->isEmpty()) {
            $user = User::find($salesman_id);
            $totalBill = 0.0;

            $order->order_number = $newOrderNumber;
            $order->salesman_id = $salesman_id;
            $order->branch_id = $user->branch_id;
            $order->total_bill = $totalBill;
            $order->taxes = $request->input('totaltaxes');
            $order->discount = $request->input('discount');
            $order->discount_reason = $request->input('discount_reason');
            $order->discount_type = $request->input('discount_type');
            $order->payment_method = $request->input('payment_method');
            $order->received_cash = $request->input('recievecash');
            $order->return_change = $request->input('change');
            $order->ordertype = $request->input('orderType');
            $order->save();

            foreach ($cartedProducts as $cartItem) {
                preg_match('/\d+(\.\d+)?/', $cartItem->totalPrice, $matches);
                $numericPart = $matches[0];
                $totalProductPrice = floatval($numericPart);
                $quantity = intval($cartItem->productQuantity);
                $totalBill += $totalProductPrice;

                $orderItem = new OrderItem();
                $orderItem->order_id = $order->id;
                $orderItem->order_number = $newOrderNumber;
                $orderItem->product_id = $cartItem->product_id;
                $orderItem->product_name = $cartItem->productName;
                $orderItem->product_variation = $cartItem->productVariation;
                $orderItem->addons = $cartItem->productAddon;
                $orderItem->product_price = 'Rs. ' . ($totalProductPrice / $quantity);
                $orderItem->product_quantity = $quantity;
                $orderItem->total_price = $cartItem->totalPrice;
                $orderItem->save();
            }

            foreach ($cartedProducts as $cartItem) {
                $cartItem->delete();
            }

            $discount = $request->input('discount');
            $discount_type = $request->input('discount_type');
            if ($discount_type == "%") {
                $discountValue = (float) (($discount / 100) * $totalBill);
                $totalBill = $totalBill - $discountValue;
            } else if ($discount_type == "-") {
                $totalBill = $totalBill - $discount;
            }
            $totalBill += $request->input('totaltaxes');
            $order->total_bill = 'Rs. ' . $totalBill;
            $order->save();

            $this->deductStock($order->id);
            $orderData = Order::with(['salesman.branch'])->find($order->id);

            $products = OrderItem::where('order_id', $order->id)->get();
            $customerRecipt = view('reciept', ['products' => $products, 'orderData' => $orderData])->render();
            $dompdf = new Dompdf();
            $dompdf->loadHtml($customerRecipt);
            $height = $dompdf->getCanvas()->get_height();
            $dompdf->setPaper([0, 0, 300, $height / 2], 'portrait');
            $dompdf->render();
            $output = $dompdf->output();
            $pdfFileName = $order->order_number . '.pdf';
            $pdfFilePath = public_path('PDF/' . $pdfFileName);
            file_put_contents($pdfFilePath, $output);
            return redirect()->back()->with([
                'success' => 'Order placed successfully.',
                'pdf_filename' => $pdfFileName
            ]);
        } else {
            return redirect()->back()->with('error', 'Select Product First');
        }
    }

    public function saveToCart(Request $request)
    {
        if (!session()->has('salesman')) {
            return redirect()->route('viewLoginPage');
        }

        $salesman_id = $request->input('salesman_id');
        $branch_id = $request->input('branch_id');

        $drinkFlavour = explode(' (Rs. ', rtrim($request->input('drinkFlavour'), ')'));
        $addon = explode(' (Rs. ', rtrim($request->input('addOn'), ')'));
        $variations = explode(' (Rs. ', rtrim($request->input('prodVariation'), ')'));

        $product_id = $request->input('product_id');
        $productName = $request->input('productname');
        $productPrice = $request->input('productprice');
        $productAddon = $addon[0] ?? null;
        $addonPrice = isset($addon[1]) ? 'Rs. ' . $addon[1] : null;
        $parts = explode('-', $variations[0]);
        $productVariation = $parts[1] ?? null;
        $variationPrice = isset($variations[1]) ? 'Rs. ' . $variations[1] : null;
        $drinkFlavour = $drinkFlavour[0] ?? null;
        $drinkFlavourPrice = isset($drinkFlavour[1]) ? 'Rs. ' . $drinkFlavour[1] : null;
        $productQuantity = (int) $request->input('prodQuantity'); // Ensure this is an integer

        $totalPrice = (float) str_replace('Rs. ', '', $request->input('totalprice'));

        $existingProductOrder = Cart::where('salesman_id', $salesman_id)
            ->where('branch_id', $branch_id)
            ->where('product_id', $product_id)
            ->where('productName', $productName)
            ->where('productPrice', $productPrice)
            ->where('productAddon', $productAddon)
            ->where('addonPrice', $addonPrice)
            ->where('productVariation', $productVariation)
            ->where('variationPrice', $variationPrice)
            ->where('drinkFlavour', $drinkFlavour)
            ->where('drinkFlavourPrice', $drinkFlavourPrice)
            ->first();

        if ($existingProductOrder) {
            $existingTotalPrice = (float) str_replace('Rs. ', '', $existingProductOrder->totalPrice);
            $existingProductOrder->productQuantity += $productQuantity;
            $existingProductOrder->totalPrice = 'Rs. ' . ($existingTotalPrice + $totalPrice); // Add and convert back to string with 'Rs. ' prefix
            $existingProductOrder->save();
        } else {
            $productOrder = new Cart();
            $productOrder->salesman_id = $salesman_id;
            $productOrder->branch_id = $branch_id;
            $productOrder->product_id = $product_id;
            $productOrder->productName = $productName;
            $productOrder->productPrice = $productPrice;
            $productOrder->productAddon = $productAddon;
            $productOrder->addonPrice = $addonPrice;
            $productOrder->productVariation = $productVariation;
            $productOrder->variationPrice = $variationPrice;
            $productOrder->drinkFlavour = $drinkFlavour;
            $productOrder->drinkFlavourPrice = $drinkFlavourPrice;
            $productOrder->productQuantity = $productQuantity;
            $productOrder->totalPrice = 'Rs. ' . $totalPrice;
            $productOrder->save();
        }

        return redirect()->route('salesman_dashboard', ['id' => $salesman_id, 'branch_id' => $branch_id]);
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

        $cartedProducts = Cart::where('salesman_id', $salesman_id)->get();
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
        if ($order->save())
            return redirect()->back()->with('success', 'Order confirm successfully');
        else
            return redirect()->back()->with('error', 'Order not confirmed');
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
            \Log::error('Error fetching data: ' . $e->getMessage());
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

    // public function sendNotification($message)
    // {
    //     $notify = new OnlineNotification();
    //     $notify->message = $message;
    //     $notify->toast = 0;
    //     $notify->save();
    // }
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