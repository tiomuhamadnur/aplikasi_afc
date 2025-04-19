<?php

use App\Http\Controllers\admin\GoogleSpreadsheetController;
use App\Http\Controllers\api\GetDataController;
use App\Http\Controllers\api\MonitoringPermitController;
use App\Http\Controllers\api\MonitoringEquipmentController;
use App\Http\Controllers\mail\ExpiringPermitMailController;
use App\Http\Controllers\whatsapp\ExpiringPermitWhatsappController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(ExpiringPermitWhatsappController::class)->group(function () {
    Route::get('/notification/whatsapp/expiring-permit', 'notification')->name('api.whatsapp.expiring-permit.nofification');
});

Route::controller(MonitoringPermitController::class)->group(function () {
    Route::get('/monitoring-permit/update-status', 'update')->name('api.monitoring-permit.update');
});

Route::controller(ExpiringPermitMailController::class)->group(function () {
    Route::get('/notification/mail/expiring-permit', 'notification')->name('api.mail.expiring-permit.nofification');
});

Route::controller(MonitoringEquipmentController::class)->group(function () {
    Route::get('/monitoring-equipment/check-status', 'check_status')->name('api.monitoring-equipment.check');
});

Route::controller(GetDataController::class)->group(function () {
    Route::get('/get-data/monitoring-equipment', 'data_monitoring_equipment')->name('api.data.monitoring-equipment');
    Route::get('/get-data/equipment', 'data_equipment')->name('api.data.equipment');
    Route::get('/get-data/asset', 'data_asset')->name('api.data.asset');
    Route::get('/get-data/functional-location', 'data_functional_location')->name('api.data.functional_location');
});

Route::controller(GoogleSpreadsheetController::class)->group(function () {
    Route::get('/looker/store-failure', 'store')->name('api.failure.looker.sync');
    Route::get('/looker/store-budgeting', 'store_budgeting')->name('api.budgeting.looker.sync');
});
