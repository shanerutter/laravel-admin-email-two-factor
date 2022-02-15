<?php

namespace Shanerutter\LaravelAdminEmailTwoFactor\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Encore\Admin\Controllers\AuthController as BaseAuthController;
use Shanerutter\LaravelAdminEmailTwoFactor\AuthEmailTwoFactor;
use Shanerutter\LaravelAdminEmailTwoFactor\Helpers\TwoFactorValidationHelper;

class AuthController extends BaseAuthController
{

    private function permittedToView()
    {
        if (!$this->guard()->check() || !TwoFactorValidationHelper::twoFactorPendingCodeValidation(request()->user('admin'))) {
            abort(redirect(admin_url()));
        }
    }

    public function getTwoFactor()
    {
        $this->permittedToView();
        return view(AuthEmailTwoFactor::$group . '::2fa');
    }

    public function postTwoFactor(Request $request): \Illuminate\Http\RedirectResponse
    {
        $this->permittedToView();

        $request->validate([
            'code' => 'integer|required',
        ]);

        if(TwoFactorValidationHelper::twoFactorValidateCode(auth('admin')->user(), (int)$request->get('code')))
        {
            return redirect(admin_url());
        }

        return redirect()->back()->withErrors(['code' => 'Incorrect code entered']);
    }

    public function getTwoFactorResend(): \Illuminate\Http\RedirectResponse
    {
        Session::remove('2fa');
        TwoFactorValidationHelper::twoFactorCompleted(auth('admin')->user());
        return redirect()->route(admin_get_route('auth.2fa.email'))->with('msgSuccess', 'New code has been emailed');
    }

}
