<?php

use App\Http\Controllers\ManagerController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KitchenController;
use App\Http\Controllers\OnlineOrdersController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\SalesmanController;
use App\Http\Controllers\RiderController;
use App\Models\Rider;
use Illuminate\Support\Facades\Route;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

/*
|---------------------------------------------------------------|
|=======================Tachyon's Routes =======================|
|---------------------------------------------------------------|
*/

Route::get('/', function () {
    // return view('index');
    return redirect()->Route('onlineOrderPage');
})->name('welcome');

Route::get('/login', [AuthController::class, 'loginIndex'])->name('viewLoginPage');
Route::get('/viewRegisterPage', [AuthController::class, 'registrationIndex'])->name('viewRegisterPage');
Route::post('/storeRegistrationData', [AuthController::class, 'register'])->name('storeRegistrationData');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::get('/viewForgotPassword', [AuthController::class, 'viewForgotPassword'])->name('viewForgotPassword');
Route::get('/resetPasswordPage/{email}', [AuthController::class, 'resetPasswordPage'])->name('resetPasswordPage');
Route::post('/resetPassword', [AuthController::class, 'resetPassword'])->name('resetPassword');
Route::post('/forgotPassword', [AuthController::class, 'forgotPassword'])->name('forgotPassword');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|---------------------------------------------------------------|
|=======================Tachyon's Routes =======================|
|---------------------------------------------------------------|
*/

Route::get('/dashboard/{owner_id}', [OwnerController::class, 'viewOwnerDashboard'])->name('dashboard');
Route::post('/UpdateOwnerProfile', [OwnerController::class, 'UpdateOwnerProfile'])->name('UpdateOwnerProfile');
Route::post('/storeNewBranchData', [OwnerController::class, 'newBranch'])->name('storeNewBranchData');
Route::get('/deleteBranch/{branch_id}', [OwnerController::class, 'deleteBranch'])->name('deleteBranch');
Route::post('/updateBranches', [OwnerController::class, 'updateBranches'])->name('updateBranches');
Route::get('/showBranchStats/{branch_id}', [OwnerController::class, 'showBranchStats'])->name('showBranchStats');

/*
|---------------------------------------------------------------|
|======================= Manager's Routes ======================|
|---------------------------------------------------------------|
*/

Route::get('/managerdashboard/{id}/{branch_id}', [ManagerController::class, 'viewManagerDashboard'])->name('managerdashboard');
Route::get('/readNotification/{user_id}/{branch_id}/{id}', [ManagerController::class, 'readNotification'])->name('readNotification');
Route::get('/deleteNotification/{user_id}/{branch_id}/{id}', [ManagerController::class, 'deleteNotification'])->name('deleteNotification');
Route::get('/redirectNotification/{user_id}/{branch_id}/{id}', [ManagerController::class, 'redirectNotification'])->name('redirectNotification');

/*
|---------------------------------------------------------------|
|======================= Categories Routes =====================|
|---------------------------------------------------------------|
*/

Route::get('/viewCategoryPage/{id}/{branch_id}', [ManagerController::class, 'viewCategoryPage'])->name('viewCategoryPage');
Route::post('/createCategory', [ManagerController::class, 'createCategory'])->name('createCategory');
Route::post('/updateCategory', [ManagerController::class, 'updateCategory'])->name('updateCategory');
Route::get('/deleteCategory/{id}', [ManagerController::class, 'deleteCategory'])->name('deleteCategory');

/*
|---------------------------------------------------------------|
|======================= Products Routes =======================|
|---------------------------------------------------------------|
*/

Route::get('/viewProductPage/{id}/{branch_id}', [ManagerController::class, 'viewProductPage'])->name('viewProductPage');
Route::post('/createProduct', [ManagerController::class, 'createProduct'])->name('createProduct');
Route::post('/updateProduct', [ManagerController::class, 'updateProduct'])->name('updateProduct');
Route::get('/deleteProduct/{id}', [ManagerController::class, 'deleteProduct'])->name('deleteProduct');

