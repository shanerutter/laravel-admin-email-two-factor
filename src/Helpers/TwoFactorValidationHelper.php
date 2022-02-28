<?php

namespace Shanerutter\LaravelAdminEmailTwoFactor\Helpers;

use Encore\Admin\Auth\Database\Administrator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Shanerutter\LaravelAdminEmailTwoFactor\AuthEmailTwoFactor;
use Shanerutter\LaravelAdminEmailTwoFactor\Mail\TwoFactorCode;

class TwoFactorValidationHelper
{

    public static function twoFactorCompleted(Administrator $admin)
    {
        // Get session data
        $fa = Session::get('2fa');

        // Remember me cookie
        if (self::twoFactorCheckCookies()) {
            Session::put('2fa', ['completed' => true]);
            return true;
        }

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
        $code = rand(pow(10, AuthEmailTwoFactor::config('pinLength')-1), pow(10, AuthEmailTwoFactor::config('pinLength'))-1);;

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
            self::twoFactorSetCookies();

            return true;
        }

        return false;
    }







    private static function twoFactorCheckCookies(): bool
    {
        // Get cookie contents
        $contents = Cookie::get(self::twoFactorCookieName(), null);
        if (empty($contents) || $contents != self::twoFactorCookieContents()) {
            return false;
        }

        return true;
    }

    private static function twoFactorSetCookies(): void
    {
        Cookie::queue(cookie(self::twoFactorCookieName(), self::twoFactorCookieContents(), 1440 * AuthEmailTwoFactor::config('rememberDays')));
    }

    private static function twoFactorCookieName(): string
    {
        return '2fa-remember-admin-' . auth('admin')->id();
    }

    private static function twoFactorCookieContents(): string
    {
        $string = 'uE7ZJpjchdb5JINsniMpJbmlLrpp3rGzEFmh6NPAOgHvfqaIBBPPtoS6'; // Change to force everyone to complete 2fa again
        $string .= '_admin_';
        $string .= '_' . auth('admin')->id() . '_';
        $string .= '_' . md5(request()->userAgent()) . '_';
        return $string;
    }











}