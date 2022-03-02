<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\LinkController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ParentController;
use App\Http\Controllers\Admin\PayoutController;
use App\Http\Controllers\Admin\RegionController;
use App\Http\Controllers\Admin\NetworkController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\WebsiteController;
use App\Http\Controllers\Admin\CurrencyController;
use App\Http\Controllers\Admin\ExternalWebsiteController;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register Admin routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "admin" middleware group. Now create something great!
|
*/

Route::prefix('/admin')->name('admin.')->middleware(['auth:api'])->group(function () {
    Route::apiResources([
        '/users'=> UserController::class,
        '/regions'=> RegionController::class,
        '/currencies'=> CurrencyController::class,
        '/parents'=> ParentController::class,
        '/networks'=> NetworkController::class,
        '/payouts'=> PayoutController::class,
        '/links'=> LinkController::class,
        '/internal-websites'=> WebsiteController::class,
        '/external-websites'=> ExternalWebsiteController::class,
        '/settings'=> SettingController::class,

    ]);

    // search user
    Route::post('/search-user', [UserController::class, 'searchUser'])->name('search-user');
    //map payout to task
    Route::post('/payouts/map-to-task', [PayoutController::class,'mapToTask'])->name('payout.map');
    //map link to task
    Route::post('/links/map-to-task', [LinkController::class,'mapToTask'])->name('link.map');

});








Route::fallback(function(){
    return response()->json([
        'message' => 'Page Not Found. If error persists, contact work@tvz.com'], 404);
});