/*
|---------------------------------------------------------------|
|========================== Deals Routes =======================|
|---------------------------------------------------------------|
*/

Route::get('/viewDealPage/{id}/{branch_id}', [ManagerController::class, 'viewDealPage'])->name('viewDealPage');
Route::post('/createDeal', [ManagerController::class, 'createDeal'])->name('createDeal');
Route::get('/viewDealProductsPage/{branch_id}', [ManagerController::class, 'viewDealProductsPage'])->name('viewDealProductsPage');
Route::post('/createDealProducts', [ManagerController::class, 'createDealProducts'])->name('createDealProducts');
Route::post('/updateDeal', [ManagerController::class, 'updateDeal'])->name('updateDeal');
Route::get('/viewUpdateDealProductsPage/{id}/{branch_id}', [ManagerController::class, 'viewUpdateDealProductsPage'])->name('viewUpdateDealProductsPage');
Route::post('/addDealProduct', [ManagerController::class, 'addDealProduct'])->name('addDealProduct');
Route::get('/deleteDeal/{id}', [ManagerController::class, 'deleteDeal'])->name('deleteDeal');
Route::get('/deleteDealProduct/{id}/{dId}/{user_id}/{branch_id}', [ManagerController::class, 'deleteDealProduct'])->name('deleteDealProduct');

/*
|---------------------------------------------------------------|
|========================= Stock Routes ========================|
|---------------------------------------------------------------|
*/

Route::get('/viewStockPage/{id}/{branch_id}', [ManagerController::class, 'viewStockPage'])->name('viewStockPage');
Route::post('/createStock', [ManagerController::class, 'createStock'])->name('createStock');
Route::post('/updateStock', [ManagerController::class, 'updateStock'])->name('updateStock');
Route::get('/deleteStock/{id}', [ManagerController::class, 'deleteStock'])->name('deleteStock');
Route::get('/viewStockHistory/{branch_id}/{user_id}', [ManagerController::class, 'stockHistory'])->name('viewStockHistory');

/*
|---------------------------------------------------------------|
|========================= Recipe Routes =======================|
|---------------------------------------------------------------|
*/

Route::get('/viewRecipePage/{id}/{branch_id}', [ManagerController::class, 'viewRecipePage'])->name('viewRecipePage');
Route::post('/createRecipe', [ManagerController::class, 'createRecipe'])->name('createRecipe');
Route::post('/editProductRecipe', [ManagerController::class, 'editProductRecipe'])->name('editProductRecipe');
Route::get('/viewProductRecipe/{category_id}/{product_id}/{user_id}/{branch_id}', [ManagerController::class, 'viewProductRecipe'])->name('viewProductRecipe');
Route::get('/deleteStockFromRecipe/{id}/{cid}/{pId}', [ManagerController::class, 'deleteStockFromRecipe'])->name('deleteStockFromRecipe');
Route::get('/showCategoryProducts/{category_id}/{branch_id}/{user_id}', [ManagerController::class, 'showCategoryProducts'])->name('showCategoryProducts');

/*
|---------------------------------------------------------------|
|==================== Orders And Staff Routes ==================|
|---------------------------------------------------------------|
*/

Route::get('/viewOrdersPage/{id}/{branch_id}', [ManagerController::class, 'viewOrdersPage'])->name('viewOrdersPage');
Route::get('/viewOrderProducts/{branch_id}/{order_id}', [ManagerController::class, 'viewOrderProducts'])->name('viewOrderProducts');
Route::get('/printrecipt/{order_id}', [ManagerController::class, 'printRecipt'])->name('printrecipt');
Route::get('/cancelorder/{order_id}/{staff_id}', [ManagerController::class, 'cancelOrder'])->name('cancelorder');
Route::get('/viewStaffPage/{id}/{branch_id}', [ManagerController::class, 'viewStaffPage'])->name('viewStaffPage');
Route::post('/updateStaff', [ManagerController::class, 'updateStaff'])->name('updateStaff');
Route::get('/deleteStaff/{id}', [ManagerController::class, 'deleteStaff'])->name('deleteStaff');

