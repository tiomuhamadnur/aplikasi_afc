<?php

namespace App\Providers;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Yajra\DataTables\Html\Builder;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Builder::useVite();

        Blade::directive('currency', function ( $expression ) { return "Rp. <?php echo number_format($expression,0,',','.'); ?>"; });

        Blade::if('secretUser', function () {
            $ids = explode(',', env('SECRET_USER_IDS'));
            return in_array(auth()->id(), $ids);
        });

        Blade::if('notUser', function () {
            return auth()->user()->role->id != 3;
        });

        Blade::if('superAdminAndOrganik', function () {
            return auth()->user()->role->id == 1 && auth()->user()->tipe_employee->id == 1;
        });
    }
}
