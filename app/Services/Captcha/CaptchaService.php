<?php

namespace App\Services\Captcha;

use Illuminate\Http\Request;
use ReCaptcha\ReCaptcha;

/**
 * Lightweight wrapper around google/recaptcha that preserves the legacy
 * app('captcha') interface used throughout the Blade views.
 *
 * When no secret key is configured (local dev, Docker, tests) the service
 * silently disables itself: render() returns an empty string and verify()
 * always returns true, so nothing breaks.
 */
class CaptchaService
{
    /**
     * The name of the hidden input / request field carrying the token.
     * Kept as "g-recaptcha-response" to match the existing validation rules.
     */
    public const FIELD = 'g-recaptcha-response';

    public function __construct(
        protected array $config
    ) {}

    /**
     * Whether captcha protection is active.
     */
    public function enabled(): bool
    {
        return (bool) ($this->config['enabled'] ?? false)
            && ! empty($this->config['site_key'])
            && ! empty($this->config['secret_key']);
    }

    /**
     * Render the captcha widget HTML (script + hidden input).
     *
     * Returns an empty string when disabled so views render cleanly.
     */
    public function render(): string
    {
        if (! $this->enabled()) {
            return '';
        }

        $siteKey = e($this->config['site_key']);
        $field = self::FIELD;
        $action = e($this->config['action'] ?? 'submit');

        if (($this->config['version'] ?? 'v3') === 'v2') {
            return <<<HTML
<div class="g-recaptcha" data-sitekey="{$siteKey}"></div>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
HTML;
        }

        // reCAPTCHA v3: invisible, token written into a hidden input named
        // g-recaptcha-response and refreshed periodically so it stays valid.
        return <<<HTML
<input type="hidden" name="{$field}" id="{$field}" value="">
<script src="https://www.google.com/recaptcha/api.js?render={$siteKey}"></script>
<script>
(function () {
    function refreshCaptchaToken() {
        if (typeof grecaptcha === 'undefined') { return; }
        grecaptcha.ready(function () {
            grecaptcha.execute('{$siteKey}', { action: '{$action}' }).then(function (token) {
                var field = document.getElementById('{$field}');
                if (field) { field.value = token; }
            });
        });
    }
    refreshCaptchaToken();
    // v3 tokens expire after ~2 minutes; refresh well before that.
    setInterval(refreshCaptchaToken, 90 * 1000);
})();
</script>
HTML;
    }

    /**
     * Verify a submitted captcha token server-side.
     *
     * Returns true when captcha is disabled so validation always passes.
     */
    public function verify(?string $token): bool
    {
        if (! $this->enabled()) {
            return true;
        }

        if (empty($token)) {
            return false;
        }

        $recaptcha = new ReCaptcha($this->config['secret_key']);

        if (($this->config['version'] ?? 'v3') === 'v3') {
            $recaptcha->setScoreThreshold((float) ($this->config['score_threshold'] ?? 0.5));

            if (! empty($this->config['action'])) {
                $recaptcha->setExpectedAction($this->config['action']);
            }
        }

        $response = $recaptcha->verify($token, $this->remoteIp());

        return $response->isSuccess();
    }

    /**
     * Resolve the remote IP from the current request, if available.
     */
    protected function remoteIp(): ?string
    {
        $request = app('request');

        return $request instanceof Request ? $request->ip() : null;
    }
}
