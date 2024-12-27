<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\BranchCategory;
use App\Models\Category;
use App\Models\Deal;
use App\Models\DineInTable;
use App\Models\Discount;
use App\Models\handler;
use App\Models\ThemeSetting;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Recipe;
use App\Models\Stock;
use App\Models\Tax;
use App\Models\StockHistory;
use Dompdf\Dompdf;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash; 
use Illuminate\Support\Facades\Log;

class ManagerController extends Controller
{
    public function viewManagerDashboard($id, $branch_id)
    {
        if (!session()->has('branchManager')) {
            return redirect()->route('viewLoginPage');
        }

        $branch = Branch::find($branch_id);
        $settings = ThemeSetting::where('branch_id', $branch_id)->with(['branch.users'])->first();

        $totalCategories = $this->totalCategories($branch_id);
        $totalProducts = $this->totalProducts($branch_id);
        $totalstocks = $this->totalstocks($branch_id);

        $totalBranchRevenueData = $this->totalBranchRevenue($branch_id);
        $annualRevenueArray = $totalBranchRevenueData['annualRevenueArray'];
        $monthlyRevenueArray = $totalBranchRevenueData['monthlyRevenueArray'];
        $dailyRevenueArray = $totalBranchRevenueData['dailyRevenueArray'];
        $year = $totalBranchRevenueData['year'];
        $month = $totalBranchRevenueData['month'];
        $minYear = $totalBranchRevenueData['minYear'];
        $totalRevenue = $totalBranchRevenueData['totalRevenue'];

        return view('Manager.ManagerDashboard')->with([
            'totalCategories' => $totalCategories,
            'totalProducts' => $totalProducts,
            'totalStocks' => $totalstocks,
            'annualRevenueArray' => $annualRevenueArray,
            'monthlyRevenueArray' => $monthlyRevenueArray,
            'dailyRevenueArray' => $dailyRevenueArray,
            'totalRevenue' => $totalRevenue,
            'year' => $year,
            'month' => $month,
            'minYear' => $minYear,
            'id' => $id,
            'branch_id' => $branch_id,
            'branch' => $branch,
            'ThemeSettings' => $settings
        ]);
    }

    public function readNotification($user_id, $branch_id, $id)
    {
        if (!session()->has('branchManager')) {
            return redirect()->route('viewLoginPage');
        }
        $notification = Notification::find($id);
        $notification->is_read = true;
        $notification->save();
        return Redirect()->route('viewStockPage', [$user_id, $branch_id]);
    }

    public function deleteNotification($user_id, $branch_id, $id)
    {
        if (!session()->has('branchManager')) {
            return redirect()->route('viewLoginPage');
        }

        $notification = Notification::find($id);
        $notification->delete();
        return Redirect()->route('viewStockPage', [$user_id, $branch_id]);
    }
    public function redirectNotification($user_id, $branch_id, $id)
    {
        if (!session()->has('branchManager')) {
            return redirect()->route('viewLoginPage');
        }

        $notification = Notification::find($id);
        $notification->is_read = true;
        $notification->save();

        $stocks = Stock::all();
        return view('Manager.Stock')->with([
            'stockData' => $stocks,
            'notification' => null,
            'user_id' => $user_id,
            'branch_id' => $branch_id,
        ]);
    }

    /*
    |---------------------------------------------------------------|
    |======================= Category Functions ====================|
    |---------------------------------------------------------------|
    */

    public function viewCategoryPage($id, $branch_id)
    {
        if (!session()->has('branchManager')) {
            return redirect()->route('viewLoginPage');
        }
        $category = Category::where('branch_id', $branch_id)->get();
        $settings = ThemeSetting::where('branch_id', $branch_id)->first();
        return view('Manager.Category')->with(['categories' => $category, 'ThemeSettings' => $settings]);
    }
    public function createCategory(Request $request)
    {
        if (!session()->has('branchManager')) {
            return redirect()->route('viewLoginPage');
        }

        $categories = Category::all();
        foreach ($categories as $category) {
            if (strtolower($category->categoryName) == strtolower($request->categoryName)) {
                return redirect()->back()->with('error', 'Category already exists.');
            }
        }


        if ($request->hasFile('CategoryImage')) {
            $image = $request->file('CategoryImage');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('Images/CategoryImages'), $imageName);
        }

        $category = new Category();
        $category->categoryImage = $imageName;
        $category->categoryName = $request->input('categoryName');
        $category->branch_id = $request->branch_id;
        $category->save();

        $branch_category = new BranchCategory();
        $branch_category->category_id = $category->id;
        $branch_category->branch_id = $request->branch_id;
        $branch_category->save();

