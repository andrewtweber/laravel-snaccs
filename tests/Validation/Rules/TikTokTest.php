<?php

namespace Snaccs\Tests\Validation\Rules;

use Snaccs\Tests\LaravelTestCase;
use Snaccs\Validation\Rules\TikTok;

/**
 * Class TikTokTest
 *
 * @package Snaccs\Tests\Validation\Rules
 */
class TikTokTest extends LaravelTestCase
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
     *           ["tiktok.com/ferretpapa"]
     *           ["tiktok.com/ferretpapa/"]
     *           ["tiktok.com/@ferretpapa"]
     *           ["https://tiktok.com/ferretpapa"]
     *           ["ab"]
     *           ["nottoolong12345678901234"]
     *           ["@nottoolong12345678901234"]
     */
    public function passes(?string $value)
    {
        $rule = new TikTok();

        $this->assertTrue($rule->passes('tiktok', $value));
    }

    /**
     * @test
     *
     * @param string|null $value
     *
     * @testWith ["@"]
     *           [" "]
     *           ["/ferretpapa"]
     *           ["illegal chars"]
     *           ["illegal+chars"]
     *           ["a"]
     *           ["toolong890123456789012345"]
     *           ["instagram.com/ferretpapa"]
     *           ["https://instagram.com/ferretpapa"]
     */
    public function fails(?string $value)
    {
        $rule = new TikTok();

        $this->assertFalse($rule->passes('tiktok', $value));
    }
}