Route::get('/download-pdf/{filename}', [SalesmanController::class, 'downloadPdf'])->name('downloadPdf');

/*
|---------------------------------------------------------------|
|======================== Dine-In Routes =======================|
|---------------------------------------------------------------|
*/
Route::get('viewDineInPage/{branch_id}', [ManagerController::class, 'viewDineInPage'])->name('viewDineInPage');
Route::post('createTable', [ManagerController::class, 'createTable'])->name('createTable');
Route::post('updateTable', [ManagerController::class, 'updateTable'])->name('updateTable');
Route::get('deleteTable/{table_id}', [ManagerController::class, 'deleteTable'])->name('deleteTable');

/*
|---------------------------------------------------------------|
|======================= Settings Routes =======================|
|---------------------------------------------------------------|
*/

Route::get('/viewSettingsPage/{id}/{branch_id}', [ManagerController::class, 'viewSettingsPage'])->name('viewSettingsPage');

Route::post('/createTax', [ManagerController::class, 'createTax'])->name('createTax');
Route::post('/updateTax', [ManagerController::class, 'updateTax'])->name('updateTax');
Route::get('/deleteTax/{id}', [ManagerController::class, 'deleteTax'])->name('deleteTax');

Route::post('/createDiscount', [ManagerController::class, 'createDiscount'])->name('createDiscount');
Route::post('/updateDiscount', [ManagerController::class, 'updateDiscount'])->name('updateDiscount');
Route::get('/deleteDiscount/{id}', [ManagerController::class, 'deleteDiscount'])->name('deleteDiscount');

Route::post('/createReceipt', [ManagerController::class, 'createReceipt'])->name('createReceipt');
Route::post('/updateReceipt', [ManagerController::class, 'updateReceipt'])->name('updateReceipt');
Route::get('/deleteReceipt/{id}', [ManagerController::class, 'deleteReceipt'])->name('deleteReceipt');

Route::post('/createPaymentMethod', [ManagerController::class, 'createPaymentMethod'])->name('createPaymentMethod');
Route::post('/updatePaymentMethod', [ManagerController::class, 'updatePaymentMethod'])->name('updatePaymentMethod');
Route::get('/deletePaymentMethod/{id}', [ManagerController::class, 'deletePaymentMethod'])->name('deletePaymentMethod');

Route::post('/createDiscountTypes', [ManagerController::class, 'createDiscountTypes'])->name('createDiscountTypes');
Route::post('/updateDiscountTypes', [ManagerController::class, 'updateDiscountTypes'])->name('updateDiscountTypes');
Route::get('/deleteDiscountTypes/{id}', [ManagerController::class, 'deleteDiscountTypes'])->name('deleteDiscountTypes');

Route::post('/createOrderTypes', [ManagerController::class, 'createOrderTypes'])->name('createOrderTypes');
Route::post('/updateOrderTypes', [ManagerController::class, 'updateOrderTypes'])->name('updateOrderTypes');
Route::get('/deleteOrderTypes/{id}', [ManagerController::class, 'deleteOrderTypes'])->name('deleteOrderTypes');

Route::post('/createDiscountValue', [ManagerController::class, 'createDiscountValue'])->name('createDiscountValue');
Route::post('/updateDiscountValue', [ManagerController::class, 'updateDiscountValue'])->name('updateDiscountValue');
Route::get('/deleteDiscountValue/{id}', [ManagerController::class, 'deleteDiscountValue'])->name('deleteDiscountValue');

Route::post('/createThemeSettings', [ManagerController::class, 'createThemeSettings'])->name('createThemeSettings');
Route::post('/updateThemeSettings', [ManagerController::class, 'updateThemeSettings'])->name('updateThemeSettings');
Route::get('/deleteThemeSettings/{id}', [ManagerController::class, 'deleteThemeSettings'])->name('deleteThemeSettings');

