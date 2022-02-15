<?php

namespace Shanerutter\LaravelAdminEmailTwoFactor;

use Encore\Admin\Extension;

class AuthEmailTwoFactor extends Extension
{
    public static string $group = 'auth-email-two-factor';
    public $name = 'auth-email-two-factor';
    public $views = __DIR__ . '/../resources/views';
}
