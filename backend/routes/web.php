<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;

Route::get('/', function () {
  return view('welcome');
});

// IP確認用（デバッグ）
Route::get('/check-ip', function (Illuminate\Http\Request $request) {
  return response()->json([
    'ip' => $request->ip(),
    'server_addr' => $_SERVER['SERVER_ADDR'] ?? 'unknown',
    'remote_addr' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
    'http_x_forwarded_for' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? 'unknown',
    'user_agent' => $request->userAgent(),
  ]);
});

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
  // Admin Authentication Routes
  Route::get('login', [AdminAuthController::class, 'showLoginForm'])->name('login');
  Route::post('login', [AdminAuthController::class, 'login']);
  Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');

  // Admin Protected Routes
  Route::middleware(['admin'])->group(function () {
    Route::get('dashboard', [AdminDashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('users', [AdminDashboardController::class, 'users'])->name('users');

    // Super Admin Only Routes
    Route::get('admins', [AdminDashboardController::class, 'admins'])->name('admins');
    Route::post('admins', [AdminDashboardController::class, 'createAdmin'])->name('admins.create')->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class]);
  });
});
