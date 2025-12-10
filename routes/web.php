<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VerificationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', fn() => view('auth.login'))->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/2fa/setup', [AuthController::class, 'show2faSetup'])->name('2fa.setup');
Route::post('/2fa/setup', [AuthController::class, 'post2faSetup']);
Route::get('/2fa', [AuthController::class, 'show2faVerify'])->name('2fa.verify');
Route::post('/2fa', [AuthController::class, 'post2faVerify']);
Route::post('/2fa/reset', [AuthController::class, 'reset2fa'])->name('2fa.reset');

Route::get('/register', fn() => view('auth.register'))->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::get('/forgot_password', fn() => view('auth.forgot_password'))->name('forgot_password');
Route::post('/forgot_password', [AuthController::class, 'forgot_password']);

Route::get('/reset_password/{token}', [AuthController::class, 'reset_password_view'])->name('reset_password');
Route::post('/reset_password', [AuthController::class, 'reset_password']);

Route::get('/auth-google-redirect', [AuthController::class, 'google_redirect']);
Route::get('/auth-google-callback', [AuthController::class, 'google_callback']);

Route::group(['middleware' => ['auth', 'check_role:customer']], function () {
    Route::get('/verify', [VerificationController::class, 'index']);
    Route::post('/verify', [VerificationController::class, 'store']);
    Route::get('/verify/{unique_id}', [VerificationController::class, 'show']);
    Route::put('/verify/{unique_id}', [VerificationController::class, 'update']);
    Route::get('/verify/{unique_id}/resend', [VerificationController::class, 'resend']);
});

Route::group(['middleware' => ['auth', 'check_role:customer', 'check_status']], function () {
    Route::get('/customer', fn() => 'halaman customer');
});
Route::group(['middleware' => ['auth', 'check_role:admin,staff']], function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
});
Route::group(['middleware' => ['auth', 'check_role:admin,staff']], function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
});
Route::group(['middleware' => ['auth', 'check_role:admin']], function () {
    Route::get('/user', [DashboardController::class, 'users']);
});
Route::get('/logout', [AuthController::class, 'logout']);
Route::post('/ban_user/{userId}', [AuthController::class, 'ban_user'])->name('ban_user');
Route::get('/auto_ban', [AuthController::class, 'auto_ban_inactive_users'])->name('auto_ban');
Route::get('/auto_logout', [AuthController::class, 'auto_logout_inactive_users'])->name('auto_logout');