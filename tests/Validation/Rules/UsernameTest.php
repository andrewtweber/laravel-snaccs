<?php

namespace Snaccs\Tests\Validation\Rules;

use Illuminate\Support\Facades\Config;
use Snaccs\Tests\LaravelTestCase;
use Snaccs\Validation\Rules\Username;

/**
 * Class UsernameTest
 *
 * @package Snaccs\Tests\Validation\Rules
 */
class UsernameTest extends LaravelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // @todo use sqllite or something to test uniqueness
        Config::set('system.usernames.table', null);
    }

    /**
     * @test
     */
    public function min_length_config_failure()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Invalid username min length 0");

        Config::set('system.usernames.min', 0);

        $rule = new Username();
        $rule->passes('username', 'test');
    }

    /**
     * @test
     */
    public function length_bounds_config_failure()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Invalid username length bounds 10-9");

        Config::set('system.usernames.min', 10);
        Config::set('system.usernames.max', 9);

        $rule = new Username();
        $rule->passes('username', 'test');
    }

    /**
     * @test
     *
     * @param string|null $username
     *
     * @testWith [null]
     *           [""]
     *           ["ab"]
     *           ["test"]
     *           ["TEST"]
     *           ["te_st"]
     *           ["te-st"]
     *           [".test."]
     *           ["1234"]
     *           ["nottoolong123456789012345"]
     */
    public function passes(?string $username)
    {
        Config::set('system.usernames.min', 2);

        $rule = new Username();

        $this->assertTrue($rule->passes('username', $username));
    }

    /**
     * @test
     *
     * @param string|null $username
     *
     * @testWith ["a"]
     *           ["__"]
     *           ["_._"]
     *           [" test "]
     *           ["te,st"]
     *           ["te st"]
     *           ["admin"]
     *           ["café"]
     *           ["toolong8901234567890123456"]
     */
    public function fails(?string $username)
    {
        Config::set('system.usernames.min', 2);

        $rule = new Username();

        $this->assertFalse($rule->passes('username', $username));
    }

    /**
     * @test
     *
     * @param string|null $username
     * @param bool        $expected
     *
     * @testWith [null,                    true]
     *           ["",                      true]
     *           ["test",                  true]
     *           ["1234",                  true]
     *           ["te[st",                 true]
     *           [" test ",                true]
     *           ["te st",                 true]
     *           ["café",                  false]
     *           ["{test!}",               true]
     *           ["a[](){}_-/*+?!&|^$.\\", true]
     *           ["te%st",                 false]
     *           ["@test",                 false]
     *           ["___",                   false]
     *           ["admin",                 false]
     */
    public function special_char_config(?string $username, bool $expected)
    {
        Config::set('system.usernames.allowed_special_chars', "[] (){}_-/*+?!&|^$.\\");

        $rule = new Username();

        $this->assertSame($expected, $rule->passes('username', $username));
    }

    /**
     */
    public function message()
    {
    }
}
