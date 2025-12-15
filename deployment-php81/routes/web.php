<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\DashboardController;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect('/admin/dashboard');
});

// Admin routes
Route::prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        // Auto-login admin user if not authenticated
        if (!Auth::check()) {
            $admin = User::where('email', env('ADMIN_EMAIL', 'admin@company.com'))->first();
            if ($admin) {
                Auth::login($admin);
            }
        }
        return app(DashboardController::class)->index();
    })->name('admin.dashboard');
});