Route::post('/updateProfile', [ManagerController::class, 'updateProfile'])->name('updateProfile');
/*
|---------------------------------------------------------------|
|======================= Reports Routes ========================|
|---------------------------------------------------------------|
*/

Route::get('/report/{branch_id}', [ManagerController::class, 'viewReportPage'])->name('report');
Route::get('/todayTotalSales/{branch_id}', [ManagerController::class, 'todayTotalSales'])->name('todayTotalSales');

Route::post('/dayFullTransactionReport', [ManagerController::class, 'dayFullTransactionReport'])->name('dayFullTransactionReport');
Route::get('/printDailyReport/{branch_id}/{salesman_id}/{selectedDate}', [ManagerController::class, 'printDailyFullTransactionReport'])->name('printDailyReport');

Route::post('/salesAssistantTotalSalesReport', [ManagerController::class, 'salesAssistantTotalSalesReport'])->name('salesAssistantTotalSalesReport');
Route::get('/printSalesReport/{branch_id}/{start_date}/{end_date}', [ManagerController::class, 'printSalesReport'])->name('printSalesReport');

Route::post('/tillReconcilationFigureByDate', [ManagerController::class, 'tillReconcilationFigureByDate'])->name('tillReconcilationFigureByDate');
Route::get('/printSalesmanReconcilationReport/{branch_id}/{start_date}/{end_date}/{salesman_id}', [ManagerController::class, 'printSalesmanReconcilationReport'])->name('printSalesmanReconcilationReport');

Route::post('/soldProductsReport', [ManagerController::class, 'soldProductsReport'])->name('soldProductsReport');
Route::get('/printSoldProductsReport/{branch_id}/{start_date}/{end_date}', [ManagerController::class, 'printSoldProductsReport'])->name('printSoldProductsReport');

Route::get('/stockHistoryReport/{branch_id}', [ManagerController::class, 'stockHistoryReport'])->name('stockHistoryReport');

Route::post('/productsRefundReport', [ManagerController::class, 'productsRefundReport'])->name('productsRefundReport');
Route::get('/printProductsRefundReport/{branch_id}/{start_date}/{end_date}', [ManagerController::class, 'printProductsRefundReport'])->name('printProductsRefundReport');

Route::post('/refundReport', [ManagerController::class, 'refundReport'])->name('refundReport');
Route::get('/printRefundReport/{branch_id}/{start_date}/{end_date}', [ManagerController::class, 'printRefundReport'])->name('printRefundReport');

Route::post('/taxReportByDate', [ManagerController::class, 'taxReportByDate'])->name('taxReportByDate');
Route::get('/printTaxReportByDate/{branch_id}/{start_date}/{end_date}', [ManagerController::class, 'printTaxReportByDate'])->name('printTaxReportByDate');

Route::post('/dailyTransactionTaxReport', [ManagerController::class, 'dailyTransactionTaxReport'])->name('dailyTransactionTaxReport');
Route::get('/printDailyTransactionTaxReport/{branch_id}/{start_date}/{end_date}', [ManagerController::class, 'printDailyTransactionTaxReport'])->name('printDailyTransactionTaxReport');

Route::post('/salesmanTaxReportByDate', [ManagerController::class, 'salesmanTaxReportByDate'])->name('salesmanTaxReportByDate');
Route::get('/printSalesmanTaxReportByDate/{branch_id}/{salesman_id}/{transaction_date}', [ManagerController::class, 'printSalesmanTaxReportByDate'])->name('printSalesmanTaxReportByDate');

Route::post('/salesAndDiscountReportByDate', [ManagerController::class, 'salesAndDiscountReportByDate'])->name('salesAndDiscountReportByDate');
Route::get('/printSalesAndDiscountReportByDate/{branch_id}/{salesman_id}/{transaction_date}', [ManagerController::class, 'printSalesAndDiscountReportByDate'])->name('printSalesAndDiscountReportByDate');

