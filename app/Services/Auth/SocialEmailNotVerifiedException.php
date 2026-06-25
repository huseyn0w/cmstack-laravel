<?php

namespace App\Services\Auth;

use RuntimeException;

/**
 * Thrown when a social profile would be linked to an existing account by a
 * shared email, but the provider has not vouched that the social account
 * actually owns that email. Refusing the link prevents a pre-auth account
 * takeover via an attacker-controlled, unverified provider email.
 */
class SocialEmailNotVerifiedException extends RuntimeException {}
