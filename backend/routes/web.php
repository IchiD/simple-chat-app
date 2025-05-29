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
    
    // User Management Routes
    Route::get('users/{id}', [AdminDashboardController::class, 'showUser'])->name('users.show');
    Route::get('users/{id}/edit', [AdminDashboardController::class, 'editUser'])->name('users.edit');
    Route::put('users/{id}', [AdminDashboardController::class, 'updateUser'])->name('users.update');
    Route::delete('users/{id}', [AdminDashboardController::class, 'deleteUser'])->name('users.delete');
    Route::post('users/{id}/restore', [AdminDashboardController::class, 'restoreUser'])->name('users.restore');
    
    // User Conversations Management
    Route::get('users/{id}/conversations', [AdminDashboardController::class, 'userConversations'])->name('users.conversations');
    Route::get('users/{userId}/conversations/{conversationId}', [AdminDashboardController::class, 'conversationDetail'])->name('users.conversations.detail');
    Route::delete('users/{userId}/conversations/{conversationId}', [AdminDashboardController::class, 'deleteConversation'])->name('users.conversations.delete');
    Route::post('users/{userId}/conversations/{conversationId}/restore', [AdminDashboardController::class, 'restoreConversation'])->name('users.conversations.restore');
    
    // Message Management
    Route::put('users/{userId}/conversations/{conversationId}/messages/{messageId}', [AdminDashboardController::class, 'updateMessage'])->name('users.messages.update');
    Route::delete('users/{userId}/conversations/{conversationId}/messages/{messageId}', [AdminDashboardController::class, 'deleteMessage'])->name('users.messages.delete');

    // Friendship Management Routes
    Route::get('friendships', [AdminDashboardController::class, 'friendships'])->name('friendships');
    Route::get('friendships/{id}', [AdminDashboardController::class, 'showFriendship'])->name('friendships.show');
    Route::delete('friendships/{id}', [AdminDashboardController::class, 'deleteFriendship'])->name('friendships.delete');
    Route::post('friendships/{id}/restore', [AdminDashboardController::class, 'restoreFriendship'])->name('friendships.restore');

    // Super Admin Only Routes
    Route::get('admins', [AdminDashboardController::class, 'admins'])->name('admins');
    Route::post('admins', [AdminDashboardController::class, 'createAdmin'])->name('admins.create')->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class]);
  });
});