Route::get('/deleteReportPDF/{file_name}', [ManagerController::class, 'deleteReportPDF'])->name('deleteReportPDF');

/*
|---------------------------------------------------------------|
|==================== Sales Man Routes =========================|
|---------------------------------------------------------------|
*/

// Route::get('salesman/dashboard/{id}/{branch_id}', [SalesmanController::class,'viewSalesmanDashboard'])->name('salesman_dashboard')->middleware('log.execution');;
Route::get('salesman/dashboard/{id}/{branch_id}', [SalesmanController::class, 'viewSalesmanDashboard'])->name('salesman_dashboard');
Route::get('salesman/dashboard/{categoryName}/{id}/{branch_id}', [SalesmanController::class, 'salesmanCategoryDashboard'])->name('salesman_dash');
Route::get('salesman/deals/', [SalesmanController::class, 'deals'])->name('deals');
Route::post('salesman/placeOrder/{salesman_id}', [SalesmanController::class, 'placeOrder'])->name('placeOrder');
Route::post('salesman/saveToCart', [SalesmanController::class, 'saveToCart'])->name('saveToCart');
Route::get('salesman/clearCart/{salesman_id}', [SalesmanController::class, 'clearCart'])->name('clearCart');
Route::get('salesman/removeOneProduct/{id}/{salesman_id}/{branch_id}', [SalesmanController::class, 'removeOneProduct'])->name('removeOneProduct');
Route::get('salesman/increaseQuantity/{id}/{salesman_id}/{branch_id}', [SalesmanController::class, 'increaseQuantity'])->name('increaseQuantity');
Route::get('salesman/decreaseQuantity/{id}/{salesman_id}/{branch_id}', [SalesmanController::class, 'decreaseQuantity'])->name('decreaseQuantity');
Route::get('salesman/deleteReceiptPDF/{file_name}', [SalesmanController::class, 'deleteReceiptPDF'])->name('deleteReceiptPDF');
Route::get('salesman/confirmOnlineOrder/{branch_id}/{salesman_id}/{order_id}', [SalesmanController::class, 'confirmOnlineOrder'])->name('confirmOnlineOrder');
Route::get('salesman/assignToRider/{branch_id}/{staff_id}/{order_id}', [SalesmanController::class, 'assignToRider'])->name('assignToRider');
Route::get('salesman/assignOrder/{branch_id}/{rider_id}/{order_id}/{salesman_id}', [SalesmanController::class, 'assignOrder'])->name('assignOrder');

Route::get('salesman/addNewProductToDineInOrder/{order_Number}/{table_id}', [SalesmanController::class, 'addNewProductToDineInOrder'])->name('addNewProductToDineInOrder');
// Route::get('salesman/finalizeDineInOrder/{order_Number}/{table_id}', [SalesmanController::class,'finalizeDineInOrder'])->name('finalizeDineInOrder');

// Route::post('/sendNotification/{message}', [SalesmanController::class, 'sendNotification'])->name('sendNotification');
Route::get('/getNotificationData', [SalesmanController::class, 'getNotificationData'])->name('getNotificationData');
Route::delete('/deleteOnlineNotification/{id}', [SalesmanController::class, 'deleteNotification'])->name('deleteOnlineNotification');
Route::get('/search', [SalesmanController::class, 'search'])->name('search');
Route::get('/cancelorderbysalesman/{order_id}/{staff_id}/{branch_id}/{reason}', [salesmanController::class, 'cancelOrder'])->name('cancelorderbysalesman');

/*
|---------------------------------------------------------------|
|====================== Kitchen Routes =========================|
|---------------------------------------------------------------|
*/

