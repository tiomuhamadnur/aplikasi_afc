<?php

use App\Http\Controllers\admin\ArahController;
use App\Http\Controllers\admin\BarangController;
use App\Http\Controllers\admin\DashboardController;
use App\Http\Controllers\admin\DepartemenController;
use App\Http\Controllers\admin\DetailLokasiController;
use App\Http\Controllers\admin\DirektoratController;
use App\Http\Controllers\admin\DivisiController;
use App\Http\Controllers\admin\EquipmentController;
use App\Http\Controllers\admin\JabatanController;
use App\Http\Controllers\admin\LokasiController;
use App\Http\Controllers\admin\RelasiAreaController;
use App\Http\Controllers\admin\RelasiStrukturController;
use App\Http\Controllers\admin\RoleController;
use App\Http\Controllers\admin\SatuanController;
use App\Http\Controllers\admin\SeksiController;
use App\Http\Controllers\admin\SubLokasiController;
use App\Http\Controllers\admin\TipeBarangController;
use App\Http\Controllers\admin\TipeEquipmentController;
use App\Http\Controllers\admin\TipePekerjaanController;
use App\Http\Controllers\admin\TipePermitController;
use App\Http\Controllers\user\MonitoringPermitController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/', function () {
    return redirect()->route('login');
})->middleware('auth');

Route::get('/', function () {
    return redirect()->route('dashboard.index');
})->middleware('auth');

