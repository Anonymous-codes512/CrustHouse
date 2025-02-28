<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SyncController;

Route::post('/sync-branch-record', [SyncController::class, 'syncBranchRecord']);
Route::post('/sync-user-record', [SyncController::class, 'syncUserRecord']);
Route::post('/sync-rider-record', [SyncController::class, 'syncRiderRecord']);
Route::post('/sync-orderWithOrderItems-record', [SyncController::class, 'syncOrderWithOrderItems']);

Route::post('/sync-category-record', [SyncController::class, 'syncCategoryRecord']);
Route::post('/sync-product-record', [SyncController::class, 'syncProductRecord']);
Route::post('/sync-dealsWithDealsItems-record', [SyncController::class, 'syncDealsWithDealsItemsRecord']);
Route::post('/sync-stock-record', [SyncController::class, 'syncStockRecord']);
Route::post('/sync-recipe-record', [SyncController::class, 'syncRecipeRecord']);

Route::post('/sync-riders', [SyncController::class, 'syncRidersRecord']);
Route::post('/sync-other-record', [SyncController::class, 'syncOtherRecord']);

Route::post('/sync-record', [SyncController::class, 'syncRecord']);
