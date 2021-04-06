<?php

namespace Snaccs\Tests\Validation\Rules;

use Snaccs\Tests\TestCase;
use Snaccs\Validation\Rules\Instagram;

/**
 * Class InstagramTest
 *
 * @package Snaccs\Tests\Validation\Rules
 */
class InstagramTest extends TestCase
{
    /**
     * @test
     *
     * @param string|null $value
     *
     * @testWith [null]
     *           [""]
     *           [" ferretpapa "]
     *           ["ferretpapa"]
     *           ["_legal."]
     *           [".legal_"]
     *           ["_legal_"]
     *           [".legal."]
     *           ["@ferretpapa"]
     *           [" @ ferretpapa "]
     *           ["instagram.com/ferretpapa"]
     *           ["instagram.com/ferretpapa/"]
     *           ["instagram.com/@ferretpapa"]
     *           ["https://instagram.com/ferretpapa"]
     *           ["nottoolong12345678901234567890"]
     *           ["@nottoolong12345678901234567890"]
     */
    public function passes(?string $value)
    {
        $rule = new Instagram();

        $this->assertTrue($rule->passes('instagram', $value));
    }

    /**
     * @test
     *
     * @param string|null $value
     *
     * @testWith ["@"]
     *           [" "]
     *           ["illegal chars"]
     *           ["illegal+chars"]
     *           ["toolong890123456789012345678901"]
     */
    public function fails(?string $value)
    {
        $rule = new Instagram();

        $this->assertFalse($rule->passes('instagram', $value));
    }
}