Route::group(['middleware' => 'auth'], function () {
    Route::controller(DashboardController::class)->group(function () {
        Route::get('/dashboard', 'index')->name('dashboard.index');
    });

    Route::controller(LokasiController::class)->group(function () {
        Route::get('/lokasi', 'index')->name('lokasi.index');
        Route::post('/lokasi', 'store')->name('lokasi.store');
        Route::put('/lokasi', 'update')->name('lokasi.update');
        Route::delete('/lokasi', 'destroy')->name('lokasi.delete');
    });

    Route::controller(SubLokasiController::class)->group(function () {
        Route::get('/sub-lokasi', 'index')->name('sub-lokasi.index');
        Route::post('/sub-lokasi', 'store')->name('sub-lokasi.store');
        Route::put('/sub-lokasi', 'update')->name('sub-lokasi.update');
        Route::delete('/sub-lokasi', 'destroy')->name('sub-lokasi.delete');
    });

    Route::controller(DetailLokasiController::class)->group(function () {
        Route::get('/detail-lokasi', 'index')->name('detail-lokasi.index');
        Route::get('/detail-lokasi', 'index')->name('detail-lokasi.index');
        Route::post('/detail-lokasi', 'store')->name('detail-lokasi.store');
        Route::put('/detail-lokasi', 'update')->name('detail-lokasi.update');
        Route::delete('/detail-lokasi', 'destroy')->name('detail-lokasi.delete');
    });

    Route::controller(RelasiAreaController::class)->group(function () {
        Route::get('/area', 'index')->name('area.index');
        Route::post('/area', 'store')->name('area.store');
        Route::get('/area/{uuid}/edit', 'edit')->name('area.edit');
        Route::put('/area', 'update')->name('area.update');
        Route::delete('/area', 'destroy')->name('area.delete');
    });

    Route::controller(DirektoratController::class)->group(function () {
        Route::get('/direktorat', 'index')->name('direktorat.index');
        Route::post('/direktorat', 'store')->name('direktorat.store');
        Route::put('/direktorat', 'update')->name('direktorat.update');
        Route::delete('/direktorat', 'destroy')->name('direktorat.delete');
    });

    Route::controller(DivisiController::class)->group(function () {
        Route::get('/divisi', 'index')->name('divisi.index');
        Route::post('/divisi', 'store')->name('divisi.store');
        Route::put('/divisi', 'update')->name('divisi.update');
        Route::delete('/divisi', 'destroy')->name('divisi.delete');
    });

    Route::controller(DepartemenController::class)->group(function () {
        Route::get('/departemen', 'index')->name('departemen.index');
        Route::post('/departemen', 'store')->name('departemen.store');
        Route::put('/departemen', 'update')->name('departemen.update');
        Route::delete('/departemen', 'destroy')->name('departemen.delete');
    });

    Route::controller(SeksiController::class)->group(function () {
        Route::get('/seksi', 'index')->name('seksi.index');
        Route::post('/seksi', 'store')->name('seksi.store');
        Route::put('/seksi', 'update')->name('seksi.update');
        Route::delete('/seksi', 'destroy')->name('seksi.delete');
    });

    Route::controller(RelasiStrukturController::class)->group(function () {
        Route::get('/struktur', 'index')->name('struktur.index');
        Route::post('/struktur', 'store')->name('struktur.store');
        Route::get('/struktur/{uuid}/edit', 'edit')->name('struktur.edit');
        Route::put('/struktur', 'update')->name('struktur.update');
        Route::delete('/struktur', 'destroy')->name('struktur.delete');
    });

    Route::controller(TipeBarangController::class)->group(function () {
        Route::get('/tipe-barang', 'index')->name('tipe-barang.index');
        Route::post('/tipe-barang', 'store')->name('tipe-barang.store');
        Route::put('/tipe-barang', 'update')->name('tipe-barang.update');
        Route::delete('/tipe-barang', 'destroy')->name('tipe-barang.delete');
    });

    Route::controller(TipePekerjaanController::class)->group(function () {
        Route::get('/tipe-pekerjaan', 'index')->name('tipe-pekerjaan.index');
        Route::post('/tipe-pekerjaan', 'store')->name('tipe-pekerjaan.store');
        Route::put('/tipe-pekerjaan', 'update')->name('tipe-pekerjaan.update');
        Route::delete('/tipe-pekerjaan', 'destroy')->name('tipe-pekerjaan.delete');
    });

    Route::controller(TipePermitController::class)->group(function () {
        Route::get('/tipe-permit', 'index')->name('tipe-permit.index');
        Route::post('/tipe-permit', 'store')->name('tipe-permit.store');
        Route::put('/tipe-permit', 'update')->name('tipe-permit.update');
        Route::delete('/tipe-permit', 'destroy')->name('tipe-permit.delete');
    });

    Route::controller(TipeEquipmentController::class)->group(function () {
        Route::get('/tipe-equipment', 'index')->name('tipe-equipment.index');
        Route::post('/tipe-equipment', 'store')->name('tipe-equipment.store');
        Route::put('/tipe-equipment', 'update')->name('tipe-equipment.update');
        Route::delete('/tipe-equipment', 'destroy')->name('tipe-equipment.delete');
    });

    Route::controller(SatuanController::class)->group(function () {
        Route::get('/satuan', 'index')->name('satuan.index');
        Route::post('/satuan', 'store')->name('satuan.store');
        Route::put('/satuan', 'update')->name('satuan.update');
        Route::delete('/satuan', 'destroy')->name('satuan.delete');
    });

    Route::controller(JabatanController::class)->group(function () {
        Route::get('/jabatan', 'index')->name('jabatan.index');
        Route::post('/jabatan', 'store')->name('jabatan.store');
        Route::put('/jabatan', 'update')->name('jabatan.update');
        Route::delete('/jabatan', 'destroy')->name('jabatan.delete');
    });

    Route::controller(RoleController::class)->group(function () {
        Route::get('/role', 'index')->name('role.index');
        Route::post('/role', 'store')->name('role.store');
        Route::put('/role', 'update')->name('role.update');
        Route::delete('/role', 'destroy')->name('role.delete');
    });

    Route::controller(ArahController::class)->group(function () {
        Route::get('/arah', 'index')->name('arah.index');
        Route::post('/arah', 'store')->name('arah.store');
        Route::put('/arah', 'update')->name('arah.update');
        Route::delete('/arah', 'destroy')->name('arah.delete');
    });

    Route::controller(BarangController::class)->group(function () {
        Route::get('/barang', 'index')->name('barang.index');
        Route::post('/barang', 'store')->name('barang.store');
        Route::get('/barang/{uuid}/edit', 'edit')->name('barang.edit');
        Route::put('/barang', 'update')->name('barang.update');
        Route::delete('/barang', 'destroy')->name('barang.delete');
    });

    Route::controller(EquipmentController::class)->group(function () {
        Route::get('/equipment', 'index')->name('equipment.index');
    });


    Route::controller(MonitoringPermitController::class)->group(function () {
        Route::get('/monitoring-permit', 'index')->name('monitoring-permit.index');
        Route::post('/monitoring-permit', 'store')->name('monitoring-permit.store');
        Route::get('/monitoring-permit/{uuid}/edit', 'edit')->name('monitoring-permit.edit');
        Route::put('/monitoring-permit', 'update')->name('monitoring-permit.update');
        Route::delete('/monitoring-permit', 'destroy')->name('monitoring-permit.delete');
    });

});
