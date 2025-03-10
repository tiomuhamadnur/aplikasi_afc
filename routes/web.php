<?php

use App\Events\HelloEvent;
use App\Events\MessageSent;
use App\Http\Controllers\admin\ApprovalController;
use App\Http\Controllers\admin\ArahController;
use App\Http\Controllers\admin\AssetController;
use App\Http\Controllers\admin\BankController;
use App\Http\Controllers\admin\BarangController;
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\admin\CauseController;
use App\Http\Controllers\admin\ClassificationController;
use App\Http\Controllers\admin\DashboardController;
use App\Http\Controllers\admin\DepartemenController;
use App\Http\Controllers\admin\DetailLokasiController;
use App\Http\Controllers\admin\DirektoratController;
use App\Http\Controllers\admin\DivisiController;
use App\Http\Controllers\admin\EquipmentController;
use App\Http\Controllers\admin\FormController;
use App\Http\Controllers\admin\FunctionalLocationController;
use App\Http\Controllers\admin\FundController;
use App\Http\Controllers\admin\FundSourceController;
use App\Http\Controllers\admin\GenderController;
use App\Http\Controllers\admin\JabatanController;
use App\Http\Controllers\admin\LokasiController;
use App\Http\Controllers\admin\OptionFormController;
use App\Http\Controllers\admin\ParameterController;
use App\Http\Controllers\admin\PCRController;
use App\Http\Controllers\admin\PerusahaanController;
use App\Http\Controllers\admin\ProblemController;
use App\Http\Controllers\admin\RelasiAreaController;
use App\Http\Controllers\admin\RelasiStrukturController;
use App\Http\Controllers\admin\RemedyController;
use App\Http\Controllers\admin\RoleController;
use App\Http\Controllers\admin\SatuanController;
use App\Http\Controllers\admin\SeksiController;
use App\Http\Controllers\admin\StatusBudgetingController;
use App\Http\Controllers\admin\StatusController;
use App\Http\Controllers\admin\SubLokasiController;
use App\Http\Controllers\admin\TipeBarangController;
use App\Http\Controllers\admin\TipeEmployeeController;
use App\Http\Controllers\admin\TipeEquipmentController;
use App\Http\Controllers\admin\TipePekerjaanController;
use App\Http\Controllers\admin\TipePermitController;
use App\Http\Controllers\admin\TransWorkOrderBarangController;
use App\Http\Controllers\admin\TransWorkOrderEquipmentController;
use App\Http\Controllers\admin\TransWorkOrderFunctionalLocationController;
use App\Http\Controllers\admin\TransWorkOrderPhotoController;
use App\Http\Controllers\admin\TransWorkOrderTasklistController;
use App\Http\Controllers\admin\TransWorkOrderUserController;
use App\Http\Controllers\admin\UserController;
use App\Http\Controllers\admin\WorkOrderController;
use App\Http\Controllers\user\BudgetAbsorptionController;
use App\Http\Controllers\user\ChecksheetController;
use App\Http\Controllers\user\DashboardBudgetController;
use App\Http\Controllers\user\GangguanController;
use App\Http\Controllers\user\LCUChecklistController;
use App\Http\Controllers\user\LogAfcController;
use App\Http\Controllers\user\MonitoringEquipmentController;
use App\Http\Controllers\user\MonitoringPermitController;
use App\Http\Controllers\user\ProfileController;
use App\Http\Controllers\user\ProjectController;
use App\Http\Controllers\user\SamCardController;
use App\Http\Controllers\user\SamCardHistoryController;
use App\Http\Controllers\user\TransaksiBarangController;
use App\Http\Controllers\user\TransaksiTiketController;
use App\Http\Controllers\user\TransGangguanRemedyController;
use Illuminate\Http\Request;
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

Route::get('/home', function () {
    return redirect()->route('dashboard.index');
})->name('home');

Route::get('/', function () {
    return redirect()->route('login');
})->middleware('auth');

Route::get('/', function () {
    return redirect()->route('dashboard.index');
})->middleware('auth');

Route::get('/refresh-captcha', function () {
    return response()->json(['captcha' => captcha_src('math')]);
})->name('captcha.refresh');

