<?php

use App\Http\Controllers\Client\BitsgoldController;
use App\Http\Controllers\Admin\TotalRevenueController;
use App\Http\Controllers\Client\KycCustomerController;

use App\Http\Controllers\Admin\KycController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\KycLogController;
use App\Http\Controllers\Admin\ReferralController;
use App\Http\Controllers\Admin\ReferralCommissionController;
use Botble\Marketplace\Http\Middleware\LocaleMiddleware;


use App\Http\Controllers\RegisterReferralController;
use App\Http\Controllers\GhnController;
use App\Models\CustomerNotification;
use GPBMetadata\Google\Api\Http;
use Illuminate\Support\Facades\Route;

Route::resource('ghn', GhnController::class);
Route::put('ghn/update', [GhnController::class, 'update'])->name('ghn.update');
Route::post('/ghn/update-session', [GhnController::class, 'updateSession'])->name('ghn.update-sesion');
Route::post('/ghn/update-shipment/{id}', [GhnController::class, 'updateShipment'])->name('ghn.update-shipment');
Route::post('/ghn/cancel-order/{id}', [GhnController::class, 'cancelOrder'])->name('ghn.cancel-order');

Route::prefix('/marketing')
  ->controller(BitsgoldController::class)
  ->middleware(LocaleMiddleware::class)
  ->group(function () {
    Route::get('dashboard', 'dashboard')->name('bitsgold.dashboard');
    Route::get('plan', 'plan')->name('bitsgold.plan');
    Route::get('invest-history', 'invest_history')->name('bitsgold.invest_history');
    Route::get('add-fund', 'add_fund')->name('bitsgold.add_fund');
    Route::get('transaction', 'transaction')->name('bitsgold.transaction');
    Route::get('referral', 'referral')->name('bitsgold.referral');
    Route::get('referral-bonus', 'referral_bonus')->name('bitsgold.referral_bonus');
  });

// -----------KYC--------
Route::prefix('admin/kyc')->group(function () {
  Route::get('/identity-form', [KycController::class, 'identityForm'])->name('kyc.form');
  Route::put('update',[KycController::class, 'update'])->name('address.update');
  Route::post('/identity-form', [KycController::class, 'storeIdentityForm'])->name('kyc.form.create');
  Route::put('/identity-form/{id}', [KycController::class, 'updateIdentityForm'])->name('kyc.form.update');
  Route::delete('/identity-form/{id}', [KycController::class, 'deleteIdentityForm'])->name('kyc.form.delete');
  Route::get('/pending', [KycController::class, 'showPending'])->name('kyc.pending');
  Route::get('/log', [KycController::class, 'logs'])->name('kyc.log');
  Route::get('kyc/log/{id}', [KycController::class, 'view'])->name('kyc.log.view');
  Route::get('pending/view/{id}',[KycController::class,'pendingview'])->name('kyc.pending.view');
  Route::patch('pending/approve/{id}',[KycController::class,'pendingapprove'])->name('kyc.pending.approve');
  Route::patch('pending/reject/{id}',[KycController::class,'pendingreject'])->name('kyc.pending.reject');
});

Route::get('/admin/customers/{id}/edit-rank', [App\Http\Controllers\Admin\AdminController::class, 'editCustomerRank'])->name('customer.edit.rank');
Route::post('/admin/customers/{id}/update-rank', [App\Http\Controllers\Admin\AdminController::class, 'updateCustomerRank'])->name('customer.update.rank');
Route::post('/admin/customers/store-rank', [App\Http\Controllers\Admin\AdminController::class, 'storeCustomerRank'])->name('customer.store.rank');

Route::prefix('/kyc')
  ->controller(KycCustomerController::class)
  ->middleware(LocaleMiddleware::class)
  ->group(function () {

    Route::get('','index')->name('kyc.index');
    Route::post('/submit','submit')->name('kyc.submit');
  });

Route::prefix('admin/ranks')->group(function () {
    Route::get('/add', [AdminController::class, 'addranks'])->name('rank.add');
    Route::get('/', [AdminController::class, 'indexranks'])->name('rank.index');
    Route::post('/store', [AdminController::class, 'storeranks'])->name('rank.store');
    Route::get('/edit/{id}', [AdminController::class, 'editranks'])->name('rank.edit');
    Route::put('/update/{id}', [AdminController::class, 'updateranks'])->name('rank.update');
    Route::delete('/delete/{id}', [AdminController::class, 'deleteranks'])->name('rank.delete');
    Route::post('/update-dayofsharing', [AdminController::class, 'updateDayOfSharing'])->name('admin.ranks.update.dayofsharing');

});

Route::get('bitsgold');

Route::prefix('admin/referralcommission')->group(function (){
        Route::get('/',[ReferralCommissionController::class, 'index'])->name('referralcommission.index');
        Route::get('/edit',[ReferralCommissionController::class, 'editreferral'])->name('referralcommission.edit');
        Route::put('/update',[ReferralCommissionController::class, 'update'])->name('referralcommission.update');
});
Route::prefix('admin/totalrevenue')->group(function () {
  Route::get('/', [TotalRevenueController::class, 'index'])->name('totalrevenue.index');
  Route::get('/add', [TotalRevenueController::class, 'add'])->name('totalrevenue.add');
  Route::post('/store', [TotalRevenueController::class, 'store'])->name('totalrevenue.store');
  Route::get('/edit/{id}', [TotalRevenueController::class, 'edit'])->name('totalrevenue.edit');
  Route::put('/update/{id}', [TotalRevenueController::class, 'update'])->name('totalrevenue.update');
  Route::delete('/delete/{id}', [TotalRevenueController::class, 'destroy'])->name('totalrevenue.delete');
});

Route::prefix('admin/referral')->group(function (){
  Route::get('/', [ReferralController::class, 'index'])->name('referral.index');
  Route::post('/save', [ReferralController::class, 'save'])->name('referral.save');
  Route::post('/action', [ReferralController::class, 'action'])->name('referral.action');
});

Route::get('/register/{username}', [RegisterReferralController::class, 'showRegistrationForm'])->name('register.referral.get');

Route::get('/notifications/latest', function () {
  $customerCheck = auth('customer')->check();
  $customer = auth('customer')->user();
  if (!$customerCheck) {
    return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
  }

  $latestNotification = CustomerNotification::where('customer_id', $customer->id)
    ->where('viewed', 0)
    ->latest()
    ->first();

  if ($latestNotification) {
    $latestNotification->update(['viewed' => 1]); // Cập nhật thành đã xem
  }
  return response()->json([
    'status' => 'success',
    'notification' => $latestNotification
  ]);
});