Route::get('chef/dashboard/{user_id}/{branch_id}', [KitchenController::class, 'viewChefDashboard'])->name('chef_dashboard');
Route::get('chef/orderComplete/{order_id}', [KitchenController::class, 'orderComplete'])->name('orderComplete');
Route::get('chef/printChefRecipt/{order_id}', [KitchenController::class, 'printChefRecipt'])->name('printChefRecipt');
Route::get('chef/getOrderNotification', [KitchenController::class, 'getOrderNotification'])->name('getOrderNotification');

/*
|---------------------------------------------------------------|
|================== Online Ordering Routes =====================|
|---------------------------------------------------------------|
*/

Route::get('/online', [OnlineOrdersController::class, 'viewOnlinePage'])->name('onlineOrderPage');
Route::post('/customerSignUp', [OnlineOrdersController::class, 'customerSignup'])->name('customerSignup');
Route::post('/customerLogin', [OnlineOrdersController::class, 'customerLogin'])->name('customerLogin');
Route::get('/customerEmailConfirmation/{token}', [OnlineOrdersController::class, 'customerEmailConfirmation'])->name('customerEmailConfirmation');
Route::post('/addToCart', [OnlineOrdersController::class, 'addToCart'])->name('addToCart');
Route::get('/registeredCustomer', [OnlineOrdersController::class, 'registeredCustomer'])->name('registeredCustomer');
Route::get('/profile/{email}', [OnlineOrdersController::class, 'Profile'])->name('profile');
Route::post('/updateCustomerProfile', [OnlineOrdersController::class, 'updateCustomerProfile'])->name('updateCustomerProfile');
Route::get('/deleteCustomer/{customer_id}', [OnlineOrdersController::class, 'deleteCustomer'])->name('deleteCustomer');
Route::get('/customerForgotPassword', [OnlineOrdersController::class, 'customerForgotPassword'])->name('customerForgotPassword');
Route::post('/customerSendPasswordReset', [OnlineOrdersController::class, 'customerSendPasswordReset'])->name('customerSendPasswordReset');
Route::get('/customerResetPasswordPage/{email}', [OnlineOrdersController::class, 'customerResetPasswordPage'])->name('customerResetPasswordPage');
Route::post('/customerResetPassword', [OnlineOrdersController::class, 'customerResetPassword'])->name('customerResetPassword');
Route::get('/payment-success', [OnlineOrdersController::class, 'placeOrder'])->name('payment.success');
Route::get('/payment-cancel', [OnlineOrdersController::class, 'paymentCancel'])->name('payment.cancel');
// Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook']);

/*
|---------------------------------------------------------------|
|======================== Rider Routes =========================|
|---------------------------------------------------------------|
*/

Route::get('rider_dashboard/{rider_id}/{branch_id}', [RiderController::class, 'viewDashboard'])->name('rider_dashboard');
Route::get('rider_orders/{rider_id}/{branch_id}', [RiderController::class, 'viewOrders'])->name('riderOrders');
Route::get('rider_profile/{rider_id}/{branch_id}', [RiderController::class, 'viewProfile'])->name('riderProfile');
Route::post('Update_profile_picture', [RiderController::class, 'updateProfilePicture'])->name('updateProfilePicture');
Route::post('Update_rider_profile', [RiderController::class, 'updateProfile'])->name('updateRiderProfile');
Route::get('view_order_details/{order_number}/{rider_id}', [RiderController::class, 'viewOrderDetails'])->name('viewOrderDetails');
Route::get('delivery_cancelled/{order_number}/{rider_id}', [RiderController::class, 'deliveryCancelled'])->name('deliveryCancelled');
Route::get('delivery_completed/{order_number}/{rider_id}', [RiderController::class, 'deliveryCompleted'])->name('deliveryCompleted');
Route::get('mark_as_read/{order_number}', [RiderController::class, 'markAsRead'])->name('markAsRead');


/*
|---------------------------------------------------------------|
|========================= Sync Routes =========================|
|---------------------------------------------------------------|
*/