Route::group(['middleware' => ['auth', 'checkBanned', 'CheckPassword']], function () {
    // ALL USER
    Route::controller(DashboardController::class)->group(function () {
        Route::get('/dashboard', 'index')->name('dashboard.index');
        Route::get('/dashboard/availability/month', 'availability_bulan')->name('dashboard.availability.bulan');
        Route::get('/dashboard/availability/station', 'availability_station')->name('dashboard.availability.station');
        Route::get('/dashboard/availability/equipment', 'availability_equipment')->name('dashboard.availability.equipment');
    });

    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'index')->name('profile.index');
        Route::put('/profile', 'update')->name('profile.update');
        Route::put('/profile/change-password', 'change_password')->name('profile.change_password');
    });

    Route::controller(TransaksiBarangController::class)->group(function () {
        Route::get('/transaksi-barang', 'index')->name('transaksi-barang.index');
        Route::post('/transaksi-barang', 'store')->name('transaksi-barang.store');
        Route::get('/transaksi-barang/{uuid}/edit', 'edit')->name('transaksi-barang.edit');
        Route::post('/transaksi-barang/import', 'import')->name('transaksi-barang.import');
        Route::put('/transaksi-barang', 'update')->name('transaksi-barang.update');
        Route::delete('/transaksi-barang', 'destroy')->name('transaksi-barang.delete');

        Route::get('/transaksi-barang/trend/monthly', 'trend_monthly')->name('transaksi-barang.trend.monthly');
    });

    Route::controller(GangguanController::class)->group(function () {
        Route::get('/gangguan', 'index')->name('gangguan.index');
        Route::get('/gangguan/create', 'create')->name('gangguan.create');
        Route::post('/gangguan', 'store')->name('gangguan.store');
        Route::get('/gangguan/{uuid}/edit', 'edit')->name('gangguan.edit');
        Route::get('/gangguan/{uuid}/show', 'show')->name('gangguan.show');
        Route::post('/gangguan/import', 'import')->name('gangguan.import');
        Route::put('/gangguan', 'update')->name('gangguan.update');
        Route::delete('/gangguan', 'destroy')->name('gangguan.delete');

        Route::get('/gangguan/filter', 'filter')->name('gangguan.filter');
        Route::get('/gangguan/trend/monthly', 'trend_monthly')->name('gangguan.trend.monthly');
    });

    Route::controller(TransGangguanRemedyController::class)->group(function () {
        Route::put('/trans-gangguan-remedy', 'update')->name('trans-gangguan-remedy.update');
        Route::delete('/trans-gangguan-remedy', 'destroy')->name('trans-gangguan-remedy.delete');
    });

    Route::controller(LCUChecklistController::class)->group(function () {
        Route::get('/lcu-checklist', 'index')->name('lcu-checklist.index');
        Route::post('/lcu-checklist', 'store')->name('lcu-checklist.store');
        Route::put('/lcu-checklist', 'update')->name('lcu-checklist.update');
        Route::delete('/lcu-checklist', 'destroy')->name('lcu-checklist.delete');
    });

    // ADMIN & SUPERADMIN
    Route::group(['middleware' => ['admin']], function () {
        Route::controller(WorkOrderController::class)->group(function () {
            Route::get('/work-order', 'index')->name('work-order.index');
            Route::get('/work-order/create', 'create')->name('work-order.create');
            Route::get('/work-order/create-from-gangguan/{uuid}', 'create_from_gangguan')->name('work-order.create.from-gangguan');

            Route::post('/work-order', 'store')->name('work-order.store');
            Route::post('/work-order/{uuid}/store-from-gangguan', 'store_from_gangguan')->name('work-order.store.from-gangguan');
            Route::get('/work-order/{uuid}/edit', 'edit')->name('work-order.edit');
            Route::get('/work-order/{uuid}/detail/work-order', 'detail')->name('work-order.detail');
            Route::get('/work-order/{uuid}/equipment', 'equipment')->name('work-order.equipment');
            Route::put('/work-order', 'update')->name('work-order.update');
            Route::put('/work-order/note/{uuid}', 'update_note')->name('work-order.note.update');
            Route::put('/work-order/time/{uuid}', 'update_time')->name('work-order.time.update');
            Route::put('/work-order/approve/{uuid}', 'approve')->name('work-order.approve');
            Route::put('/work-order/reject/{uuid}', 'reject')->name('work-order.reject');
            Route::put('/work-order/revise/{uuid}', 'revise')->name('work-order.revise');
            Route::delete('/work-order', 'destroy')->name('work-order.delete');

            Route::get('/work-order/export/{uuid}/pdf', 'pdf')->name('work-order.export.pdf');
        });

        Route::controller(TransWorkOrderEquipmentController::class)->group(function () {
            Route::post('/trans-workorder-equipment/{uuid_workorder}', 'store')->name('trans-workorder-equipment.store');
            Route::delete('/trans-workorder-equipment', 'destroy')->name('trans-workorder-equipment.delete');
        });

        Route::controller(TransWorkOrderTasklistController::class)->group(function () {
            Route::post('/trans-workorder-tasklist/{uuid_workorder}', 'store')->name('trans-workorder-tasklist.store');
            Route::put('/trans-workorder-tasklist', 'update')->name('trans-workorder-tasklist.update');
            Route::put('/trans-workorder-tasklist/actual-duration', 'update_actual_duration')->name('trans-workorder-tasklist.update-actual-duration');
            Route::delete('/trans-workorder-tasklist', 'destroy')->name('trans-workorder-tasklist.delete');
        });

        Route::controller(TransWorkOrderBarangController::class)->group(function () {
            Route::post('/trans-workorder-barang/{uuid_workorder}', 'store')->name('trans-workorder-barang.store');
            Route::delete('/trans-workorder-barang', 'destroy')->name('trans-workorder-barang.delete');
        });

        Route::controller(TransWorkOrderUserController::class)->group(function () {
            Route::post('/trans-workorder-user/{uuid_workorder}', 'store')->name('trans-workorder-user.store');
            Route::delete('/trans-workorder-user', 'destroy')->name('trans-workorder-user.delete');
        });

        Route::controller(TransWorkOrderPhotoController::class)->group(function () {
            Route::post('/trans-workorder-photo/{uuid_workorder}', 'store')->name('trans-workorder-photo.store');
            Route::delete('/trans-workorder-photo', 'destroy')->name('trans-workorder-photo.delete');
        });

        Route::controller(TransWorkOrderFunctionalLocationController::class)->group(function () {
            Route::post('/trans-workorder-functional-location/{uuid_workorder}', 'store')->name('trans-workorder-functional-location.store');
            Route::delete('/trans-workorder-functional-location', 'destroy')->name('trans-workorder-functional-location.delete');
        });

        Route::controller(FundController::class)->group(function () {
            Route::get('/fund', 'index')->name('fund.index');
            Route::post('/fund', 'store')->name('fund.store');
            Route::put('/fund', 'update')->name('fund.update');
            Route::delete('/fund', 'destroy')->name('fund.delete');
        });

        Route::controller(FundSourceController::class)->group(function () {
            Route::get('/fund-source', 'index')->name('fund-source.index');
            Route::post('/fund-source', 'store')->name('fund-source.store');
            Route::get('/fund-source/{uuid}/edit', 'edit')->name('fund-source.edit');
            Route::put('/fund-source', 'update')->name('fund-source.update');
            Route::delete('/fund-source', 'destroy')->name('fund-source.delete');
        });

        Route::controller(ProjectController::class)->group(function () {
            Route::get('/project', 'index')->name('project.index');
            Route::post('/project', 'store')->name('project.store');
            Route::get('/project/{uuid}/edit', 'edit')->name('project.edit');
            Route::get('/project/{uuid}/show', 'show')->name('project.show');
            Route::put('/project', 'update')->name('project.update');
            Route::delete('/project', 'destroy')->name('project.delete');
        });

        Route::controller(BudgetAbsorptionController::class)->group(function () {
            Route::get('/budget-absorption', 'index')->name('budget-absorption.index');
            Route::get('/budget-absorption/project/{uuid}/show', 'show')->name('budget-absorption.by_project.show');
            Route::post('/budget-absorption', 'store')->name('budget-absorption.store');
            Route::get('/budget-absorption/{uuid}/edit', 'edit')->name('budget-absorption.edit');
            Route::put('/budget-absorption', 'update')->name('budget-absorption.update');
            Route::delete('/budget-absorption', 'destroy')->name('budget-absorption.delete');
        });

        Route::controller(DashboardBudgetController::class)->group(function () {
            Route::get('/monitoring-budget', 'index')->name('dashboard-budget.index');
            Route::get('/monitoring-budget/department', 'departemen')->name('dashboard-budget.departemen');
        });

        Route::controller(MonitoringPermitController::class)->group(function () {
            Route::get('/monitoring-permit', 'index')->name('monitoring-permit.index');
            Route::post('/monitoring-permit', 'store')->name('monitoring-permit.store');
            Route::get('/monitoring-permit/{uuid}/edit', 'edit')->name('monitoring-permit.edit');
            Route::put('/monitoring-permit', 'update')->name('monitoring-permit.update');
            Route::delete('/monitoring-permit', 'destroy')->name('monitoring-permit.delete');
        });

        Route::controller(SamCardController::class)->group(function () {
            Route::get('/sam-card', 'index')->name('sam-card.index');
            Route::post('/sam-card', 'store')->name('sam-card.store');
            Route::post('/sam-card/merry-code', 'merry_code')->name('sam-card.merry-code.store');
            Route::get('/sam-card/{uuid}/edit', 'edit')->name('sam-card.edit');
            Route::post('/sam-card/import', 'import')->name('sam-card.import');
            Route::put('/sam-card', 'update')->name('sam-card.update');
            Route::delete('/sam-card', 'destroy')->name('sam-card.delete');
        });

        Route::controller(SamCardHistoryController::class)->group(function () {
            Route::get('/sam-history', 'index')->name('sam-history.index');
            Route::post('/sam-history', 'store')->name('sam-history.store');
            Route::get('/sam-history/{uuid}/create', 'create')->name('sam-history.create');
        });
    });

    // SUPERADMIN & ORGANIK
    Route::group(['middleware' => ['admin', 'organik']], function () {
        Route::controller(LokasiController::class)->group(function () {
            Route::get('/lokasi', 'index')->name('lokasi.index');
            Route::post('/lokasi', 'store')->name('lokasi.store');
            Route::put('/lokasi', 'update')->name('lokasi.update');
            Route::delete('/lokasi', 'destroy')->name('lokasi.delete');
        });

        Route::controller(UserController::class)->group(function () {
            Route::get('/user', 'index')->name('user.index');
            Route::get('/banned-user', 'banned_user')->name('user.banned');
            Route::post('/user', 'store')->name('user.store');
            Route::get('/user/{uuid}/edit', 'edit')->name('user.edit');
            Route::put('/user', 'update')->name('user.update');
            Route::put('/user/change-password', 'change_password')->name('user.change_password');
            Route::delete('/user/ban', 'ban')->name('user.ban');
            Route::delete('/user/unban', 'unban')->name('user.unban');
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

        Route::controller(ApprovalController::class)->group(function () {
            Route::get('/approval', 'index')->name('approval.index');
            Route::post('/approval', 'store')->name('approval.store');
            Route::get('/approval/{uuid}/edit', 'edit')->name('approval.edit');
            Route::put('/approval', 'update')->name('approval.update');
            Route::delete('/approval', 'destroy')->name('approval.delete');
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

        Route::controller(TipeEmployeeController::class)->group(function () {
            Route::get('/tipe-employee', 'index')->name('tipe-employee.index');
            Route::post('/tipe-employee', 'store')->name('tipe-employee.store');
            Route::put('/tipe-employee', 'update')->name('tipe-employee.update');
            Route::delete('/tipe-employee', 'destroy')->name('tipe-employee.delete');
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

        Route::controller(GenderController::class)->group(function () {
            Route::get('/gender', 'index')->name('gender.index');
            Route::post('/gender', 'store')->name('gender.store');
            Route::put('/gender', 'update')->name('gender.update');
            Route::delete('/gender', 'destroy')->name('gender.delete');
        });

        Route::controller(StatusController::class)->group(function () {
            Route::get('/status', 'index')->name('status.index');
            Route::post('/status', 'store')->name('status.store');
            Route::put('/status', 'update')->name('status.update');
            Route::delete('/status', 'destroy')->name('status.delete');
        });

        Route::controller(StatusBudgetingController::class)->group(function () {
            Route::get('/status-budgeting', 'index')->name('status-budgeting.index');
            Route::post('/status-budgeting', 'store')->name('status-budgeting.store');
            Route::put('/status-budgeting', 'update')->name('status-budgeting.update');
            Route::delete('/status-budgeting', 'destroy')->name('status-budgeting.delete');
        });

        Route::controller(ClassificationController::class)->group(function () {
            Route::get('/classification', 'index')->name('classification.index');
            Route::post('/classification', 'store')->name('classification.store');
            Route::put('/classification', 'update')->name('classification.update');
            Route::delete('/classification', 'destroy')->name('classification.delete');
        });

        Route::controller(CategoryController::class)->group(function () {
            Route::get('/category', 'index')->name('category.index');
            Route::post('/category', 'store')->name('category.store');
            Route::put('/category', 'update')->name('category.update');
            Route::delete('/category', 'destroy')->name('category.delete');
        });

        Route::controller(ProblemController::class)->group(function () {
            Route::get('/problem', 'index')->name('problem.index');
            Route::post('/problem', 'store')->name('problem.store');
            Route::put('/problem', 'update')->name('problem.update');
            Route::post('/problem/import', 'import')->name('problem.import');
            Route::delete('/problem', 'destroy')->name('problem.delete');
        });

        Route::controller(CauseController::class)->group(function () {
            Route::get('/cause', 'index')->name('cause.index');
            Route::post('/cause', 'store')->name('cause.store');
            Route::put('/cause', 'update')->name('cause.update');
            Route::post('/cause/import', 'import')->name('cause.import');
            Route::delete('/cause', 'destroy')->name('cause.delete');
        });

        Route::controller(RemedyController::class)->group(function () {
            Route::get('/remedy', 'index')->name('remedy.index');
            Route::post('/remedy', 'store')->name('remedy.store');
            Route::put('/remedy', 'update')->name('remedy.update');
            Route::post('/remedy/import', 'import')->name('remedy.import');
            Route::delete('/remedy', 'destroy')->name('remedy.delete');
        });

        Route::controller(PCRController::class)->group(function () {
            Route::get('/pcr', 'index')->name('pcr.index');
            Route::post('/pcr', 'store')->name('pcr.store');
            Route::get('/pcr/{uuid}/edit', 'edit')->name('pcr.edit');
            Route::put('/pcr', 'update')->name('pcr.update');
            Route::post('/pcr/import', 'import')->name('pcr.import');
            Route::delete('/pcr', 'destroy')->name('pcr.delete');
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

        Route::controller(PerusahaanController::class)->group(function () {
            Route::get('/perusahaan', 'index')->name('perusahaan.index');
            Route::post('/perusahaan', 'store')->name('perusahaan.store');
            Route::put('/perusahaan', 'update')->name('perusahaan.update');
            Route::delete('/perusahaan', 'destroy')->name('perusahaan.delete');
        });

        Route::controller(OptionFormController::class)->group(function () {
            Route::get('/option-form', 'index')->name('option-form.index');
            Route::post('/option-form', 'store')->name('option-form.store');
            Route::put('/option-form', 'update')->name('option-form.update');
            Route::delete('/option-form', 'destroy')->name('option-form.delete');
        });

        Route::controller(FormController::class)->group(function () {
            Route::get('/form', 'index')->name('form.index');
            Route::post('/form', 'store')->name('form.store');
            Route::get('/form/{uuid}/edit', 'edit')->name('form.edit');
            Route::put('/form', 'update')->name('form.update');
            Route::delete('/form', 'destroy')->name('form.delete');
        });

        Route::controller(ParameterController::class)->group(function () {
            Route::get('/parameter/{uuid}', 'index')->name('parameter.index');
            Route::get('/parameter/{uuid}/create', 'create')->name('parameter.create');
            Route::post('/parameter', 'store')->name('parameter.store');
            Route::get('/parameter/{uuid}/edit', 'edit')->name('parameter.edit');
            Route::put('/parameter', 'update')->name('parameter.update');
            Route::delete('/parameter', 'destroy')->name('parameter.delete');
        });

        Route::controller(BarangController::class)->group(function () {
            Route::get('/barang', 'index')->name('barang.index');
            Route::post('/barang', 'store')->name('barang.store');
            Route::post('/barang/import', 'import')->name('barang.import');
            Route::get('/barang/{uuid}/edit', 'edit')->name('barang.edit');
            Route::put('/barang', 'update')->name('barang.update');
            Route::delete('/barang', 'destroy')->name('barang.delete');
        });

        Route::controller(EquipmentController::class)->group(function () {
            Route::get('/equipment', 'index')->name('equipment.index');
            Route::post('/equipment', 'store')->name('equipment.store');
            Route::post('/equipment/import', 'import')->name('equipment.import');
            Route::get('/equipment/{uuid}/edit', 'edit')->name('equipment.edit');
            Route::put('/equipment', 'update')->name('equipment.update');
            Route::delete('/equipment', 'destroy')->name('equipment.delete');
        });

        Route::controller(AssetController::class)->group(function () {
            Route::get('/asset', 'index')->name('asset.index');
        });

        Route::controller(FunctionalLocationController::class)->group(function () {
            Route::get('/fun-loc', 'index')->name('fun_loc.index');
            Route::post('/fun-loc', 'store')->name('fun_loc.store');
            Route::get('/fun-loc/{uuid}/edit', 'edit')->name('fun_loc.edit');
            Route::put('/fun-loc', 'update')->name('fun_loc.update');
        });

        Route::controller(LogAfcController::class)->group(function () {
            Route::get('/log-afc', 'index')->name('log.index');
            Route::post('/log-afc/import', 'import')->name('log.import');
            // Route::post('/log-afc/import-convert', 'import_convert')->name('convert.import');
            Route::get('/log-afc/export', 'export')->name('log.export');
        });

        Route::controller(TransaksiTiketController::class)->group(function () {
            Route::get('/transaksi-tiket/ftp', 'ftp')->name('transaksi.tiket.ftp');
            Route::get('/transaksi-tiket', 'index')->name('transaksi.tiket.index');
            Route::post('/transaksi-tiket/import', 'import')->name('transaksi.tiket.import');
        });

        Route::controller(MonitoringEquipmentController::class)->group(function () {
            Route::get('/monitoring-equipment', 'index')->name('monitoring-equipment.index');
            Route::delete('/monitoring-equipment', 'destroy')->name('monitoring-equipment.delete');
        });

        Route::controller(ChecksheetController::class)->group(function () {
            Route::get('/checksheet/create', 'create')->name('checksheet.create');
            Route::post('/checksheet', 'store')->name('checksheet.store');
            Route::get('/checksheet/history', 'history')->name('checksheet.history');
            Route::get('/checksheet/trend', 'trend')->name('checksheet.trend');
            Route::put('/checksheet/history', 'export_excel')->name('checksheet.history.export.excel');
        });
    });
});

Route::controller(MonitoringEquipmentController::class)->group(function () {
    Route::get('/monitoring-equipment/store', 'store')->name('monitoring-equipment.store');
    Route::get('/client-monitoring-equipment', 'client_index')->name('client.monitoring-equipment.index');
});

// Route::get('/send-message', function (Request $request) {
//     $message = $request->query('message'); // Ambil data dari query string
//     broadcast(new MessageSent($message))->toOthers();
//     return response()->json(['success' => true, 'message' => $message]);
// });

// Route::get('/send-event', function () {
//     $data = [
//         'name' => 'Tio Muhamad Nur',
//         'email' => 'tiomuhamadnur@gmail.com',
//         'phone' => '087723704469',
//     ];
//     broadcast(new HelloEvent($data));
// });

// Route::get('/chat', function () {
//     return view('welcome');
// });
