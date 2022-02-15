<?php

namespace Shanerutter\LaravelAdminEmailTwoFactor\Rules;

use Illuminate\Contracts\Validation\Rule;
use ReCaptcha\ReCaptcha;
use Shanerutter\LaravelAdminEmailTwoFactor\AuthEmailTwoFactor;

class ReCaptchaRule implements Rule
{

    private string $errorMsg = '';

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Make sure not empty
        if (empty($value)) {
            $this->errorMsg = ':attribute field is required.';
            return false;
        }

        // Validate ReCaptcha
        $resp = (new ReCaptcha(AuthEmailTwoFactor::config('recaptchaPrivateKey', '')))
            ->setExpectedHostname(request()->getHost())
            ->setScoreThreshold(0.5)
            ->verify($value, request()->ip());

        // Was not successful
        if (!$resp->isSuccess()) {
            $this->errorMsg = 'ReCaptcha field is required.';
            return false;
        }

        // Failed validation
        if ($resp->getScore() < 0.5) {
            $this->errorMsg = 'Failed to validate captcha.';
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->errorMsg;
    }
}