Route::post('/sync-with-remote-db', function () {
    $phpPath = (new PhpExecutableFinder)->find();
    $artisanPath = base_path('artisan');

    $process = new Process([$phpPath, $artisanPath, 'sync:data']);
    $process->setTimeout(3600); // Set timeout (in seconds) for long-running processes

    $output = '';
    $process->start();

    $process->wait(function ($type, $line) use (&$output) {
        $output .= $line; // Append each line to the output
    });

    return response()->json(['output' => $output]);
});

Route::post('/sync-user-data', function () {
    $phpPath = (new PhpExecutableFinder)->find();
    $artisanPath = base_path('artisan');

    $process = new Process([$phpPath, $artisanPath, 'sync:userData']);
    $process->setTimeout(180);
    $output = '';
    $process->start();

    $process->wait(function ($type, $line) use (&$output) {
        $output .= $line . PHP_EOL;
    });
    return response()->json(['output' => $output]);
});

Route::post('/sync-category-data', function () {
    $phpPath = (new PhpExecutableFinder)->find();
    $artisanPath = base_path('artisan');

    $process = new Process([$phpPath, $artisanPath, 'sync:categoryData']);
    $process->setTimeout(180);
    $output = '';
    $process->start();

    $process->wait(function ($type, $line) use (&$output) {
        $output .= $line . PHP_EOL;
    });
    return response()->json(['output' => $output]);
});

Route::post('/sync-product-data', function () {
    $phpPath = (new PhpExecutableFinder)->find();
    $artisanPath = base_path('artisan');

    $process = new Process([$phpPath, $artisanPath, 'sync:productData']);
    $process->setTimeout(180);
    $output = '';
    $process->start();

    $process->wait(function ($type, $line) use (&$output) {
        $output .= $line . PHP_EOL;
    });
    return response()->json(['output' => $output]);
});

Route::post('/sync-deals-data', function () {
    $phpPath = (new PhpExecutableFinder)->find();
    $artisanPath = base_path('artisan');

    $process = new Process([$phpPath, $artisanPath, 'sync:dealData']);
    $process->setTimeout(180);
    $output = '';
    $process->start();

    $process->wait(function ($type, $line) use (&$output) {
        $output .= $line . PHP_EOL;
    });
    return response()->json(['output' => $output]);
});

Route::post('/sync-recipe-data', function () {
    $phpPath = (new PhpExecutableFinder)->find();
    $artisanPath = base_path('artisan');

    $process = new Process([$phpPath, $artisanPath, 'sync:recipeData']);
    $process->setTimeout(180);
    $output = '';
    $process->start();

    $process->wait(function ($type, $line) use (&$output) {
        $output .= $line . PHP_EOL;
    });
    return response()->json(['output' => $output]);
});

Route::post('/sync-stock-data', function () {
    $phpPath = (new PhpExecutableFinder)->find();
    $artisanPath = base_path('artisan');

    $process = new Process([$phpPath, $artisanPath, 'sync:stockData']);
    $process->setTimeout(180);
    $output = '';
    $process->start();

    $process->wait(function ($type, $line) use (&$output) {
        $output .= $line . PHP_EOL;
    });
    return response()->json(['output' => $output]);
});

Route::post('/sync-rider-data', function () {
    $phpPath = (new PhpExecutableFinder)->find();
    $artisanPath = base_path('artisan');

    $process = new Process([$phpPath, $artisanPath, 'sync:riderData']);
    $process->setTimeout(180);
    $output = '';
    $process->start();

    $process->wait(function ($type, $line) use (&$output) {
        $output .= $line . PHP_EOL;
    });
    return response()->json(['output' => $output]);
});

Route::post('/sync-other-data', function () {
    $phpPath = (new PhpExecutableFinder)->find();
    $artisanPath = base_path('artisan');

    $process = new Process([$phpPath, $artisanPath, 'sync:otherData']);
    $process->setTimeout(180);
    $output = '';
    $process->start();

    $process->wait(function ($type, $line) use (&$output) {
        $output .= $line . PHP_EOL;
    });
    return response()->json(['output' => $output]);
});
