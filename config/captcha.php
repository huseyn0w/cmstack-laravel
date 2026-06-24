<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Captcha Enabled
    |--------------------------------------------------------------------------
    |
    | When disabled, the captcha service renders nothing and all captcha
    | validation passes automatically. This keeps local dev, Docker and the
    | test environment free of any captcha requirement.
    |
    | If CAPTCHA_ENABLED is not set explicitly, captcha is considered enabled
    | only when a secret key is present (i.e. configured for production).
    |
    */

    'enabled' => env('CAPTCHA_ENABLED', ! empty(env('RECAPTCHA_SECRET_KEY'))),

    /*
    |--------------------------------------------------------------------------
    | reCAPTCHA Keys
    |--------------------------------------------------------------------------
    |
    | Obtain these from https://www.google.com/recaptcha/admin
    |
    */

    'site_key' => env('RECAPTCHA_SITE_KEY'),

    'secret_key' => env('RECAPTCHA_SECRET_KEY'),

    /*
    |--------------------------------------------------------------------------
    | reCAPTCHA Version
    |--------------------------------------------------------------------------
    |
    | Supported: "v3" (score based, invisible) and "v2" (checkbox).
    |
    */

    'version' => env('RECAPTCHA_VERSION', 'v3'),

    /*
    |--------------------------------------------------------------------------
    | reCAPTCHA v3 Score Threshold
    |--------------------------------------------------------------------------
    |
    | Minimum score (0.0 - 1.0) required for a v3 response to be accepted.
    |
    */

    'score_threshold' => (float) env('RECAPTCHA_SCORE_THRESHOLD', 0.5),

    /*
    |--------------------------------------------------------------------------
    | reCAPTCHA v3 Action
    |--------------------------------------------------------------------------
    */

    'action' => env('RECAPTCHA_ACTION', 'submit'),

];
