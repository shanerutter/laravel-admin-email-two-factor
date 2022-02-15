<?php

use Shanerutter\LaravelAdminEmailTwoFactor\Http\Controllers\AuthController;

Route::get('auth/2fa', [AuthController::class, 'getTwoFactor'])->name('admin.auth.2fa.email')->withoutMiddleware('admin.auth.2fa.email');
Route::post('auth/2fa', [AuthController::class, 'postTwoFactor'])->name('admin.auth.2fa.email')->withoutMiddleware('admin.auth.2fa.email');
Route::get('auth/2fa/resend', [AuthController::class, 'getTwoFactorResend'])->name('admin.auth.2fa.email.resend')->withoutMiddleware('admin.auth.2fa.email');
