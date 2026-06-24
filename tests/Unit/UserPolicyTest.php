<?php

namespace Tests\Unit;

use App\Policies\UserPolicy;
use Tests\TestCase;

/**
 * Fix #10: the policy must not blow up when there is no authenticated user or
 * when the permissions JSON is null/invalid; it should simply deny.
 */
class UserPolicyTest extends TestCase
{
    public function test_policy_denies_safely_with_no_authenticated_user(): void
    {
        // No user is logged in, so the constructor must not throw.
        $policy = new UserPolicy;

        $this->assertFalse($policy->see_admin_panel());
        $this->assertFalse($policy->manage_users());
        $this->assertFalse($policy->manage_posts());
    }
}
