<?php

namespace Snaccs\Tests\Validation\Rules;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;
use Snaccs\Tests\LaravelTestCase;
use Snaccs\Validation\Rules\VerifyPassword;

/**
 * Class VerifyPasswordTest
 *
 * @package Snaccs\Tests\Validation\Rules
 */
class VerifyPasswordTest extends LaravelTestCase
{
    /**
     * @test
     */
    public function validate()
    {
        $user = new class implements Authenticatable {
            public string $password;
            use \Illuminate\Auth\Authenticatable;
        };

        $user->password = Hash::make("testing");

        $rule = new VerifyPassword($user);

        $this->assertTrue($rule->passes('password', 'testing'));
        $this->assertFalse($rule->passes('password', ''));
        $this->assertFalse($rule->passes('password', null));
        $this->assertFalse($rule->passes('password', 'incorrect'));
        $this->assertSame('The :attribute field is incorrect.', $rule->message());
    }
}
