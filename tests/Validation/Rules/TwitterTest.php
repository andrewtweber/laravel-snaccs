<?php

namespace Snaccs\Tests\Validation\Rules;

use Snaccs\Tests\TestCase;
use Snaccs\Validation\Rules\Twitter;

/**
 * Class TwitterTest
 *
 * @package Snaccs\Tests\Validation\Rules
 */
class TwitterTest extends TestCase
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
     *           ["_legal_"]
     *           ["@ferretpapa"]
     *           [" @ ferretpapa "]
     *           ["twitter.com/ferretpapa"]
     *           ["twitter.com/ferretpapa/"]
     *           ["twitter.com/@ferretpapa"]
     *           ["twitter.com/#!ferretpapa"]
     *           ["https://twitter.com/ferretpapa"]
     *           ["nottoolong12345"]
     *           ["@nottoolong12345"]
     */
    public function passes(?string $value)
    {
        $rule = new Twitter();

        $this->assertTrue($rule->passes('twitter', $value));
    }

    /**
     * @test
     *
     * @param string|null $value
     *
     * @testWith ["@"]
     *           [" "]
     *           ["illegal.chars"]
     *           ["illegal chars"]
     *           ["illegal+chars"]
     *           ["toolong890123456"]
     */
    public function fails(?string $value)
    {
        $rule = new Twitter();

        $this->assertFalse($rule->passes('twitter', $value));
    }
}
