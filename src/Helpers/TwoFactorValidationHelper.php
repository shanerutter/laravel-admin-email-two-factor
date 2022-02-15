<?php

namespace Shanerutter\LaravelAdminEmailTwoFactor\Helpers;

use Encore\Admin\Auth\Database\Administrator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Shanerutter\LaravelAdminEmailTwoFactor\Mail\TwoFactorCode;

class TwoFactorValidationHelper
{

    public static function twoFactorCompleted(Administrator $admin)
    {
        // Get session data
        $fa = Session::get('2fa');

        // Data is missing
        if (empty($fa)) {
            self::twoFactorGenerateCode($admin);
            return false;
        }

        return $fa['completed'];
    }

    public function twoFactorPendingCodeValidation(Administrator $admin): bool
    {
        return !self::twoFactorCompleted($admin);
    }

    public static function twoFactorGenerateCode(Administrator $admin): int
    {
        $code = rand(100000, 999999);

        Session::put('2fa', [
            'completed' => false,
            'code' => $code,
            'requested_at' => now(),
            'id' => auth('admin')->user()->id,
            'expired_at' => now()->addMinutes(10),
        ]);

        if (!empty($admin->email)) {
            Mail::to($admin->email)->send(new TwoFactorCode($code));
        }

        return $code;
    }

    public static function twoFactorValidateCode(Administrator $admin, int $code)
    {
        if (!self::twoFactorPendingCodeValidation($admin)) {
            return false;
        }

        // Get 2fa data
        $fa = Session::get('2fa');

        // Code is valid
        if (!empty($fa['code']) && $code === $fa['code']) {
            // Check code has not expired
            if ($fa['expired_at']->lt(now())) {
                Session::remove('2fa');
                Auth::logout();
                return redirect(admin_url())->withErrors('Code has expired, please login again.');
            }

            Session::put('2fa', ['completed' => true]);
            return true;
        }

        return false;
    }

}