        return redirect()->back()->with('success', 'Category add successfully');
    }
    public function updateCategory(Request $request)
    {

        if (!session()->has('branchManager')) {
            return redirect()->route('viewLoginPage');
        }

        $categories = Category::all();
        foreach ($categories as $category) {
            if ($category->id != $request->input('id') && strtolower($category->categoryName) == strtolower($request->categoryName)) {
                return redirect()->back()->with('error', 'Category already exist.');
            }
        }

        $category = Category::find($request->id);
        if ($category) {
            if ($request->hasFile('CategoryImage')) {

                $imageName = null;
                $existingImagePath = public_path('Images/CategoryImages') . '/' . $category->categoryImage;
                File::delete($existingImagePath);

                $image = $request->file('CategoryImage');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('Images/CategoryImages'), $imageName);
                $category->categoryImage = $imageName;
            }

            $category->categoryName = $request->input('categoryName');
            $category->save();
            return redirect()->back()->with('success', 'Category Updated successfully');
        } else {
            return redirect()->back()->with('error', 'Category not updated');
        }
    }
    public function deleteCategory($id)
    {
        if (!session()->has('branchManager')) {
            return redirect()->route('viewLoginPage');
        }

        $category = Category::find($id);
        if ($category) {
            $category->delete();
            $imagePath = public_path('Images/CategoryImages') . '/' . $category->categoryImage;
            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }
            return redirect()->back()->with('success', 'Category delete Successfully');
        } else {
            return redirect()->back()->with('success', 'Category not delete');
        }
    }

    /*
    |---------------------------------------------------------------|
    |======================= Product Functions =====================|
    |---------------------------------------------------------------|
    */

    public function viewProductPage($id, $branch_id)
    {
        if (!session()->has('branchManager')) {
            return redirect()->route('viewLoginPage');
        }
        // dd($branch_id, $id);
        $categories = Category::where('branch_id', $branch_id)->get();
        $products = Product::where('branch_id', $branch_id)->orderBy('category_name')->get();
        $settings = ThemeSetting::where('branch_id', $branch_id)->first();
        return view('Manager.Product')->with(['categoryData' => $categories, 'productsData' => $products, 'ThemeSettings' => $settings]);
    }
    public function createProduct(Request $request)
    {
        if (!session()->has('branchManager')) {
            return redirect()->route('viewLoginPage');
        }

        [$categoryId, $categoryName] = explode(',', $request->categoryId);
        $category = Category::with('branches')->find($categoryId);
        $branch_id = $request->branch_id;
        if (!$category) {
            return redirect()->back();
        }

        $total_variations = intval($request->noOfVariations);

        $imageContent = null;
        if ($request->hasFile('productImage')) {
            $uploadedImage = $request->file('productImage');
            $imageContent = file_get_contents($uploadedImage->getRealPath());
        }

        for ($i = 0; $i < $total_variations; $i++) {
            $product = new Product();
            $product->productName = $request->productName;
            $product->productVariation = $request->{"productVariation" . ($i + 1)};
            $product->productPrice = $request->{"price" . ($i + 1)};
            $product->category_id = $categoryId;
            $product->branch_id = $branch_id;
            $product->category_name = $categoryName;

            if ($imageContent) {
                $uniqueImageName = time() . '_' . $i . '_' . mt_rand(1000, 9999) . '.' . $uploadedImage->getClientOriginalExtension();
                $destinationPath = public_path('Images/ProductImages/' . $uniqueImageName);

                try {
                    file_put_contents($destinationPath, $imageContent);
                    $product->productImage = $uniqueImageName;
                } catch (\Exception $e) {

                    Log::error('File upload error: ' . $e->getMessage());
                    return response()->json(['error' => 'File upload error: ' . $e->getMessage()], 500);
                }
            }

            $product->save();
        }
        return redirect()->back()->with('success', 'Product added successfully');
    }
    public function updateProduct(Request $request)
    {
        if (!session()->has('branchManager')) {
            return redirect()->route('viewLoginPage');
        }
        $products = Product::where('productName', $request->productName)->get();

        // Check if a file is uploaded
        if ($request->hasFile('productImage')) {
            $image = $request->file('productImage');
            $imageName = time() . mt_rand(1, 10) . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('Images/ProductImages/'), $imageName);
        } else {
            $imageName = null; // Handle case where no new image is uploaded
        }

        foreach ($products as $key => $product) {
            // Update product image if a new image was uploaded
            if ($request->hasFile('productImage')) {
                // Delete existing image if needed
                $existingImagePath = public_path('Images/ProductImages/') . $product->productImage;
                if (File::exists($existingImagePath)) {
                    File::delete($existingImagePath);
                }

                // Assign new image name to the product
                $product->productImage = $imageName;
            }

            switch ($key + 1) {
                case 1:
                    $product->productVariation = $request->productVariation1;
                    $product->productPrice = $request->price1;
                    break;
                case 2:
                    $product->productVariation = $request->productVariation2;
                    $product->productPrice = $request->price2;
                    break;
                case 3:
                    $product->productVariation = $request->productVariation3;
                    $product->productPrice = $request->price3;
                    break;
                case 4:
                    $product->productVariation = $request->productVariation4;
                    $product->productPrice = $request->price4;
                    break;
                default:
                    break;
            }

            $product->save();
        }

        return redirect()->back()->with('success', 'Products updated successfully.');
    }
    public function deleteProduct($id)
    {
        if (!session()->has('branchManager')) {
            return redirect()->route('viewLoginPage');
        }
        $product = Product::find($id);
        if ($product) {

            $product->delete();
            $imagePath = public_path('Images/ProductImages') . '/' . $product->productImage;
            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }
            return redirect()->back()->with('success', 'Product delete successfully');
        } else {
            return redirect()->back()->with('error', 'Product not delete');

        }
    }

    /*
    |---------------------------------------------------------------|
    |========================= Deal Functions ======================|
    |---------------------------------------------------------------|
    */

    public function viewDealPage($id, $branch_id)
    {
        if (!session()->has('branchManager')) {
            return redirect()->route('viewLoginPage');
        }

        $currentDate = date('Y-m-d');
        $settings = ThemeSetting::where('branch_id', $branch_id)->first();
        $deals = Deal::all();
        foreach ($deals as $deal) {
            if ($deal->dealEndDate <= $currentDate) {
                $deal->dealStatus = 'not active';
                $deal->save();
            }
        }

        $handlers = handler::whereHas('deal', function ($query) use ($branch_id) {
            $query->where('branch_id', $branch_id);
        })->with('deal')->get();

        $deals = $handlers->pluck('deal')->unique();

        $handlersAndProducts = handler::join('products', 'handlers.product_id', '=', 'products.id')
            ->select('handlers.id as handler_id', 'handlers.*', 'products.*')
            ->get();

        return view('Manager.Deal')->with([
            'dealsData' => $deals,
            'dealProducts' => $handlersAndProducts,
            'ThemeSettings' => $settings
        ])->with('success');
    }
    public function viewDealProductsPage($branch_id)
    {
        if (!session()->has('branchManager')) {
            return redirect()->route('viewLoginPage');
        }
        $settings = ThemeSetting::where('branch_id', $branch_id)->first();
        $products = Product::where('branch_id', $branch_id)->orderBy('category_id')->get();
        return view('Manager.DealProducts')->with(['Products' => $products, 'ThemeSettings' => $settings]);
    }
    public function viewUpdateDealProductsPage($deal_id, $branch_id)
    {
        if (!session()->has('branchManager')) {
            return redirect()->route('viewLoginPage');
        }
        $settings = ThemeSetting::where('branch_id', $branch_id)->first();
        $products = Product::where('branch_id', $branch_id)->orderBy('category_id')->get();
        $handler = Deal::where('branch_id', $branch_id)->with('handlers')->find($deal_id);
        return view('Manager.UpdateDealProduct')->with(['Products' => $products, 'dealId' => $deal_id, 'dealproducts' => $handler, 'ThemeSettings' => $settings]);
    }
    public function createDeal(Request $request)
    {
        if (!session()->has('branchManager')) {
            return redirect()->route('viewLoginPage');
        }
        $branch_id = $request->branch_id;

        $existingDeals = Deal::all();
        foreach ($existingDeals as $deal) {
            if (strtolower($deal->dealTitle) == strtolower($request->input('dealTitle'))) {
                return redirect()->back()->with('error', 'Deal already exists.');
            }
        }


        $deal = new Deal();
        $imageName = null;
        if ($request->hasFile('dealImage')) {
            $image = $request->file('dealImage');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('Images/DealImages'), $imageName);
        }

        $deal->dealImage = $imageName;
        $deal->dealTitle = $request->dealTitle;
        $deal->dealStatus = $request->dealStatus;
        $deal->dealEndDate = $request->dealEndDate;
        $deal->branch_id = $branch_id;
        $deal->save();
        session(['deal_id' => $deal->id]);
        return redirect()->route('viewDealProductsPage', $branch_id);
    }
    public function createDealProducts(Request $request)
    {
        if (!session()->has('branchManager')) {
            return redirect()->route('viewLoginPage');
        }
        $branch_id = $request->branch_id;
        $user_id = $request->user_id;

        $productDetails = [];
        $index = 0;
        while ($request->has("product_name_{$index}")) {
            $productDetails[] = [
                'product_id' => $request->input("product_id{$index}"),
                'quantity' => $request->input("product_quantity_{$index}"),
                'total_price' => $request->input("product_total_price_{$index}"),
            ];
            $index++;
        }

        $deal = Deal::find($request->id);

        foreach ($productDetails as $productDetail) {
            $deal->products()->attach(
                $productDetail['product_id'],
                [
                    'product_quantity' => $productDetail['quantity'],
                    'product_total_price' => $productDetail['total_price'],
                ]
            );
        }

        $deal->dealActualPrice = $request->input('currentDealPrice');
        $deal->dealDiscountedPrice = $request->input('dealFinalPrice') . " " . "Pkr";

        $deal->save();

        return redirect()->route('viewDealPage', [$user_id, $branch_id]);
    }
    public function updateDeal(Request $request)
    {
        if (!session()->has('branchManager')) {
            return redirect()->route('viewLoginPage');
        }

        $existingDeals = Deal::all();
        foreach ($existingDeals as $deal) {
            if ($deal->id != $request->input('dId') && strtolower($deal->dealTitle) == strtolower($request->input('dealTitle'))) {
                return redirect()->back()->with('error', 'Deal already exists.');
            }
        }

        $deal = Deal::find($request->dId);

        $branch_id = $request->branch_id;
        $user_id = $request->user_id;

        if ($request->hasFile('dealImage')) {

            $existingImagePath = public_path('Images/DealImages') . '/' . $deal->dealImage;
            File::delete($existingImagePath);

            $image = $request->file('dealImage');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('Images/DealImages'), $imageName);
            $deal->dealImage = $imageName;
        }

        $deal->dealTitle = $request->dealTitle;
        $deal->dealDiscountedPrice = $request->dealprice . " Pkr";
        $deal->dealStatus = $request->dealStatus;
        $deal->dealEndDate = $request->dealEndDate;

        $deal->save();
        session(['id' => $deal->id]);
        return redirect()->route('viewDealPage', [$user_id, $branch_id]);
    }

    public function addDealProduct(Request $request)
    {
        if (!session()->has('branchManager')) {
            return redirect()->route('viewLoginPage');
        }

        $productDetails = [];
        $index = 0;

        $user_id = $request->user_id;
        $branch_id = $request->branch_id;

        while ($request->has("product_name_{$index}")) {
            $productDetails[] = [
                'product_id' => $request->input("product_id{$index}"),
                'quantity' => $request->input("product_quantity_{$index}"),
                'total_price' => $request->input("product_total_price_{$index}"),
            ];
            $index++;
        }

        $deal = Deal::find($request->id);

        $deal->dealActualPrice = $request->input('currentDealPrice');
        $deal->dealDiscountedPrice = $request->input('dealFinalPrice') . ' Pkr';

        foreach ($productDetails as $productDetail) {
            // Check if the product already exists in the deal
            $existingHandler = handler::where('deal_id', $request->id)
                ->where('product_id', $productDetail['product_id'])
                ->first();

            if ($existingHandler) {
                // Update the existing product quantity and total price
                $existingHandler->product_quantity += $productDetail['quantity'];
                $existingHandler->product_total_price = $productDetail['total_price'];
                $existingHandler->save();
            } else {
                // Create a new entry if the product does not exist
                $handler = new handler();
                $handler->deal_id = $request->id;
                $handler->product_id = $productDetail['product_id'];
                $handler->product_quantity = $productDetail['quantity'];
                $handler->product_total_price = $productDetail['total_price'];
                $handler->save();
            }
        }

        $deal->save();

        return redirect()->route('viewDealPage', [$user_id, $branch_id]);
    }

    public function deleteDeal($id)
    {
        if (!session()->has('branchManager')) {
            return redirect()->route('viewLoginPage');
        }

        $deal = Deal::find($id);
        $deal->delete();
        $imagePath = public_path('Images/DealImages') . '/' . $deal->dealImage;
        if (File::exists($imagePath)) {
            File::delete($imagePath);
        }
        return redirect()->back();
    }
    public function deleteDealProduct($id, $dId, $user_id, $branch_id)
    {
        if (!session()->has('branchManager')) {
            return redirect()->route('viewLoginPage');
        }

        $handler = handler::find($id);
        $deals = handler::with(['deal', 'product'])->get();

        // $deals = handler::where(function ($query) use ($branch_id) {
        //     $query->whereHas('product', function ($query) use ($branch_id) {
        //         $query->where('branch_id', $branch_id);
        //     })
        //         ->orWhereHas('deal', function ($query) use ($branch_id) {
        //             $query->where('branch_id', $branch_id);
        //         });
        // })
        //     ->with([
        //         'product' => function ($query) use ($branch_id) {
        //             $query->where('branch_id', $branch_id);
        //         }
        //     ])
        //     ->with('deal')
        //     ->get();


        if (!$handler) {
            return redirect()->route('viewDealPage', [$user_id, $branch_id])->with('error', 'handler not found.');
        }

        $deal = $handler->deal;


        if (!$deal) {
            return redirect()->route('viewDealPage', [$user_id, $branch_id])->with('error', 'Deal not found.');
        }

        $productPrice = intval($handler->product_total_price);

        $dealActualPrice = intval(str_replace(' Pkr', '', $deal->dealActualPrice));
        $dealDiscountedPrice = intval(str_replace(' Pkr', '', $deal->dealDiscountedPrice));

        $updatedDealActualPrice = ($dealActualPrice - $productPrice) . " Pkr";
        $updatedDealDiscountedPrice = ($dealDiscountedPrice - $productPrice) . " Pkr";

        $deal->dealActualPrice = $updatedDealActualPrice;
        $deal->dealDiscountedPrice = $updatedDealDiscountedPrice;

        $deal->save();
        $handler->delete();

        // return redirect()->route('viewDealPage', [$user_id, $branch_id])
        //     ->with('deals', $deals)
        //     ->with('deal', $deal)
        //     ->with('success', 'Product Deleted Successfully.');

        return redirect()->back()->with('success', 'Product Deleted Successfully.');
        ;
    }

    /*
    |---------------------------------------------------------------|
    |====================== Stock's Functions ======================|
    |---------------------------------------------------------------|
    */

    public function viewStockPage($id, $branch_id)
    {
        if (!session()->has('branchManager')) {
            return redirect()->route('viewLoginPage');
        }

        $conversionMap = [
            'g' => 1,           // grams
            'kg' => 1000,       // kilograms
            'mg' => 0.001,      // milligrams
            'lbs' => 453.592,   // pounds
            'oz' => 28.3495,    // ounces
            'ml' => 1,          // milliliters
            'l' => 1000,        // liters
            'liter' => 1000,    // liters
            'gal' => 3785.41,   // gallons
            'piece' => 1,
            'dozen' => 12,
        ];
        $settings = ThemeSetting::where('branch_id', $branch_id)->first();
        $stocks = Stock::where('branch_id', $branch_id)->get();
        $notifications = [];

        foreach ($stocks as $stock) {
            if (preg_match('/([0-9.]+)\s*([a-zA-Z]+)/', $stock->itemQuantity, $matches)) {
                $quantity = (float) $matches[1];
                $unit = strtolower($matches[2]);
            } else {
                continue;
            }

            if (preg_match('/([0-9.]+)\s*([a-zA-Z]+)/', $stock->mimimumItemQuantity, $minMatches)) {
                $minimumQuantity = (float) $minMatches[1];
                $minimumUnit = strtolower($minMatches[2]);
            } else {
                continue;
            }

            $quantityInBaseUnit = isset($conversionMap[$unit]) ? $quantity * $conversionMap[$unit] : $quantity;
            $minimumQuantityInBaseUnit = isset($conversionMap[$minimumUnit]) ? $minimumQuantity * $conversionMap[$minimumUnit] : $minimumQuantity;
            if ($quantityInBaseUnit <= $minimumQuantityInBaseUnit) {
                $notificationMessage = "Quantity of {$stock->itemName} is below or equal to the minimum level";
                Notification::create(['message' => $notificationMessage]);
                $notifications[] = $notificationMessage;
            }
        }

        $notify = Notification::where('is_read', false)->get();
        session(['Notifications' => $notify]);
        return view('Manager.Stock')->with(['stockData' => $stocks, 'notification' => $notifications, 'user_id' => $id, 'branch_id' => $branch_id, 'ThemeSettings' => $settings]);
    }
    public function createStock(Request $request)
    {
        if (!session()->has('branchManager')) {
            return redirect()->route('viewLoginPage');
        }

        $branch_id = $request->branch_id;

        $stock_history = new StockHistory();
        $stock_history->itemName = $request->itemName;
        $stock_history->itemQuantity = number_format($request->stockQuantity, 2) . ' ' . $request->unit1;
        $stock_history->mimimumItemQuantity = number_format($request->minStockQuantity, 2) . ' ' . $request->unit2;
        $stock_history->unitPrice = number_format($request->unitPrice, 2) . ' Pkr';
        $stock_history->branch_id = $branch_id;
        $stock_history->save();

        $existingStock = Stock::where('itemName', $request->itemName)->where('branch_id', $branch_id)->first();

        if ($existingStock) {
            preg_match('/([0-9.]+)\s*([a-zA-Z]+)/', $existingStock->itemQuantity, $matches);
            $quantity = (float) ($matches[1] ?? 0);
            $unit = strtolower($matches[2] ?? 'unit');
            $conversionMap = [
                'g' => 1,
                'kg' => 1000,
                'mg' => 0.001,
                'lbs' => 453.592,
                'oz' => 28.3495,
                'ml' => 1,
                'liter' => 1000,
                'gal' => 3785.41,
                'piece' => 1,
                'dozen' => 12,
            ];

            $quantityInBaseUnit = isset($conversionMap[$unit]) ? $quantity * $conversionMap[$unit] : $quantity;
            $incomingQuantityInBaseUnit = isset($conversionMap[$request->unit1]) ? $request->stockQuantity * $conversionMap[$request->unit1] : $request->stockQuantity;
            $totalQuantityInBaseUnit = $quantityInBaseUnit + $incomingQuantityInBaseUnit;

            $updatedQuantity = $totalQuantityInBaseUnit / $conversionMap[$unit];

            $existingStock->itemQuantity = number_format($updatedQuantity, 2) . ' ' . $unit;
            $existingStock->mimimumItemQuantity = number_format($request->minStockQuantity, 2) . ' ' . $request->unit2;
            $existingStock->unitPrice = number_format($request->unitPrice, 2) . ' Pkr';

            $existingStock->save();
            return redirect()->back();
        } else {
            $newStock = new Stock();
            $newStock->itemName = $request->itemName;
            $newStock->branch_id = $branch_id;

            $newStock->itemQuantity = number_format($request->stockQuantity, 2) . ' ' . $request->unit1;
            $newStock->mimimumItemQuantity = number_format($request->minStockQuantity, 2) . ' ' . $request->unit2;
            $newStock->unitPrice = number_format($request->unitPrice, 2) . ' Pkr';

            $newStock->save();
            return redirect()->back();
        }
    }
    public function updateStock(Request $request)
    {
        if (!session()->has('branchManager')) {
            return redirect()->route('viewLoginPage');
        }
        $stockData = Stock::find($request->sId);

        $stockData->itemName = $request->itemName;
        $stockData->itemQuantity = $request->stockQuantity . $request->unit1;
        $stockData->mimimumItemQuantity = $request->minStockQuantity . $request->unit2;
        $stockData->unitPrice = $request->unitPrice . ' Pkr';

        $stockData->save();
        return redirect()->back();
    }
    public function deleteStock($id)
    {
        if (!session()->has('branchManager')) {
            return redirect()->route('viewLoginPage');
        }

        $stockData = Stock::find($id);
        $stockData->delete();

        return redirect()->back();
    }
    public function stockHistory($branch_id, $user_id)
    {
        $stock_history = StockHistory::where('branch_id', $branch_id)->get();
        $settings = ThemeSetting::where('branch_id', $branch_id)->first();
        return view('Manager.StockHistory')->with(['stockHistory' => $stock_history, 'branch_id' => $branch_id, 'user_id' => $user_id, 'ThemeSettings' => $settings]);
    }

    /*
        |---------------------------------------------------------------|
        |======================= Recipe Functions ======================|
        |---------------------------------------------------------------|
        */

    public function viewRecipePage($id, $branch_id)
    {
        if (!session()->has('branchManager')) {
            return redirect()->route('viewLoginPage');
        }
        $categories = Category::where('branch_id', $branch_id)->get();
        $products = Product::where('branch_id', $branch_id)->get();
        $categoryIdsWithProducts = $products->pluck('category_id')->unique();
        $settings = ThemeSetting::where('branch_id', $branch_id)->first();

        $categoriesWithProducts = $categories->filter(function ($category) use ($categoryIdsWithProducts) {
            return $categoryIdsWithProducts->contains($category->id);
        });

        $stocks = Stock::where('branch_id', $branch_id)->get();
        $recipe = Recipe::with('product', 'stock')->get();

        // session('showProductRecipe', false);
        return view('Manager.Recipe')->with([
            'categories' => $categoriesWithProducts,
            'stocks' => $stocks,
            'user_id' => $id,
            'branch_id' => $branch_id,
            'recipes' => $recipe,
            'ThemeSettings' => $settings,
            'showProductRecipe' => false
        ]);
    }

    public function showCategoryProducts($category_id, $branch_id, $user_id)
    {
        if (!session()->has('branchManager')) {
            return redirect()->route('viewLoginPage');
        }
        $products = Product::where('category_id', $category_id)->where('branch_id', $branch_id)->get();
        return redirect()->back()->with('categoryProducts', $products);

        // $categories = Category::where('branch_id', $branch_id)->get();
        // $stocks = Stock::where('branch_id', $branch_id)->get();
        // $settings = ThemeSetting::where('branch_id', $branch_id)->first();

        // $recipes = Recipe::where(function ($query) use ($branch_id) {
        //     $query->whereHas('product', function ($query) use ($branch_id) {
        //         $query->where('branch_id', $branch_id);
        //     })
        //         ->orWhereHas('stock', function ($query) use ($branch_id) {
        //             $query->where('branch_id', $branch_id);
        //         });
        // })
        //     ->with([
        //         'product' => function ($query) use ($branch_id) {
        //             $query->where('branch_id', $branch_id);
        //         }
        //     ])
        //     ->with('stock')
        //     ->get();

        // $products = Product::where('branch_id', $branch_id)->with('category')->get();

        // $categoryProducts = Product::where('category_id', $category_id)->get();
        // $categories = Category::where('branch_id', $branch_id)->get();
        // $categoryIdsWithProducts = $products->pluck('category_id')->unique();
        // $categoriesWithProducts = $categories->filter(function ($category) use ($categoryIdsWithProducts) {
        //     return $categoryIdsWithProducts->contains($category->id);
        // });
        // return view('Manager.Recipe')->with(['categoryProducts' => $categoryProducts, 'categories' => $categoriesWithProducts, 'branch_id' => $branch_id, 'user_id' => $user_id, 'stocks' => $stocks, 'recipes' => $recipes, 'ThemeSettings' => $settings, 'showProductRecipe' => false]);
    }

    public function viewProductRecipe($category_id, $product_id, $user_id, $branch_id)
    {
        if (!session()->has('branchManager')) {
            return redirect()->route('viewLoginPage');
        }

        $recipes = Recipe::where('product_id', $product_id)->where('category_id', $category_id)->with('stock', 'product')->get();

        $recipes->category_id = $category_id;
        $recipes->product_id = $product_id;
        return redirect()->back()->with('productRecipe', $recipes);

        // $recipes = Recipe::where('product_id', $product_id)->where('category_id', $category_id)->with('stock', 'product')->get();
        // $settings = ThemeSetting::where('branch_id', $branch_id)->first();
        // $products = Product::where('branch_id', $branch_id)->with('category')->get();

        // $categories = Category::where('branch_id', $branch_id)->get();
        // $categoryIdsWithProducts = $products->pluck('category_id')->unique();
        // $categoriesWithProducts = $categories->filter(function ($category) use ($categoryIdsWithProducts) {
        //     return $categoryIdsWithProducts->contains($category->id);
        // });

        // $stocks = Stock::all();
        // session()->put('showProductRecipe', true);

        // return redirect()->back()->with('showProductRecipe', true);
        // return view('Manager.Recipe')->with([
        //     'categoryProducts' => $products,
        //     'categories' => $categoriesWithProducts,
        //     'stocks' => $stocks,
        //     'recipes' => $recipes,
        //     'branch_id' => $branch_id,
        //     'user_id' => $user_id,
        //     'ThemeSettings' => $settings,
        //     'showProductRecipe' => true
        // ]);
    }

    public function createRecipe(Request $request)
    {
        if (!session()->has('branchManager')) {
            return redirect()->route('viewLoginPage');
        }
        $user_id = $request->user_id;
        $branch_id = $request->branch_id;
        $requestData = $request->all();

        $category_id = $requestData['cId'];
        $product_id = $requestData['pId'];

        $existingRecipes = Recipe::where('category_id', $category_id)
            ->where('product_id', $product_id)
            ->get();

        if ($existingRecipes->isNotEmpty()) {
            Recipe::where('category_id', $category_id)
                ->where('product_id', $product_id)
                ->delete();
        }

        $recipeItems = array_filter(explode(',', $requestData['recipeItems']), function ($item) {
            return trim($item) !== '';
        });

        foreach ($recipeItems as $item) {
            $itemParts = explode('~', trim($item));
            if (count($itemParts) == 2) {
                $quantity = trim($itemParts[0]);
                $stockId = trim($itemParts[1]);

                $newRecipe = new Recipe();
                $newRecipe->category_id = $category_id;
                $newRecipe->product_id = $product_id;
                $newRecipe->stock_id = $stockId;
                $newRecipe->quantity = $quantity;
                $newRecipe->save();
            }
        }

        return redirect()->route('viewRecipePage', [$user_id, $branch_id]);
    }
    public function editProductRecipe(Request $req)
    {
        $recipe = Recipe::find($req->recipeId);
        $recipe->quantity = $req->input('item-stock-quantity') . ' ' . $req->input('unit1');
        $recipe->save();
        // session('editproductrecipie', true);
        return redirect()->back();
    }

    public function deleteStockFromRecipe($id, $cId, $pId)
    {
        if (!session()->has('branchManager')) {
            return redirect()->route('viewLoginPage');
        }

        $recipe = Recipe::find($id);
        if ($recipe) {
            $recipe->delete();
        }

        return redirect()->route('viewProductRecipe', ['category_id' => $cId, 'product_id' => $pId]);
    }


    /* 
    |---------------------------------------------------------------|
    |====================== Orders Functions =======================|
    |---------------------------------------------------------------|
    */

    public function viewOrdersPage($id, $branch_id)
    {
        if (!session()->has('branchManager')) {
            return redirect()->route('viewLoginPage');
        }
        $settings = ThemeSetting::where('branch_id', $branch_id)->first();
        $orders = Order::with('salesman')
            ->where('branch_id', $branch_id)
            ->orderBy('id', 'desc')  // Sort by 'id' in descending order
            ->get();
        return view('Manager.Order')->with(['orders' => $orders, 'orderItems' => null, 'ThemeSettings' => $settings]);
    }
    public function viewOrderProducts($branch_id, $order_id)
    {
        $orders = Order::with('salesman')->where('id', $order_id)->get();
        $orderItems = OrderItem::where('order_id', $order_id)->get();
        $settings = ThemeSetting::where('branch_id', $branch_id)->first();
        return view('Manager.Order')->with(['orders' => $orders, 'orderItems' => $orderItems, 'ThemeSettings' => $settings]);
    }
    public function printRecipt($order_id)
    {
        $order = Order::with('salesman', 'branch')->where('id', $order_id)->first();
        $products = OrderItem::where('order_id', $order_id)->get();
        // return view('reciept')->with(['products' => $products, 'orderData' => $order]);

        $html = view('reciept', ['products' => $products, 'orderData' => $order])->render();
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $height = $dompdf->getCanvas()->get_height();
        $dompdf->setPaper([0, 0, 300, $height / 2], 'portrait');
        $dompdf->render();
        $dompdf->stream($order->order_number . '.pdf');
    }
    public function cancelOrder($order_id, $staff_id)
    {
        $staff = User::find($staff_id);
        $order = Order::where('id', $order_id)->first();
        $order->status = 3;
        if($order->order_cancel_by == null){
            $order->order_cancel_by = $staff->name;
        }
        $this->returnStock($order_id);
        $order->save();

        return redirect()->back();
    }
    public function returnStock($order_id)
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
            if(!$product){
                return redirect()->back();
            }
            $recipes = Recipe::where('product_id', $product->id)->get();

            foreach ($recipes as $recipeItem) {
                $quantityToReturn = floatval($this->convertToBaseUnit($recipeItem->quantity));
                $stockItem = Stock::find($recipeItem->stock_id);

                if ($stockItem) {
                    $currentQuantityInBaseUnit = $this->convertToBaseUnit($stockItem->itemQuantity);
                    $deductedQuantityInBaseUnit = $quantityToReturn * $totalQuantity;
                    $newQuantityInBaseUnit = $currentQuantityInBaseUnit + $deductedQuantityInBaseUnit;
                    $newQuantity = $this->convertFromBaseUnit($newQuantityInBaseUnit, $stockItem->itemQuantity);

                    $stockItem->itemQuantity = $newQuantity;
                    $stockItem->save();
                }
            }
        }
    }
    public function viewStaffPage($id, $branch_id)
    {
        if (!session()->has('branchManager')) {
            return redirect()->route('viewLoginPage');
        }
        $settings = ThemeSetting::where('branch_id', $branch_id)->first();
        $branches = Branch::where('id', $branch_id)->get();
        $staff = User::with('branch')
            ->where('branch_id', $branch_id)
            ->whereIn('role', ['salesman', 'chef', 'rider','branchManager'])
            ->get();
        return view('Manager.Staff')->with(['Staff' => $staff, 'branches' => $branches, 'ThemeSettings' => $settings]);
    }
    public function updateStaff(Request $req)
    {
        if (!session()->has('branchManager')) {
            return redirect()->route('viewLoginPage');
        }
        $existingChef = User::where('role', 'chef')
            ->where('id', '!=', $req->staffId)
            ->where('branch_id', $req->branch)
            ->get();

        if (!$existingChef->isEmpty() && $req->input('role') == 'chef') {
            return redirect()->back()->with('error', 'Chef already exists in this branch');
        }

        $existingEmail = User::where('email', $req->input('email'))
            ->where('id', '!=', $req->input('staffId'))
            ->first();
        if ($existingEmail) {
            return redirect()->back()->with('error', 'Email already exists');
        }

        $auth = User::find($req->input('staffId'));

        if ($req->hasFile('updated_profile_picture')) {
            $existingImagePath = public_path('Images/UsersImages') . '/' . $auth->profile_picture;
            if (File::exists($existingImagePath)) {
                File::delete($existingImagePath);
            }

            $image = $req->file('updated_profile_picture');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('Images/UsersImages'), $imageName);
            $auth->profile_picture = $imageName;
        }

        $auth->name = $req->input('name');
        $auth->phone_number = $req->input('phone_number');
        $auth->email = $req->input('email');
        $auth->role = $req->input('role');

        if ($req->filled('password')) {
            $auth->password = Hash::make($req->input('password'));
        }

        if ($auth->save()) {
            return redirect()->back()->with('success', 'Staff updated successfully');
        } else {
            return redirect()->back()->with('error', 'Staff not updated');
        }
    }
    public function deleteStaff($id)
    {
        if (!session()->has('branchManager')) {
            return redirect()->route('viewLoginPage');
        }

        $product = Product::find($id);

        $staff = User::find($id);
        if ($staff) {
            $staff->delete();
            $imagePath = public_path('Images/UsersImages') . '/' . $staff->profile_picture;
            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }
            return redirect()->back()->with('success', 'Staff Deleted ');
        } else {
            return redirect()->back()->with('error', 'Staff not found');
        }
    }
    private function convertToBaseUnit($quantity)
    {
        // Assuming quantity is in the format "10 kg", "5 liter", etc.
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
                return $quantityValue * 453.592; // 1 lb = 453.592 g
            case 'oz':
                return $quantityValue * 28.3495; // 1 oz = 28.3495 g
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

    /*
    |---------------------------------------------------------------|
    |======================= Dine-In Functions =====================|
    |---------------------------------------------------------------|
    */

    public function viewDineInPage($branch_id)
    {
        if (!session()->has('branchManager')) {
            return redirect()->route('viewLoginPage');
        }
        $settings = ThemeSetting::where('branch_id', $branch_id)->first();
        $branch = Branch::find($branch_id);
        if ($branch->DiningOption == 0) {
            return redirect()->back()->with('warning', 'The Dine-In option for this branch is disable, kindly contact with Tachyon.');
        }
        $dineInTables = DineInTable::where('branch_id', $branch_id)->get();
        return view('Manager.Dine-In')->with(['dineInTables' => $dineInTables, 'ThemeSettings' => $settings]);
    }

    public function createTable(Request $request)
    {

        $branch_id = $request->input('branch_id');

        $newTable = new DineInTable();

        $newTable->table_number = $request->input('table_number');
        $newTable->max_sitting_capacity = $request->input('table_max_capacity');
        $newTable->table_status = 1;          //Available = 1, occupied = 0;
        $newTable->branch_id = $branch_id;

        if ($newTable->save()) {
            return redirect()->back()->with('success', "Table added successfully");
        } else {
            return redirect()->back()->with('error', "Table not added");
        }
    }
    public function updateTable(Request $request)
    {
        $table_id = $request->input('table_id');

        $existingTable = DineInTable::find($table_id);

        $existingTable->table_number = $request->input('table_number');
        $existingTable->max_sitting_capacity = $request->input('table_max_capacity');

        // $existingTable->table_status = 1;          //Available = 1, occupied = 0;

        if ($existingTable->save()) {
            return redirect()->back()->with('success', "Table updated successfully");
        } else {
            return redirect()->back()->with('error', "Table not updated");
        }
    }
    public function deleteTable($table_id)
    {
        $existingTable = DineInTable::find($table_id);
        if ($existingTable->delete()) {
            return redirect()->back()->with('success', "Table updated successfully");
        } else {
            return redirect()->back()->with('error', "Table not updated");
        }
    }

    /*
    |---------------------------------------------------------------|
    |====================== Settings Functions =====================|
    |---------------------------------------------------------------|
    */

    public function viewSettingsPage($id, $branch_id)
    {
        $settings = ThemeSetting::where('branch_id', $branch_id)->first();
        $taxes = Tax::where('branch_id', $branch_id)->get();
        $discounts = Discount::where('branch_id', $branch_id)->get();
        $receipts = Branch::where('id', $branch_id)->first();
        $profile = User::where('branch_id', $branch_id)->where('role', 'branchManager')->first();
        $settingsData = ThemeSetting::where('branch_id', $branch_id)->first();
        $paymentMethods = PaymentMethod::where('branch_id', $branch_id)->whereNotNull('payment_method')->get();
        $orderTypes = PaymentMethod::where('branch_id', $branch_id)->whereNotNull('order_type')->get();
        $discountTypes = PaymentMethod::where('branch_id', $branch_id)->whereNotNull('discount_type')->get();
        $branchOptions = Branch::find($branch_id);
        return view('Manager.Setting')->with([
            'taxes' => $taxes,
            'discounts' => $discounts,
            'receipt' => $receipts,
            'paymentMethods' => $paymentMethods,
            'orderTypes' => $orderTypes,
            'discountTypes' => $discountTypes,
            'settingsData' => $settingsData,
            'profile' => $profile,
            'branchOptions' => $branchOptions,
            'ThemeSettings' => $settings
        ]);
    }
    public function createTax(Request $request)
    {
        $branch_id = $request->input('branch_id');
        $newTax = new Tax();
        $newTax->tax_name = $request->tax_name;
        $newTax->tax_value = $request->tax_value;
        $newTax->branch_id = $branch_id;
        $newTax->save();

        return redirect()->back()->with('success', 'Tax added successfully');
    }
    public function updateTax(Request $request)
    {
        $tax_id = $request->tax_id;
        $newTax = Tax::find($tax_id);
        if ($newTax) {
            $newTax->tax_name = $request->tax_name;
            $newTax->tax_value = $request->tax_value;
            $newTax->save();
            return redirect()->back()->with('success', 'Tax update successfully');
        } else {
            return redirect()->back()->with('error', 'Tax not update');
        }
    }
    public function deleteTax($id)
    {
        $newTax = Tax::find($id);
        if ($newTax) {
            $newTax->delete();
            return redirect()->back()->with('success', 'Tax delete successfully');
        } else {
            return redirect()->back()->with('error', 'Tax not delete');
        }
    }

    public function createDiscount(Request $request)
    {
        $branch_id = $request->input('branch_id');
        $newDiscount = new Discount();
        $newDiscount->discount_reason = $request->discount_reason;
        $newDiscount->branch_id = $branch_id;
        $newDiscount->save();

        return redirect()->back()->with('success', 'Discount added successfully');
    }
    public function updateDiscount(Request $request)
    {
        $discount_id = $request->input('discount_id');
        $discount = Discount::find($discount_id);
        if ($discount) {
            $discount->discount_reason = $request->discount_reason;
            $discount->save();
            return redirect()->back()->with('success', 'Discount update successfully');
        } else {
            return redirect()->back()->with('error', 'Discount not update');
        }
    }
    public function deleteDiscount($id)
    {
        $discount = Discount::find($id);
        if ($discount) {
            $discount->delete();
            return redirect()->back()->with('success', 'Discount delete successfully');
        } else {
            return redirect()->back()->with('error', 'Discount not delete');
        }
    }

    public function createReceipt(Request $request)
    {
        $branch_id = $request->input('branch_id');
        $newReceipt = Branch::find($branch_id);
        $newReceipt->receipt_message = $request->receipt_message;
        $newReceipt->branch_web_address = $request->branch_web_address;
        $newReceipt->feedback = $request->feedback;
        $newReceipt->receipt_tagline = $request->receipt_tagline;
        $newReceipt->save();

        return redirect()->back()->with('success', 'Message added successfully');
    }
    public function updateReceipt(Request $request)
    {
        $receipt_id = $request->input('receipt_id');
        $receipt = Branch::find($receipt_id);
        if ($receipt) {
            $receipt->receipt_message = $request->receipt_message;
            $receipt->branch_web_address = $request->branch_web_address;
            $receipt->feedback = $request->feedback;
            $receipt->receipt_tagline = $request->receipt_tagline;
            $receipt->save();
            return redirect()->back()->with('success', 'Message update successfully');
        } else {
            return redirect()->back()->with('error', 'Message not update');
        }
    }
    public function deleteReceipt($id)
    {
        $receipt = Branch::find($id);
        if ($receipt) {
            $receipt->receipt_message = null;
            $receipt->branch_web_address = null;
            $receipt->feedback = null;
            $receipt->receipt_tagline = null;
            $receipt->save();
            return redirect()->back()->with('success', 'Message delete successfully');
        } else {
            return redirect()->back()->with('error', 'Message not delete');
        }
    }

    public function createPaymentMethod(Request $request)
    {
        $branch_id = $request->input('branch_id');
        $newPaymentMethod = new PaymentMethod();
        $newPaymentMethod->payment_method = $request->payment_method;
        $newPaymentMethod->branch_id = $branch_id;
        $newPaymentMethod->save();

        return redirect()->back()->with('success', 'Payment Method added successfully');
    }
    public function updatePaymentMethod(Request $request)
    {
        $payment_method_id = $request->input('payment_method_id');
        $payment_methods = PaymentMethod::find($payment_method_id);
        if ($payment_methods) {
            $payment_methods->payment_method = $request->payment_method;
            $payment_methods->save();
            return redirect()->back()->with('success', 'Payment Method update successfully');
        } else {
            return redirect()->back()->with('error', 'Payment Method not update');
        }
    }

    public function deletePaymentMethod($id)
    {
        $payment_methods = PaymentMethod::find($id);
        if ($payment_methods) {
            $payment_methods->delete();
            return redirect()->back()->with('success', 'Payment Method delete successfully');
        } else {
            return redirect()->back()->with('error', 'Payment Method not delete');
        }
    }
    public function createDiscountTypes(Request $request)
    {
        $branch_id = $request->input('branch_id');
        $newDiscountType = new PaymentMethod();
        $newDiscountType->discount_type = $request->discount_type;
        $newDiscountType->branch_id = $branch_id;
        $newDiscountType->save();

        return redirect()->back()->with('success', 'Discount Type added successfully');
    }
    public function updateDiscountTypes(Request $request)
    {
        $discount_type_id = $request->input('discount_type_id');
        $discountType = PaymentMethod::find($discount_type_id);
        if ($discountType) {
            $discountType->discount_type = $request->discount_type;
            $discountType->save();
            return redirect()->back()->with('success', 'Discount Type update successfully');
        } else {
            return redirect()->back()->with('error', 'Discount type not update');
        }
    }

    public function deleteDiscountTypes($id)
    {
        $discountType = PaymentMethod::find($id);
        if ($discountType) {
            $discountType->delete();
            return redirect()->back()->with('success', 'Discount Type delete successfully');
        } else {
            return redirect()->back()->with('error', 'Discount Type not delete');
        }
    }

    public function createDiscountValue(Request $request)
    {
        $branch_id = $request->input('branch_id');
        $newDiscountValue = Branch::find($branch_id);
        $newDiscountValue->max_discount_percentage = $request->max_discount_percentage;
        $newDiscountValue->save();

        return redirect()->back()->with('success', 'Value added successfully');
    }
    public function updateDiscountValue(Request $request)
    {
        $discount_value_id = $request->input('discount_value_id');
        $discountValue = Branch::find($discount_value_id);
        if ($discountValue) {
            $discountValue->max_discount_percentage = $request->max_discount_percentage;
            $discountValue->save();
            return redirect()->back()->with('success', 'Value update successfully');
        } else {
            return redirect()->back()->with('error', 'Value not update');
        }
    }

    public function deleteDiscountValue($id)
    {
        $discountValue = Branch::find($id);
        if ($discountValue) {
            $discountValue->max_discount_percentage = 20;
            $discountValue->save();
            return redirect()->back()->with('success', 'Value delete successfully');
        } else {
            return redirect()->back()->with('error', 'Value not delete');
        }
    }

    public function createOrderTypes(Request $request)
    {
        $branch_id = $request->input('branch_id');
        $newOrderType = new PaymentMethod();
        $newOrderType->order_type = $request->order_type;
        $newOrderType->branch_id = $branch_id;
        $newOrderType->save();

        return redirect()->back()->with('success', 'Order Type added successfully');
    }
    public function updateOrderTypes(Request $request)
    {
        $order_type_id = $request->input('order_type_id');
        $orderType = PaymentMethod::find($order_type_id);
        if ($orderType) {
            $orderType->order_type = $request->order_type;
            $orderType->save();
            return redirect()->back()->with('success', 'Order Type update successfully');
        } else {
            return redirect()->back()->with('error', 'Order Type not update');
        }
    }
    public function deleteOrderTypes($id)
    {
        $orderType = PaymentMethod::find($id);
        if ($orderType) {
            $orderType->delete();
            return redirect()->back()->with('success', 'Order Type delete successfully');
        } else {
            return redirect()->back()->with('error', 'Order Type not delete');
        }
    }

    public function createThemeSettings(Request $request)
    {
        $newSettings = new ThemeSetting();
        if ($request->hasFile('logoPic')) {
            $uploadedFile = $request->file('logoPic');
            $extension = $uploadedFile->getClientOriginalExtension();
            $imageName = time() . '.' . $extension;
            $uploadedFile->move(public_path('Images/Logos/'), $imageName);

            $newSettings->pos_logo = $imageName;
        }

        $newSettings->pos_primary_color = $request->primary_color;
        $newSettings->pos_secondary_color = $request->secondary_color;
        $newSettings->branch_id = $request->branch_id;
        $newSettings->save();
        if ($newSettings->save())
            return redirect()->back()->with('success', 'Settings Updated');
        else
            return redirect()->back()->with('success', 'Settings not Update');
    }
    public function updateThemeSettings(Request $request)
    {
        $setting_id = $request->input('setting_id');

        $setting = ThemeSetting::find($setting_id);
        if ($request->hasFile('logoPic')) {
            $existingImage = public_path("Images/Logos/$setting->pos_logo");
            File::delete($existingImage);

            $newImage = $request->file("logoPic");
            $extension = $newImage->getClientOriginalExtension();
            $newImageName = time() . "." . $extension;
            $newImage->move(public_path("Images/Logos"), $newImageName);
            $setting->pos_logo = $newImageName;
        }

        $setting->pos_primary_color = $request->primary_color;
        $setting->pos_secondary_color = $request->secondary_color;
        $setting->save();
        if ($setting->save())
            return redirect()->back()->with('success', 'Settings Updated');
        else
            return redirect()->back()->with('success', 'Settings not Update');
    }
    public function deleteThemeSettings($id)
    {
        $setting = ThemeSetting::find($id);
        if ($setting->delete()) {
            $image = public_path("Images/Logos/$setting->pos_logo");
            File::delete($image);
            return redirect()->back()->with('success', 'Setting Delete');
        } else
            return redirect()->back()->with('error', 'Settings Not Delete');
    }

    public function updateProfile(Request $request)
    {
        $user_id = $request->user_id;
        $updateProfile = User::find($user_id);
    
        // Handle profile picture update
        if ($request->hasFile('updated_profile_picture')) {
            $existingImage = public_path('Images/UsersImages') . '/' . $updateProfile->profile_picture;
            if (file_exists($existingImage)) {
                File::delete($existingImage);
            }
    
            $image = $request->file('updated_profile_picture');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('Images/UsersImages'), $imageName);
            $updateProfile->profile_picture = $imageName;
        }
    
        // Update name
        $updateProfile->name = $request->name;
    
        // Handle password update
        if ($request->filled('password')) {
            $request->validate([
                'password' => 'required|confirmed|min:8',
            ]);
    
            $updateProfile->password = bcrypt($request->password);
        }
    
        // Save the updated user information
        if ($updateProfile->save()) {
            return redirect()->back()->with('success', 'Profile Updated Successfully');
        } else {
            return redirect()->back()->with('error', 'Profile not updated');
        }
    }
    
    /*
    |---------------------------------------------------------------|
    |====================== Reports Functions ======================|
    |---------------------------------------------------------------|
    */

    public function viewReportPage($branch_id)
    {
        if (!session()->has('branchManager')) {
            return redirect()->route('viewLoginPage');
        }
        $settings = ThemeSetting::where('branch_id', $branch_id)->first();
        $order = Order::where('branch_id', $branch_id)
            ->with('items')
            ->get();
        $user = User::where('branch_id', $branch_id)->where('role', 'salesman')->get();

        return view('Manager/Report')->with([
            'totalOrders' => $order,
            'salesman' => $user,
            'ThemeSettings' => $settings,
            'dailyOrders' => null
        ]);
    }

    public function dayFullTransactionReport(Request $request)
    {
        $branch_id = $request->branch_id;
        $salesman_id = $request->salesman;
        $selectedDate = $request->transaction_report_date;

        $dailyOrders = Order::whereDate('created_at', $selectedDate)
            ->where('branch_id', $branch_id)
            ->where('salesman_id', $salesman_id)
            ->where('status', 1)
            ->with('items')
            ->get()
            ->transform(function ($order) use ($selectedDate) {
                $order->selected_date = $selectedDate;
                return $order;
            });
        if (!$dailyOrders->isEmpty()) {
            return redirect()->back()->with('dailyOrders', $dailyOrders);
        } else {
            return redirect()->back()->with('error', 'Data not found');
        }
    }
    public function printDailyFullTransactionReport($branch_id, $salesman_id, $selectedDate)
    {
        $pdfName = 'Day Full Transaction Report';
        $dailyOrders = Order::whereDate('created_at', $selectedDate)
            ->where('branch_id', $branch_id)
            ->where('salesman_id', $salesman_id)
            ->where('status', 1)
            ->with('items')->get()
            ->transform(function ($order) use ($selectedDate) {
                $order->selected_date = $selectedDate;
                return $order;
            });

        // return view('Reports.ReportTable', ['dailyOrders' => $dailyOrders]);
        $html = view('Reports.ReportTable', ['dailyOrders' => $dailyOrders])->render();
        $dompdf = new Dompdf();

        $dompdf->loadHtml($html);
        $width = 250 * 2.83465;
        $height = 297 * 2.83465;
        $dompdf->setPaper([0, 0, $width, $height], 'portrait');
        $dompdf->render();

        $output = $dompdf->output();
        $pdfFileName = $pdfName . '.pdf';
        $pdfFilePath = public_path('PDF/Reports/' . $pdfFileName);
        file_put_contents($pdfFilePath, $output);
        return redirect()->back()->with([
            'pdf_filename' => $pdfFileName
        ]);
    }

    public function salesAssistantTotalSalesReport(Request $request)
    {

        $branch_id = $request->branch_id;
        $report_start_date = $request->report_start_date;
        $report_end_date = $request->report_end_date;

        $dailyOrders = Order::where('created_at', '>=', $report_start_date)
            ->where('created_at', '<=', $report_end_date . ' 23:59:59')
            ->where('branch_id', $branch_id)
            ->whereIn('status', [1, 3])
            ->with('salesman')
            ->with('items')
            ->get();

        $report = [
            'report_start_date' => $report_start_date,
            'report_end_date' => $report_end_date,
            'sales_data' => []
        ];

        foreach ($dailyOrders as $order) {
            $Name = $order->salesman->name;
            $order_bill = (float) str_replace('Rs. ', '', $order->total_bill);
            $order_date = $order->created_at->toDateString();

            if (!isset($report['sales_data'][$Name])) {
                $report['sales_data'][$Name] = [];
            }

            if (!isset($report['sales_data'][$Name][$order_date])) {
                $report['sales_data'][$Name][$order_date] = [
                    'sales' => 0,
                    'refunds' => 0
                ];
            }

            if ($order->status == 1) {
                $report['sales_data'][$Name][$order_date]['sales'] += $order_bill;
            } elseif ($order->status == 3) {
                $report['sales_data'][$Name][$order_date]['refunds'] += $order_bill;
            }
        }
        if (collect($report['sales_data'])->isEmpty()) {
            return redirect()->back()->with('error', 'Data not found.');
        }
        return redirect()->back()->with('report', $report);
    }
    public function printSalesReport($branch_id, $start_date, $end_date)
    {

        $pdfName = 'Daily Sales Report';
        $dailyOrders = Order::where('created_at', '>=', $start_date)
            ->where('created_at', '<=', $end_date . ' 23:59:59')
            ->where('branch_id', $branch_id)
            ->whereIn('status', [1, 3])
            ->with('salesman')
            ->with('items')
            ->get();

        $report = [
            'report_start_date' => $start_date,
            'report_end_date' => $end_date,
            'sales_data' => []
        ];

        foreach ($dailyOrders as $order) {
            $salesmanName = $order->salesman->name;
            $order_bill = (float) str_replace('Rs. ', '', $order->total_bill);
            $order_date = $order->created_at->toDateString();

            if (!isset($report['sales_data'][$salesmanName])) {
                $report['sales_data'][$salesmanName] = [];
            }

            if (!isset($report['sales_data'][$salesmanName][$order_date])) {
                $report['sales_data'][$salesmanName][$order_date] = [
                    'sales' => 0,
                    'refunds' => 0
                ];
            }

            if ($order->status == 1) {
                $report['sales_data'][$salesmanName][$order_date]['sales'] += $order_bill;
            } elseif ($order->status == 3) {
                $report['sales_data'][$salesmanName][$order_date]['refunds'] += $order_bill;
            }
        }

        $html = view('Reports.SalesReportTable', ['report' => $report])->render();
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $output = $dompdf->output();
        $pdfFileName = $pdfName . '.pdf';
        $pdfFilePath = public_path('PDF/Reports/' . $pdfFileName);
        file_put_contents($pdfFilePath, $output);

        return redirect()->back()->with([
            'pdf_filename' => $pdfFileName
        ]);
    }

    public function tillReconcilationFigureByDate(Request $request)
    {
        $branch_id = $request->branch_id;
        $salesman_id = $request->salesman;
        $report_start_date = $request->report_start_date;
        $report_end_date = $request->report_end_date;

        $dailyOrders = Order::where('created_at', '>=', $report_start_date)
            ->where('created_at', '<=', $report_end_date . ' 23:59:59')
            ->where('branch_id', $branch_id)
            ->where('salesman_id', $salesman_id)
            ->where('status', 1)
            ->with('salesman')
            ->with('items')
            ->get();

        $dailyReconciliation = [
            'report_start_date' => $report_start_date,
            'report_end_date' => $report_end_date,
            'salesman_id' => $salesman_id,
            'total_sales' => 0,
            'sales_data' => []
        ];

        foreach ($dailyOrders as $order) {
            $salesmanName = $order->salesman->name;
            $order_bill = (float) str_replace('Rs. ', '', $order->total_bill);
            $order_date = $order->created_at->toDateString();

            if (!isset($dailyReconciliation['sales_data'][$salesmanName])) {
                $dailyReconciliation['sales_data'][$salesmanName] = [];
            }

            if (!isset($dailyReconciliation['sales_data'][$salesmanName][$order_date])) {
                $dailyReconciliation['sales_data'][$salesmanName][$order_date] = [
                    'sales' => 0,
                    'discount' => 0
                ];
            }

            if ($order->status == 1) {
                $dailyReconciliation['sales_data'][$salesmanName][$order_date]['sales'] += $order_bill;
                $dailyReconciliation['total_sales'] += $order_bill;
                $dailyReconciliation['sales_data'][$salesmanName][$order_date]['discount'] += $order->discount_type == '%' ? ($order_bill + $order->taxes) * ($order->discount / 100) : $order->discount;
            }

        }
        if ($dailyOrders->isEmpty()) {
            return redirect()->back()->with('error', 'Data Not Found');
        }
        return redirect()->back()->with('dailyReconciliation', $dailyReconciliation);
    }
    public function printSalesmanReconcilationReport($branch_id, $start_date, $end_date, $salesman_id)
    {
        $pdfName = 'Salesman Reconcilation Report';
        $dailyOrders = Order::where('created_at', '>=', $start_date)
            ->where('created_at', '<=', $end_date . ' 23:59:59')
            ->where('branch_id', $branch_id)
            ->where('salesman_id', $salesman_id)
            ->where('status', 1)
            ->with('salesman')
            ->with('items')
            ->get();

        $dailyReconciliation = [
            'report_start_date' => $start_date,
            'report_end_date' => $end_date,
            'salesman_id' => $salesman_id,
            'total_sales' => 0,
            'sales_data' => []
        ];

        foreach ($dailyOrders as $order) {
            $salesmanName = $order->salesman->name;
            $order_bill = (float) str_replace('Rs. ', '', $order->total_bill);
            $order_date = $order->created_at->toDateString();

            if (!isset($dailyReconciliation['sales_data'][$salesmanName])) {
                $dailyReconciliation['sales_data'][$salesmanName] = [];
            }

            if (!isset($dailyReconciliation['sales_data'][$salesmanName][$order_date])) {
                $dailyReconciliation['sales_data'][$salesmanName][$order_date] = [
                    'sales' => 0,
                    'discount' => 0
                ];
            }

            if ($order->status == 1) {
                $dailyReconciliation['sales_data'][$salesmanName][$order_date]['sales'] += $order_bill;
                $dailyReconciliation['total_sales'] += $order_bill;
                $dailyReconciliation['sales_data'][$salesmanName][$order_date]['discount'] += $order->discount_type == '%' ? ($order_bill + $order->taxes) * ($order->discount / 100) : $order->discount;
            }
        }

        $html = view('Reports.SalesmanReconcilationTable', ['dailyReconciliation' => $dailyReconciliation])->render();
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $output = $dompdf->output();
        $pdfFileName = $pdfName . '.pdf';
        $pdfFilePath = public_path('PDF/Reports/' . $pdfFileName);
        file_put_contents($pdfFilePath, $output);

        return redirect()->back()->with([
            'pdf_filename' => $pdfFileName
        ]);
    }

    public function soldProductsReport(Request $request)
    {
        $branch_id = $request->branch_id;
        $start_date = $request->transaction_start_date;
        $end_date = $request->transaction_end_date;

        $dailySales = Order::where('created_at', '>=', $start_date)
            ->where('created_at', '<=', $end_date . ' 23:59:59')
            ->where('branch_id', $branch_id)
            ->where('status', 1)
            ->with('salesman')
            ->with('items')
            ->get()
            ->transform(function ($order) use ($start_date) {
                $order->start_date = $start_date;
                return $order;
            })
            ->transform(function ($order) use ($end_date) {
                $order->end_date = $end_date;
                return $order;
            });
        if ($dailySales->isEmpty()) {
            return redirect()->back()->with('error', 'Data not found');
        }
        return redirect()->back()->with('dailySales', $dailySales);
    }
    public function printSoldProductsReport($branch_id, $start_date, $end_date)
    {
        $pdfName = 'Sold Products Report';

        $dailySales = Order::where('created_at', '>=', $start_date)
            ->where('created_at', '<=', $end_date . ' 23:59:59')
            ->where('branch_id', $branch_id)
            ->where('status', 1)
            ->with('salesman')
            ->with('items')
            ->get();

        // return view('Reports.SoldProductsReportTable', ['dailySales' => $dailySales]);
        $html = view('Reports.SoldProductsReportTable', ['dailySales' => $dailySales])->render();
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $output = $dompdf->output();
        $pdfFileName = $pdfName . '.pdf';
        $pdfFilePath = public_path('PDF/Reports/' . $pdfFileName);
        file_put_contents($pdfFilePath, $output);

        return redirect()->back()->with([
            'pdf_filename' => $pdfFileName
        ]);
    }

    public function stockHistoryReport($branch_id)
    {
        $stock_history = StockHistory::where('branch_id', $branch_id)->get();

        if ($stock_history->isEmpty()) {
            return redirect()->back()->with('error', 'Stock history not found');
        }
        $pdfName = 'Stock Reorder Report';

        $html = view('Reports.StockReorderReportTable', ['stock_history' => $stock_history])->render();
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $dompdf->stream($pdfName . '.pdf');
    }

    public function productsRefundReport(Request $request)
    {
        $branch_id = $request->branch_id;
        $start_date = $request->transaction_start_date;
        $end_date = $request->transaction_end_date;

        $salesRefund = Order::where('created_at', '>=', $start_date)
            ->where('created_at', '<=', $end_date . ' 23:59:59')
            ->where('branch_id', $branch_id)
            ->where('status', 3)
            ->with('salesman')
            ->with('items')
            ->get()
            ->transform(function ($order) use ($start_date) {
                $order->start_date = $start_date;
                return $order;
            })
            ->transform(function ($order) use ($end_date) {
                $order->end_date = $end_date;
                return $order;
            });

        if ($salesRefund->isEmpty()) {
            return redirect()->back()->with('error', "Data not found.");
        }
        return redirect()->back()->with('salesRefund', $salesRefund);
    }
    public function printProductsRefundReport($branch_id, $start_date, $end_date)
    {

        $pdfName = 'Products Refund Report';

        $salesRefund = Order::where('created_at', '>=', $start_date)
            ->where('created_at', '<=', $end_date . ' 23:59:59')
            ->where('branch_id', $branch_id)
            ->where('status', 3)
            ->with('salesman')
            ->with('items')
            ->get();

        $html = view('Reports.ProductsRefundReportTable', ['salesRefund' => $salesRefund])->render();
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $output = $dompdf->output();
        $pdfFileName = $pdfName . '.pdf';
        $pdfFilePath = public_path('PDF/Reports/' . $pdfFileName);
        file_put_contents($pdfFilePath, $output);

        return redirect()->back()->with([
            'pdf_filename' => $pdfFileName
        ]);
    }
    public function refundReport(Request $request)
    {

        $branch_id = $request->branch_id;
        $start_date = $request->transaction_start_date;
        $end_date = $request->transaction_end_date;

        $refunds = Order::where('created_at', '>=', $start_date)
            ->where('created_at', '<=', $end_date . ' 23:59:59')
            ->where('branch_id', $branch_id)
            ->where('status', 3)
            ->with('salesman')
            ->with('items')
            ->get();

        $refundsReport = [
            'report_start_date' => $start_date,
            'report_end_date' => $end_date,
            'total_refunds' => 0,
            'refunds_data' => []
        ];

        foreach ($refunds as $order) {
            $Name = $order->salesman->name;
            $order_bill = (float) str_replace('Rs. ', '', $order->total_bill);
            $order_date = $order->created_at->toDateString();

            if (!isset($refundsReport['refunds_data'][$Name])) {
                $refundsReport['refunds_data'][$Name] = [];
            }

            if (!isset($refundsReport['refunds_data'][$Name][$order_date])) {
                $refundsReport['refunds_data'][$Name][$order_date] = [
                    'refunds' => 0
                ];
            }

            if ($order->status == 3) {
                $refundsReport['refunds_data'][$Name][$order_date]['refunds'] += $order_bill;
                $refundsReport['total_refunds'] += $order_bill;
            }
        }
        if (collect($refundsReport['refunds_data'])->isEmpty()) {
            return redirect()->back()->with('error', 'Data not found.');
        }
        return redirect()->back()->with('refundsReport', $refundsReport);
    }
    public function printRefundReport($branch_id, $start_date, $end_date)
    {

        $pdfName = 'Refund Report';
        $refunds = Order::where('created_at', '>=', $start_date)
            ->where('created_at', '<=', $end_date . ' 23:59:59')
            ->where('branch_id', $branch_id)
            ->where('status', 3)
            ->with('salesman')
            ->with('items')
            ->get();

        $refundsReport = [
            'report_start_date' => $start_date,
            'report_end_date' => $end_date,
            'total_refunds' => 0,
            'refunds_data' => []
        ];

        foreach ($refunds as $order) {
            $Name = $order->salesman->name;
            $order_bill = (float) str_replace('Rs. ', '', $order->total_bill);
            $order_date = $order->created_at->toDateString();

            if (!isset($refundsReport['refunds_data'][$Name])) {
                $refundsReport['refunds_data'][$Name] = [];
            }

            if (!isset($refundsReport['refunds_data'][$Name][$order_date])) {
                $refundsReport['refunds_data'][$Name][$order_date] = [
                    'refunds' => 0
                ];
            }

            if ($order->status == 3) {
                $refundsReport['refunds_data'][$Name][$order_date]['refunds'] += $order_bill;
                $refundsReport['total_refunds'] += $order_bill;
            }
        }

        $html = view('Reports.RefundReportTable', ['refundsReport' => $refundsReport])->render();
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $output = $dompdf->output();
        $pdfFileName = $pdfName . '.pdf';
        $pdfFilePath = public_path('PDF/Reports/' . $pdfFileName);
        file_put_contents($pdfFilePath, $output);

        return redirect()->back()->with([
            'pdf_filename' => $pdfFileName
        ]);
    }

    public function taxReportByDate(Request $request)
    {

        $branch_id = $request->branch_id;

        $start_date = $request->report_start_date;
        $end_date = $request->report_end_date;

        $taxes = Order::where('created_at', '>=', $start_date)
            ->where('created_at', '<=', $end_date . ' 23:59:59')
            ->where('branch_id', $branch_id)
            ->where('status', 1)
            ->with('salesman')
            ->with('items')
            ->orderBy('created_at', 'asc')
            ->get();

        $taxReport = [
            'report_start_date' => $start_date,
            'report_end_date' => $end_date,
            'total_sales' => 0,
            'total_tax' => 0,
            'sales_data' => []
        ];

        foreach ($taxes as $tax) {
            $Name = $tax->salesman->name;
            $order_bill = (float) str_replace('Rs. ', '', $tax->total_bill);
            $order_tax = (float) str_replace('Rs. ', '', $tax->taxes);
            $order_date = $tax->created_at->toDateString();

            if (!isset($taxReport['sales_data'][$Name])) {
                $taxReport['sales_data'][$Name] = [];
            }

            if (!isset($taxReport['sales_data'][$Name][$order_date])) {
                $taxReport['sales_data'][$Name][$order_date] = [
                    'Sale' => 0,
                    'Tax' => 0
                ];
            }

            if ($tax->status == 1) {
                $taxReport['sales_data'][$Name][$order_date]['Sale'] += $order_bill;
                $taxReport['total_sales'] += $order_bill;

                $taxReport['sales_data'][$Name][$order_date]['Tax'] += $order_tax;
                $taxReport['total_tax'] += $order_tax;
            }
        }
        if (collect($taxReport['sales_data'])->isEmpty()) {
            return redirect()->back()->with('error', 'Data not found.');
        }
        return redirect()->back()->with('taxReport', $taxReport);
    }
    public function printTaxReportByDate($Branch_id, $Start_date, $End_date)
    {

        $pdfName = 'Daily Tax Report';
        $branch_id = $Branch_id;

        $start_date = $Start_date;
        $end_date = $End_date;

        $taxes = Order::where('created_at', '>=', $start_date)
            ->where('created_at', '<=', $end_date . ' 23:59:59')
            ->where('branch_id', $branch_id)
            ->where('status', 1)
            ->with('salesman')
            ->with('items')
            ->orderBy('created_at', 'asc')
            ->get();

        $taxReport = [
            'report_start_date' => $start_date,
            'report_end_date' => $end_date,
            'total_sales' => 0,
            'total_tax' => 0,
            'sales_data' => []
        ];

        foreach ($taxes as $tax) {
            $Name = $tax->salesman->name;
            $order_bill = (float) str_replace('Rs. ', '', $tax->total_bill);
            $order_tax = (float) str_replace('Rs. ', '', $tax->taxes);
            $order_date = $tax->created_at->toDateString();

            if (!isset($taxReport['sales_data'][$Name])) {
                $taxReport['sales_data'][$Name] = [];
            }

            if (!isset($taxReport['sales_data'][$Name][$order_date])) {
                $taxReport['sales_data'][$Name][$order_date] = [
                    'Sale' => 0,
                    'Tax' => 0
                ];
            }

            if ($tax->status == 1) {
                $taxReport['sales_data'][$Name][$order_date]['Sale'] += $order_bill;
                $taxReport['total_sales'] += $order_bill;

                $taxReport['sales_data'][$Name][$order_date]['Tax'] += $order_tax;
                $taxReport['total_tax'] += $order_tax;
            }
        }

        $html = view('Reports.DailyTaxReportTable', ['taxReport' => $taxReport])->render();
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $output = $dompdf->output();
        $pdfFileName = $pdfName . '.pdf';
        $pdfFilePath = public_path('PDF/Reports/' . $pdfFileName);
        file_put_contents($pdfFilePath, $output);

        return redirect()->back()->with([
            'pdf_filename' => $pdfFileName
        ]);
    }

    public function dailyTransactionTaxReport(Request $request)
    {
        $branch_id = $request->branch_id;
        $start_date = Carbon::parse($request->report_start_date)->startOfDay();
        $end_date = Carbon::parse($request->report_end_date)->endOfDay();

        $transactionTaxes = Order::where('created_at', '>=', $start_date)
            ->where('created_at', '<=', $end_date . ' 23:59:59')
            ->where('branch_id', $branch_id)
            ->where('status', 1)
            ->with('salesman')
            ->with('items')
            ->orderBy('created_at', 'asc')
            ->get()
            ->transform(function ($order) use ($start_date, $end_date) {
                $order->start_date = $start_date->toDateString();
                $order->end_date = $end_date->toDateString();
                return $order;
            });
        if ($transactionTaxes->isEmpty()) {
            return redirect()->back()->with('error', 'Data not found.');
        }
        return redirect()->back()->with('transactionTaxes', $transactionTaxes);
    }
    public function printDailyTransactionTaxReport($Branch_id, $Start_date, $End_date)
    {

        $pdfName = 'Daily Transaction Tax Report Table';
        $branch_id = $Branch_id;

        $start_date = $Start_date;
        $end_date = $End_date;

        $transactionTaxes = Order::where('created_at', '>=', $start_date)
            ->where('created_at', '<=', $end_date . ' 23:59:59')
            ->where('branch_id', $branch_id)
            ->where('status', 1)
            ->with('salesman')
            ->with('items')
            ->orderBy('created_at', 'asc')
            ->get();

        $html = view('Reports.DailyTransactionTaxReportTable', ['transactionTaxes' => $transactionTaxes])->render();
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $output = $dompdf->output();
        $pdfFileName = $pdfName . '.pdf';
        $pdfFilePath = public_path('PDF/Reports/' . $pdfFileName);
        file_put_contents($pdfFilePath, $output);

        return redirect()->back()->with([
            'pdf_filename' => $pdfFileName
        ]);
    }

    public function salesmanTaxReportByDate(Request $request)
    {
        $branch_id = $request->branch_id;
        $salesman_id = $request->salesman;
        $transaction_report_date = Carbon::parse($request->transaction_report_date)->startOfDay();

        $salesmanTaxReport = Order::whereDate('created_at', $transaction_report_date)
            ->where('branch_id', $branch_id)
            ->where('salesman_id', $salesman_id)
            ->where('status', 1)
            ->with('salesman')
            ->with('items')
            ->get()
            ->transform(function ($order) use ($transaction_report_date) {
                $order->transaction_date = $transaction_report_date->toDateString();
                return $order;
            });

        if ($salesmanTaxReport->isEmpty()) {
            return redirect()->back()->with('error', 'Data not found.');
        } else {
            return redirect()->back()->with('salesmanTaxReport', $salesmanTaxReport);
        }
    }
    public function printSalesmanTaxReportByDate($Branch_id, $Salesman_id, $transaction_date)
    {

        $pdfName = 'Daily Transaction Tax Report Table';
        $branch_id = $Branch_id;
        $salesman_id = $Salesman_id;
        $transaction_report_date = $transaction_date;

        $salesmanTaxReport = Order::whereDate('created_at', $transaction_report_date)
            ->where('branch_id', $branch_id)
            ->where('salesman_id', $salesman_id)
            ->where('status', 1)
            ->with('salesman')
            ->with('items')
            ->get();

        $html = view('Reports.SalesmanTaxReportTable', ['salesmanTaxReport' => $salesmanTaxReport])->render();
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $output = $dompdf->output();
        $pdfFileName = $pdfName . '.pdf';
        $pdfFilePath = public_path('PDF/Reports/' . $pdfFileName);
        file_put_contents($pdfFilePath, $output);

        return redirect()->back()->with([
            'pdf_filename' => $pdfFileName
        ]);
    }

    public function salesAndDiscountReportByDate(Request $request)
    {
        $branch_id = $request->branch_id;
        $salesman_id = $request->salesman;
        $transaction_report_date = Carbon::parse($request->transaction_report_date)->startOfDay();

        $salesmanDiscountReport = Order::whereDate('created_at', $transaction_report_date)
            ->where('branch_id', $branch_id)
            ->where('salesman_id', $salesman_id)
            ->where('status', 1)
            ->with('salesman')
            ->with('items')
            ->get()
            ->transform(function ($order) use ($transaction_report_date) {
                $order->transaction_date = $transaction_report_date->toDateString();
                return $order;
            });

        if ($salesmanDiscountReport->isEmpty()) {
            return redirect()->back()->with('error', 'Data not found.');
        } else {
            return redirect()->back()->with('salesmanDiscountReport', $salesmanDiscountReport);
        }
    }
    public function printSalesAndDiscountReportByDate($Branch_id, $Salesman_id, $transaction_date)
    {
        $pdfName = 'Discount Report By Date';
        $branch_id = $Branch_id;
        $salesman_id = $Salesman_id;
        $transaction_report_date = $transaction_date;

        $salesmanDiscountReport = Order::whereDate('created_at', $transaction_report_date)
            ->where('branch_id', $branch_id)
            ->where('salesman_id', $salesman_id)
            ->where('status', 1)
            ->with('salesman')
            ->with('items')
            ->get();

        $html = view('Reports.SalesmanDiscountReportTable', ['salesmanDiscountReport' => $salesmanDiscountReport])->render();
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $output = $dompdf->output();
        $pdfFileName = $pdfName . '.pdf';
        $pdfFilePath = public_path('PDF/Reports/' . $pdfFileName);
        file_put_contents($pdfFilePath, $output);

        return redirect()->back()->with([
            'pdf_filename' => $pdfFileName
        ]);
    }

    public function deleteReportPDF($file_name)
    {
        $filePath = public_path('PDF/Reports/' . $file_name);

        File::delete($filePath);
        return redirect()->back();
    }

    /*
    |---------------------------------------------------------------|
    |==================== Dashboard Functions ======================|
    |---------------------------------------------------------------|
    */

    public function totalCategories($branch_id)
    {
        $categories = Category::where('branch_id', $branch_id)->count();
        session(['totalCategories' => $categories]);
        return $categories;
    }
    public function totalProducts($branch_id)
    {
        $Products = Product::where('branch_id', $branch_id)->count();
        session(['totalProducts' => $Products]);
        return $Products;
    }
    public function totalstocks($branch_id)
    {
        $Stocks = Stock::where('branch_id', $branch_id)->count();
        session(['totalStocks' => $Stocks]);
        return $Stocks;
    }
    public function totalBranchRevenue($branch_id)
    {
        $year = Carbon::now()->year;
        $month = Carbon::now()->month;

        $dailyBranchRevenues = Order::where('status', 1)
            ->where('branch_id', $branch_id)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->get();


        $monthlyBranchRevenues = Order::where('status', 1)
            ->where('branch_id', $branch_id)
            ->whereYear('created_at', $year)
            ->get();

        $annualBranchRevenues = Order::where('status', 1)
            ->where('branch_id', $branch_id)
            ->get();

        $dailyRevenueArray = [];
        $monthlyRevenueArray = [];
        $annualRevenueArray = [];
        $totalRevenue = 0;

        foreach ($dailyBranchRevenues as $revenue) {
            $branch_id = $revenue->branch_id;
            $date = $revenue->created_at->format('Y-m-d');

            $revenueValue = (int) str_replace([' Pkr', 'Rs. ', ','], '', $revenue->total_bill);
            if (!isset($dailyRevenueArray[$branch_id])) {
                $dailyRevenueArray[$branch_id] = array_fill(0, Carbon::create($year, $month)->daysInMonth, 0);
            }
            $dayIndex = Carbon::parse($date)->day - 1;
            $dailyRevenueArray[$branch_id][$dayIndex] += $revenueValue;
            $totalRevenue += $revenueValue;
        }

        foreach ($monthlyBranchRevenues as $revenue) {
            $branch_id = $revenue->branch_id;
            $date = $revenue->created_at->format('Y-m');
            $revenueValue = (int) str_replace([' Pkr', 'Rs. ', ','], '', $revenue->total_bill);

            if (!isset($monthlyRevenueArray[$branch_id])) {
                $monthlyRevenueArray[$branch_id] = array_fill(0, 12, 0);
            }
            $monthIndex = Carbon::parse($date)->month - 1;
            $monthlyRevenueArray[$branch_id][$monthIndex] += $revenueValue;
        }

        foreach ($annualBranchRevenues as $revenue) {
            $branch_id = $revenue->branch_id;
            $yearKey = $revenue->created_at->format('Y');
            $revenueValue = (int) str_replace([' Pkr', 'Rs. ', ','], '', $revenue->total_bill);

            if (!isset($annualRevenueArray[$branch_id])) {
                $annualRevenueArray[$branch_id] = [];
            }

            if (!isset($annualRevenueArray[$branch_id][$yearKey])) {
                $annualRevenueArray[$branch_id][$yearKey] = 0;
            }

            $annualRevenueArray[$branch_id][$yearKey] += $revenueValue;
        }

        $minYear = Order::min(DB::raw('YEAR(created_at)'));
        $formattedTotalRevenue = $this->formatRevenue($totalRevenue);
        session(['totalRevenue' => $formattedTotalRevenue]);

        return [
            'dailyRevenueArray' => $dailyRevenueArray,
            'monthlyRevenueArray' => $monthlyRevenueArray,
            'annualRevenueArray' => $annualRevenueArray,
            'totalRevenue' => $formattedTotalRevenue,
            'year' => $year,
            'month' => $month,
            'minYear' => $minYear,
        ];
    }
    private function formatRevenue($revenue)
    {
        $abbreviations = array(
            12 => 'T', // Trillion
            9 => 'B', // Billion
            6 => 'M', // Million
            3 => 'k', // Thousand
        );
        foreach ($abbreviations as $exp => $abbr) {
            if ($revenue >= pow(10, $exp)) {
                return number_format($revenue / pow(10, $exp), 1) . $abbr;
            }
        }
        return $revenue;
    }
}
